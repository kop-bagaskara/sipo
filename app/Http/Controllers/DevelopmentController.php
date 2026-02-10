<?php

namespace App\Http\Controllers;

use App\Models\JobDevelopment;
use App\Models\JobOrderDevelopment;
use App\Models\MasterDataPrepress;
use App\Models\JobDevelopmentProcess;
use App\Models\JobDevelopmentProcessHistory;
use App\Models\JobDevelopmentPurchasingItem;
use App\Models\JobDevelopmentQualityCheck;
use App\Models\JobDevelopmentApproval;
use App\Models\Divisi;
use App\Models\JobPrepress;
use App\Models\User;
use App\Models\MeetingOPP;
use App\Models\SchedulingDevelopment;
use App\Models\MapProof;
use App\Models\SalesOrder;
use App\Models\HandlingDevelopment;
use Carbon\Carbon;
use App\Models\JenisPekerjaanPrepress;
use App\Models\MasterProsesDevelopment;
use App\Models\MaterialPurchasing;
use App\Models\ProductionSchedule;
use App\Services\DevelopmentHandlingService;
use App\Services\SystemLogService;
use App\Services\MasterProsesService;
use App\Services\DevelopmentEmailNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevelopmentController extends Controller
{
    public function indexJobOrderDevelopment()
    {
        return view('main.process.development.data-job-development');
    }

    public function jobOrderDevelopmentData()
    {
        return view('main.process.development.data-job-order-development');
    }

    // Marketing Input - Form untuk input job development
    public function marketingInputForm()
    {
        return view('main.process.development.marketing-input-form');
    }

    // Development Input - Form untuk input job development (mirip prepress job order)
    public function developmentInputForm()
    {
        return view('main.process.development.development-input');
    }

    // Development Input - Form untuk view job development
    public function developmentInputView($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);
        $mode = 'view';
        return view('main.process.development.development-input', compact('job', 'mode'));
    }

    // Development Input - Form untuk edit job development
    public function developmentInputEdit($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if user can edit (only creator and status is OPEN)
        if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
            return redirect()->route('development.marketing-jobs.list')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit job ini.');
        }

        $mode = 'edit';
        return view('main.process.development.development-input', compact('job', 'mode'));
    }

    /**
     * Generate unique job code with format DEV-YYMMDD-XXX
     */
    private function generateJobCode()
    {
        $today = now()->format('ymd'); // Format: 250801 (25=year, 08=month, 01=day)

        // Find the last job created today to get the sequence number
        $lastJobToday = JobOrderDevelopment::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJobToday) {
            // Extract sequence number from existing job code
            $lastJobCode = $lastJobToday->job_code;
            if (preg_match('/DEV-' . $today . '-(\d{4})$/', $lastJobCode, $matches)) {
                $lastSequence = (int) $matches[1];
                $newSequence = $lastSequence + 1;
            } else {
                // If existing job code doesn't match format, start from 1
                $newSequence = 1;
            }
        } else {
            // First job of the day
            $newSequence = 1;
        }

        return 'DEV-' . $today . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if user can edit (only creator and status is OPEN)
        if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
            return redirect()->route('development.marketing-jobs.list')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit job ini.');
        }

        $jenisPekerjaan = \App\Models\JenisPekerjaanPrepress::all();

        return view('main.process.development.development-edit', compact('job', 'jenisPekerjaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if user can edit (only creator and status is OPEN)
        if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit job ini.'
            ], 403);
        }

        $request->validate([
            'job_name' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'job_deadline' => 'required|date|after:tanggal',
            'customer' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'kode_design' => 'nullable|string|max:255',
            'dimension' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'total_color' => 'required|integer|min:1',
            'colors' => 'nullable|array',
            'qty_order_estimation' => 'required|integer|min:1',
            'job_type' => 'required|in:new,repeat',
            'change_percentage' => 'nullable|integer|min:0|max:100',
            'change_details' => 'nullable|array',
            'job_order' => 'required|array|min:1',
            'file_data' => 'nullable|array',
            'prioritas_job' => 'required|in:low,medium,high,urgent',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'catatan' => 'nullable|string'
        ]);

        try {
            // Handle file uploads
            $attachmentPaths = $job->attachment_paths ?? [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/development'), $filename);
                    $attachmentPaths[] = 'uploads/development/' . $filename;
                }
            }

            // Update job data
            $job->update([
                'job_name' => $request->job_name,
                'tanggal' => $request->tanggal,
                'job_deadline' => $request->job_deadline,
                'customer' => $request->customer,
                'product' => $request->product,
                'kode_design' => $request->kode_design,
                'dimension' => $request->dimension,
                'material' => $request->material,
                'total_color' => $request->total_color,
                'colors' => $request->colors,
                'qty_order_estimation' => $request->qty_order_estimation,
                'job_type' => $request->job_type,
                'change_percentage' => $request->change_percentage,
                'change_details' => $request->change_details,
                'job_order' => $request->job_order,
                'file_data' => $request->file_data,
                'prioritas_job' => $request->prioritas_job,
                'attachment_paths' => $attachmentPaths,
                'catatan' => $request->catatan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil diperbarui!',
                'data' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $job = JobOrderDevelopment::with('marketingUser')->findOrFail($id);
        return view('main.process.development.development-view', compact('job'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if user can delete (only creator and status is OPEN)
        if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus job ini.'
            ], 403);
        }

        try {
            // Delete attachment files
            if ($job->attachment_paths) {
                foreach ($job->attachment_paths as $path) {
                    $filePath = public_path($path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Force delete the job
            $job->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function marketingInputStore(Request $request)
    {
        // dd($request->all());
        // Validate request
        $request->validate([
            'job_name' => 'required|string|max:255',
            'specification' => 'required|string',
            'type' => 'required|in:proof,trial_khusus',
            'priority' => 'required|in:high,medium,low',
            'customer_name' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            // Generate unique job code with format DEV-YYMMDD-XXX
            $jobCode = $this->generateJobCode();
            // dd($jobCode);

            // Log the generated job code for debugging
            Log::info('Generated job code: ' . $jobCode, [
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s')
            ]);

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('job_developments', 'public');
            }

            // Create job development
            $jobDevelopment = JobDevelopment::create([
                'job_code' => $jobCode,
                'job_name' => $request->job_name,
                'specification' => $request->specification,
                'attachment' => $attachmentPath,
                'type' => $request->type,
                'priority' => $request->priority,
                'customer_name' => $request->customer_name,
                'status' => 'draft',
                'marketing_user_id' => Auth::id(),
                'started_at' => now()
            ]);

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Job Development berhasil dibuat dengan kode: ' . $jobCode,
                'data' => $jobDevelopment
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating job development: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Development Input Store - Handle form submission dari development-input form
    public function developmentInputStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tanggal' => 'required|date',
            'customer' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'kode_design' => 'required|string|max:255',
            'dimension' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'total_color' => 'required|string|max:255',
            'qty_order_estimation' => 'required|string|max:255',
            'job_type' => 'required|in:new,repeat',
            'change_percentage' => 'nullable|integer|min:0|max:100',
            'change_details' => 'nullable|array',
            'job_order' => 'required|array|min:1',
            'job_order.*.jenis_pekerjaan' => 'required|string',
            'job_order.*.unit_job' => 'required|string',
            'file_data' => 'required|array|min:1',
            'proses' => 'required|array|min:1',
            'prioritas_job' => 'required|in:Urgent,Normal',
            'catatan' => 'nullable|string',
            'job_deadline' => 'nullable|date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            // Special materials validation
            'kertas_khusus' => 'nullable|boolean',
            'kertas_khusus_detail' => 'nullable|string|max:500',
            'tinta_khusus' => 'nullable|boolean',
            'tinta_khusus_detail' => 'nullable|string|max:500',
            'foil_khusus' => 'nullable|boolean',
            'foil_khusus_detail' => 'nullable|string|max:500',
            'pale_tooling_khusus' => 'nullable|boolean',
            'pale_tooling_khusus_detail' => 'nullable|string|max:500',
        ]);

        try {
            $jobCode = $this->generateJobCode();

            Log::info('Generated development job code: ' . $jobCode, [
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s'),
                'customer' => $request->customer,
                'product' => $request->product
            ]);

            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('development_attachments', $filename, 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Prepare job name from product and customer
            $jobName = $request->product . ' - ' . $request->customer;

            // Prepare colors array
            $colors = [];
            for ($i = 1; $i <= 10; $i++) {
                if ($request->has("color.{$i}") && !empty($request->input("color.{$i}"))) {
                    $colors[$i] = $request->input("color.{$i}");
                }
            }

            // Create job order development
            $jobOrderDevelopment = JobOrderDevelopment::create([
                'job_code' => $jobCode,
                'job_name' => $jobName,
                'tanggal' => $request->tanggal,
                'job_deadline' => $request->job_deadline,
                'customer' => $request->customer,
                'product' => $request->product,
                'kode_design' => $request->kode_design,
                'dimension' => $request->dimension,
                'material' => $request->material,
                'total_color' => $request->total_color,
                'colors' => $colors,
                'qty_order_estimation' => $request->qty_order_estimation,
                'job_type' => $request->job_type,
                'change_percentage' => $request->change_percentage,
                'change_details' => $request->change_details,
                'job_order' => $request->job_order,
                'file_data' => $request->file_data,
                'proses' => $request->proses,
                'prioritas_job' => $request->prioritas_job,
                'attachment_paths' => $attachmentPaths,
                'catatan' => $request->catatan,
                'status_job' => 'IN_PROGRESS_PREPRESS', // Langsung ke prepress tanpa konfirmasi RnD
                'marketing_user_id' => Auth::id(),
                // Special materials fields
                'kertas_khusus' => $request->boolean('kertas_khusus'),
                'kertas_khusus_detail' => $request->kertas_khusus_detail,
                'tinta_khusus' => $request->boolean('tinta_khusus'),
                'tinta_khusus_detail' => $request->tinta_khusus_detail,
                'foil_khusus' => $request->boolean('foil_khusus'),
                'foil_khusus_detail' => $request->foil_khusus_detail,
                'pale_tooling_khusus' => $request->boolean('pale_tooling_khusus'),
                'pale_tooling_khusus_detail' => $request->pale_tooling_khusus_detail,
            ]);

            // Log special materials data for debugging
            Log::info('Special materials data being saved:', [
                'kertas_khusus' => $request->boolean('kertas_khusus'),
                'kertas_khusus_detail' => $request->kertas_khusus_detail,
                'tinta_khusus' => $request->boolean('tinta_khusus'),
                'tinta_khusus_detail' => $request->tinta_khusus_detail,
                'foil_khusus' => $request->boolean('foil_khusus'),
                'foil_khusus_detail' => $request->foil_khusus_detail,
                'pale_tooling_khusus' => $request->boolean('pale_tooling_khusus'),
                'pale_tooling_khusus_detail' => $request->pale_tooling_khusus_detail,
            ]);

            // Log bahwa job langsung dikirim ke prepress tanpa konfirmasi RnD
            Log::info('Job development created and sent directly to prepress:', [
                'job_code' => $jobCode,
                'customer' => $request->customer,
                'product' => $request->product,
                'status' => 'IN_PROGRESS_PREPRESS',
                'created_by' => Auth::user()->name,
                'note' => 'Marketing input langsung ke prepress tanpa konfirmasi RnD'
            ]);

            // Kirim email notifikasi untuk input awal development
            try {
                $emailService = new DevelopmentEmailNotificationService();

                // Siapkan data job untuk email
                $jobData = [
                    'job_code' => $jobCode,
                    'job_name' => $jobName,
                    'customer' => $request->customer,
                    'product' => $request->product,
                    'kode_design' => $request->kode_design,
                    'dimension' => $request->dimension,
                    'material' => $request->material,
                    'total_color' => $request->total_color,
                    'colors' => $colors,
                    'qty_order_estimation' => $request->qty_order_estimation,
                    'job_type' => $request->job_type,
                    'change_percentage' => $request->change_percentage,
                    'change_details' => $request->change_details,
                    'prioritas_job' => $request->prioritas_job,
                    'tanggal' => $request->tanggal,
                    'job_deadline' => $request->job_deadline,
                    'catatan' => $request->catatan,
                    // Special materials
                    'kertas_khusus' => $request->boolean('kertas_khusus'),
                    'kertas_khusus_detail' => $request->kertas_khusus_detail,
                    'tinta_khusus' => $request->boolean('tinta_khusus'),
                    'tinta_khusus_detail' => $request->tinta_khusus_detail,
                    'foil_khusus' => $request->boolean('foil_khusus'),
                    'foil_khusus_detail' => $request->foil_khusus_detail,
                    'pale_tooling_khusus' => $request->boolean('pale_tooling_khusus'),
                    'pale_tooling_khusus_detail' => $request->pale_tooling_khusus_detail,
                ];

                // Kirim notifikasi email
                $emailSent = $emailService->sendInputAwalNotification($jobData);

                if ($emailSent) {
                    Log::info("Email notification sent successfully for job: {$jobCode}");
                } else {
                    Log::warning("Email notification failed for job: {$jobCode}");
                }
            } catch (\Exception $e) {
                // Log error tapi jangan gagalkan proses utama
                Log::error("Error sending email notification for job {$jobCode}: " . $e->getMessage());
            }

            $job = JobOrderDevelopment::findOrFail($jobOrderDevelopment->id);
            // dd($job);
            $deadline = Carbon::parse($job->tanggal)->addDays(3);

            // Parse job_order untuk create multiple prepress jobs
            $jobOrders = $job->job_order ? (is_string($job->job_order) ? json_decode($job->job_order, true) : $job->job_order) : [];
            $createdPrepressJobs = [];

            if (empty($jobOrders)) {
                // Jika tidak ada job_order, create single prepress job
                $prepressJob = JobPrepress::create([
                    'nomor_job_order' => $job->job_code,
                    'tanggal_job_order' => $job->tanggal,
                    'tanggal_deadline' => $deadline,
                    'customer' => $job->customer,
                    'product' => $job->product,
                    'kode_design' => $job->job_code,
                    'dimension' => $job->dimension,
                    'material' => $job->material,
                    'total_color' => $job->total_color,
                    'total_color_details' => $job->colors ? json_encode($job->colors) : null,
                    'qty_order_estimation' => $job->qty_order_estimation,
                    'job_order' => 'General Job', // Default job order jika tidak ada
                    'sub_unit_job' => 'General Unit', // Default sub unit jika tidak ada
                    'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                    'created_by' => auth()->id(),
                    'status_job' => 'OPEN',
                ]);
                $createdPrepressJobs[] = $prepressJob;
            } else {
                // Create multiple prepress jobs berdasarkan job_order
                $jobCount = count($jobOrders);

                foreach ($jobOrders as $index => $jobOrderItem) {
                    $jobNumber = $index + 1;
                    $nomorJobOrder = $jobCount > 1 ? $job->job_code . '_' . $jobNumber : $job->job_code;

                    // Extract jenis_pekerjaan dan unit_job dari jobOrderItem
                    $jenisPekerjaan = $jobOrderItem['jenis_pekerjaan'] ?? '';
                    $unitJob = $jobOrderItem['unit_job'] ?? '';

                    $job_title  = 'DEVELOPMENT - ' . $job->job_code . ' - ' . $jenisPekerjaan . ' - ' . $job->product;

                    $est_job_default = JenisPekerjaanPrepress::where('nama_jenis', $jenisPekerjaan)->first();
                    $est_job_default = MasterDataPrepress::where('job', $est_job_default->id)->where('unit_job', $unitJob)->first();
                    $est_job_default = $est_job_default->waktu_job;

                    $kategori_job = MasterDataPrepress::where('unit_job', $unitJob)->first();

                    $prepressJob = JobPrepress::create([
                        'nomor_job_order' => $nomorJobOrder,
                        'tanggal_job_order' => $job->tanggal,
                        'tanggal_deadline' => $deadline,
                        'customer' => $job->customer,
                        'product' => $job->product,
                        'kode_design' => $job->job_code,
                        'dimension' => $job->dimension,
                        'material' => $job->material,
                        'total_color' => $job->total_color,
                        'total_color_details' => $job->colors ? json_encode($job->colors) : null,
                        'qty_order_estimation' => $job->qty_order_estimation,
                        'job_order' => $jenisPekerjaan, // jenis_pekerjaan masuk ke job_order
                        'sub_unit_job' => $unitJob, // unit_job masuk ke sub_unit_job
                        'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                        'created_by' => auth()->user()->name,
                        'job_title' => $job_title,
                        'kategori_job' => $kategori_job->kode,
                        'est_job_default' => $est_job_default,
                        'prioritas_job' => $job->prioritas_job,
                        'status_job' => 'OPEN',
                    ]);

                    $createdPrepressJobs[] = $prepressJob;
                }
            }

            // Update development job status
            $oldStatus = $job->status_job;
            $job->update([
                'status_job' => 'IN_PROGRESS_PREPRESS',
            ]);

            // Log the action ke handling developments untuk setiap prepress job
            foreach ($createdPrepressJobs as $prepressJob) {
                DevelopmentHandlingService::logSentToPrepress($job, $prepressJob->id);
            }

            // Log ke system logs
            SystemLogService::logJobDevelopmentStatusChange($job, $oldStatus, 'IN_PROGRESS_PREPRESS', 'Job dikirim ke tahap prepress', request());

            // Kirim notifikasi progress job
            $this->sendProgressJobNotification($job, $oldStatus, 'IN_PROGRESS_PREPRESS', 'Job dikirim ke tahap prepress');

            // Log the action
            $prepressJobIds = collect($createdPrepressJobs)->pluck('id')->toArray();
            Log::info('Job sent to prepress:', [
                'job_development_id' => $job->id,
                'prepress_job_ids' => $prepressJobIds,
                'job_count' => count($createdPrepressJobs),
                'deadline' => $deadline,
                'assigned_by' => auth()->id()
            ]);

            // Send email notification
            $emailService = new DevelopmentEmailNotificationService();

            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design,
                'dimension' => $job->dimension,
                'material' => $job->material,
                'total_color' => $job->total_color,
                'colors' => $job->colors,
                'qty_order_estimation' => $job->qty_order_estimation,
                'job_type' => $job->job_type,
                'prioritas_job' => $job->prioritas_job,
                'tanggal' => $job->tanggal,
                'prepress_deadline' => $deadline,
                'catatan' => $job->catatan,
                'job_order' => $jobOrders
            ];

            $emailService->sendPrepressNotification($jobData);

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Job Development berhasil dibuat dengan kode: ' . $jobCode . ' dan langsung dikirim ke Prepress',
                'data' => $jobOrderDevelopment
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating development job: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview email template untuk development notification
     */
    public function previewEmailTemplate(Request $request)
    {
        $type = $request->get('type', 'new'); // 'new' atau 'repeat'

        // Sample data untuk preview
        $setting = (object) [
            'process_name' => 'Input Awal Development',
            'description' => 'Notifikasi saat job development baru diinput oleh marketing'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Notifikasi awal input job development'
        ];

        if ($type === 'repeat') {
            // Data untuk produk repeat
            $jobData = [
                'job_code' => 'DEV-250109-002',
                'job_name' => 'Repeat Product - Test Customer',
                'customer' => 'PT. INOVASI TEKNOLOGI KOSMETIKA',
                'product' => 'IB HAIR OIL AUTOBUTTOM TUCK TOP (REVISION)',
                'kode_design' => 'IB-001-REV',
                'dimension' => '20x30 cm',
                'material' => 'Art Paper 150gsm',
                'total_color' => '4',
                'colors' => [
                    1 => 'Cyan',
                    2 => 'Magenta',
                    3 => 'Yellow',
                    4 => 'Black'
                ],
                'qty_order_estimation' => '2000',
                'job_type' => 'repeat',
                'change_percentage' => 25,
                'change_details' => ['ukuran_dimensi', 'warna', 'finishing'],
                'prioritas_job' => 'Urgent',
                'tanggal' => date('Y-m-d'),
                'job_deadline' => date('Y-m-d', strtotime('+14 days')),
                'catatan' => 'Test job repeat untuk preview email template'
            ];
        } else {
            // Data untuk produk baru
            $jobData = [
                'job_code' => 'DEV-250109-001',
                'job_name' => 'Test Product - Test Customer',
                'customer' => 'PT. INOVASI TEKNOLOGI KOSMETIKA',
                'product' => 'IB HAIR OIL AUTOBUTTOM TUCK TOP',
                'kode_design' => 'IB-001',
                'dimension' => '20x30 cm',
                'material' => 'Art Paper 150gsm',
                'total_color' => '4',
                'colors' => [
                    1 => 'Cyan',
                    2 => 'Magenta',
                    3 => 'Yellow',
                    4 => 'Black'
                ],
                'qty_order_estimation' => '1000',
                'job_type' => 'new',
                'prioritas_job' => 'Normal',
                'tanggal' => date('Y-m-d'),
                'job_deadline' => date('Y-m-d', strtotime('+30 days')),
                'catatan' => 'Test job untuk preview email template',
                'kertas_khusus' => true,
                'kertas_khusus_detail' => 'Art Paper 200gsm dengan finishing glossy',
                'tinta_khusus' => true,
                'tinta_khusus_detail' => 'Pantone 286C untuk logo'
            ];
        }

        $additionalData = [
            'notification_type' => 'input_awal',
            'action_url' => route('development.marketing-jobs.list'),
            'action_text' => 'Lihat Job Development'
        ];

        $currentUser = auth()->user();

        return view('emails.development-notification', compact(
            'setting',
            'reminder',
            'jobData',
            'additionalData',
            'currentUser'
        ));
    }

    /**
     * Preview prepress email template
     */
    public function previewPrepressEmailTemplate(Request $request)
    {
        $type = $request->get('type', 'prepress'); // 'prepress', 'H-2', 'H-1'

        // Sample data for preview
        $setting = (object) [
            'process_name' => 'Job Prepress',
            'description' => 'Notifikasi untuk job prepress dan reminder deadline'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Job Dikirim ke Prepress'
        ];

        if ($type === 'H-2') {
            $reminder = [
                'days' => '2',
                'description' => 'H-2'
            ];
        } elseif ($type === 'H-1') {
            $reminder = [
                'days' => '1',
                'description' => 'H-1'
            ];
        }

        // Sample data for prepress job
        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250109-001',
            'job_name' => 'Test Product - Test Customer',
            'customer' => 'PT. INOVASI TEKNOLOGI KOSMETIKA',
            'product' => 'IB HAIR OIL AUTOBUTTOM TUCK TOP',
            'kode_design' => 'IB-001',
            'dimension' => '20x30 cm',
            'material' => 'Art Paper 150gsm',
            'total_color' => '4',
            'colors' => [
                1 => 'Cyan', 2 => 'Magenta', 3 => 'Yellow', 4 => 'Black'
            ],
            'qty_order_estimation' => '1000',
            'job_type' => 'new',
            'prioritas_job' => 'Normal',
            'tanggal' => date('Y-m-d'),
            'prepress_deadline' => date('Y-m-d', strtotime('+3 days')),
            'catatan' => 'Test job untuk preview prepress email template',
            'job_order' => [
                [
                    'jenis_pekerjaan' => 'Design Layout',
                    'unit_job' => 'Design Unit'
                ],
                [
                    'jenis_pekerjaan' => 'Color Separation',
                    'unit_job' => 'Color Unit'
                ]
            ]
        ];

        $additionalData = [
            'notification_type' => $type === 'prepress' ? 'prepress' : 'prepress_reminder',
            'reminder_type' => $type,
            'action_url' => route('development.rnd-workspace.view', 1),
            'action_text' => 'Lihat Job Prepress'
        ];

        $currentUser = auth()->user();

        return view('emails.development-prepress-notification', compact(
            'setting',
            'reminder',
            'jobData',
            'additionalData',
            'currentUser'
        ));
    }

    /**
     * Ambil unit job berdasarkan jenis pekerjaan yang dipilih
     */
    public function getUnitJob(Request $request)
    {
        try {
            $request->validate([
                'jenis_pekerjaan' => 'required|string'
            ]);

            $jenisPekerjaan = $request->jenis_pekerjaan;

            // dd($jenisPekerjaan);
            // Cari di tabel tb_master_data_prepresses
            $jenisPekerjaanData = MasterDataPrepress::where('job', $jenisPekerjaan)->get();
            // dd($jenisPekerjaanData);

            if ($jenisPekerjaanData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis pekerjaan tidak ditemukan',
                    'unit_jobs' => []
                ], 404);
            }

            // Ambil semua unit job yang tersedia
            $unitJobs = $jenisPekerjaanData->pluck('unit_job')->unique()->filter()->values();

            return response()->json([
                'success' => true,
                'unit_jobs' => $unitJobs,
                'message' => 'Unit job berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unit job: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil unit job',
                'unit_jobs' => []
            ], 500);
        }
    }

    /**
     * Build specification from form data
     */
    private function buildSpecificationFromForm(Request $request)
    {
        $specification = [];

        $specification['tanggal'] = $request->tanggal;
        $specification['customer'] = $request->customer;
        $specification['product'] = $request->product;
        $specification['kode_design'] = $request->kode_design;
        $specification['dimension'] = $request->dimension;
        $specification['material'] = $request->material;
        $specification['total_color'] = $request->total_color;
        $specification['qty_order_estimation'] = $request->qty_order_estimation;
        $specification['job_type'] = $request->job_type;
        $specification['prioritas_job'] = $request->prioritas_job;
        $specification['catatan'] = $request->catatan;
        $specification['job_deadline'] = $request->job_deadline;

        // Add colors
        $colors = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($request->has("color.{$i}") && !empty($request->input("color.{$i}"))) {
                $colors[$i] = $request->input("color.{$i}");
            }
        }
        $specification['colors'] = $colors;

        // Add job order
        $specification['job_order'] = $request->job_order;

        // Add file data
        $specification['file_data'] = $request->file_data;

        // Add change details for repeat jobs
        if ($request->job_type === 'repeat') {
            $specification['change_percentage'] = $request->change_percentage;
            $specification['change_details'] = $request->change_details ?? [];
        }

        // Add special materials for new jobs
        if ($request->job_type === 'new') {
            $specification['kertas_khusus'] = $request->boolean('kertas_khusus');
            $specification['kertas_khusus_detail'] = $request->kertas_khusus_detail;
            $specification['tinta_khusus'] = $request->boolean('tinta_khusus');
            $specification['tinta_khusus_detail'] = $request->tinta_khusus_detail;
            $specification['foil_khusus'] = $request->boolean('foil_khusus');
            $specification['foil_khusus_detail'] = $request->foil_khusus_detail;
            $specification['pale_tooling_khusus'] = $request->boolean('pale_tooling_khusus');
            $specification['pale_tooling_khusus_detail'] = $request->pale_tooling_khusus_detail;
        }

        return json_encode($specification, JSON_PRETTY_PRINT);
    }

    // List semua job development yang sudah diinput marketing
    public function marketingJobList()
    {
        return view('main.process.development.marketing-job-list');
    }

    // AJAX data untuk DataTable
    public function marketingJobData(Request $request)
    {
        $query = JobOrderDevelopment::query();

        // Search functionality
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('job_code', 'like', "%{$searchValue}%")
                    ->orWhere('job_name', 'like', "%{$searchValue}%")
                    ->orWhere('customer', 'like', "%{$searchValue}%")
                    ->orWhere('product', 'like', "%{$searchValue}%")
                    ->orWhere('job_type', 'like', "%{$searchValue}%")
                    ->orWhere('prioritas_job', 'like', "%{$searchValue}%")
                    ->orWhere('status_job', 'like', "%{$searchValue}%");
            });
        }

        // Ordering
        $orderColumn = $request->input('order.0.column', 6); // Default: created_at
        $orderDir = $request->input('order.0.dir', 'desc');

        $columns = ['job_code', 'job_name', 'customer', 'job_type', 'prioritas_job', 'status_job', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';

        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $jobs = $query->skip($start)->take($length)->get();

        // Get total count for pagination
        $totalRecords = JobOrderDevelopment::count();
        $filteredRecords = $query->count();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $jobs
        ]);
    }

    /**
     * Test method to show job code generation logic
     */
    public function testJobCodeGeneration()
    {
        try {
            $today = now()->format('Y-m-d');
            $todayFormatted = now()->format('ymd'); // Format: 250801

            // Get all jobs created today
            $jobsToday = JobDevelopment::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->get(['id', 'job_code', 'created_at']);

            // Generate next job code
            $nextJobCode = $this->generateJobCode();

            return response()->json([
                'success' => true,
                'data' => [
                    'today_date' => $today,
                    'today_formatted' => $todayFormatted,
                    'jobs_created_today' => $jobsToday->count(),
                    'existing_jobs_today' => $jobsToday,
                    'next_job_code' => $nextJobCode,
                    'explanation' => [
                        'format' => 'DEV-YYMMDD-XXX',
                        'example' => 'DEV-250801-001',
                        'logic' => 'Sequence number increments for each job created on the same date'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RnD Planning List - Show jobs that need RnD planning
     */
    public function rndPlanningList()
    {
        return view('main.process.development.rnd-planning-list');
    }

    /**
     * RnD Workspace Edit - Edit job development from rnd-workspace
     */
    public function rndWorkspaceEdit($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if user can edit (only creator and status is OPEN)
        if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
            return redirect()->route('development.rnd-workspace')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit job ini.');
        }

        $mode = 'edit';
        return view('main.process.development.development-input', compact('job', 'mode'));
    }

    /**
     * RnD Workspace Delete - Delete job development from rnd-workspace
     */
    public function rndWorkspaceDelete($id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);

            // Check if user can delete (only creator and status is OPEN)
            if ($job->marketing_user_id !== auth()->id() || $job->status_job !== 'OPEN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus job ini.'
                ], 403);
            }

            // Delete attachment files
            if ($job->attachment_paths) {
                foreach ($job->attachment_paths as $path) {
                    $filePath = public_path($path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Force delete the job
            $job->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RnD Planning Data - AJAX endpoint for DataTable
     */
    public function rndPlanningData()
    {
        $jobs = JobDevelopment::whereIn('status', ['draft', 'planning'])
            ->with(['marketingUser', 'processes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $jobs
        ]);
    }

    /**
     * RnD Planning Form - Form untuk planning proses
     */
    public function rndPlanningForm($id)
    {
        $job = JobDevelopment::with(['marketingUser'])->findOrFail($id);
        $divisis = Divisi::all(); // Get all divisions

        if ($job->status !== 'draft') {
            return redirect()->route('development.rnd-planning.list')
                ->with('error', 'Job ini sudah tidak dapat di-planning');
        }

        return view('main.process.development.rnd-planning-form', compact('job', 'divisis'));
    }

    /**
     * RnD Planning Edit Form - Form untuk edit planning yang sudah ada
     */
    public function rndPlanningEditForm($id)
    {
        $job = JobDevelopment::with(['marketingUser', 'processes.assignedUser'])->findOrFail($id);
        $divisis = Divisi::all(); // Get all divisions

        if ($job->status !== 'planning') {
            return redirect()->route('development.rnd-planning.list')
                ->with('error', 'Job ini bukan status planning');
        }

        // Check if any process is already in progress
        $inProgressProcesses = $job->processes()->where('status', 'in_progress')->count();
        if ($inProgressProcesses > 0) {
            return redirect()->route('development.rnd-planning.list')
                ->with('error', 'Tidak dapat edit planning karena ada proses yang sedang dikerjakan');
        }

        return view('main.process.development.rnd-planning-edit-form', compact('job', 'divisis'));
    }

    /**
     * RnD Planning Store - Simpan planning proses
     */
    public function rndPlanningStore(Request $request, $id)
    {
        try {
            $job = JobDevelopment::findOrFail($id);

            if ($job->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Job ini sudah tidak dapat di-planning'
                ], 400);
            }

            $request->validate([
                'processes' => 'required|array|min:1',
                'processes.*.process_name' => 'required|string|max:255',
                'processes.*.process_type' => 'required|in:normal,ppic,purchasing,qc,rnd_verification',
                'processes.*.department' => 'required|exists:tb_divisis,id',
                'processes.*.assigned_user_id' => 'required|exists:users,id',
                'processes.*.estimated_duration' => 'required|integer|min:1',
                'processes.*.process_order' => 'required|integer|min:1'
            ]);

            // Update job status to planning
            $job->update(['status' => 'planning']);

            // Create job processes with branching logic
            foreach ($request->processes as $processData) {
                $branchConditions = $this->generateBranchConditions($job->type, $processData['process_type']);

                JobDevelopmentProcess::create([
                    'job_development_id' => $job->id,
                    'process_name' => $processData['process_name'],
                    'process_type' => $processData['process_type'],
                    'branch_type' => $job->type,
                    'branch_conditions' => $branchConditions,
                    'department_id' => $processData['department'],
                    'assigned_user_id' => $processData['assigned_user_id'],
                    'estimated_duration' => $processData['estimated_duration'],
                    'process_order' => $processData['process_order'],
                    'status' => 'pending'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Planning proses berhasil disimpan',
                'data' => $job->load('processes')
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing RnD planning: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan planning'
            ], 500);
        }
    }

    /**
     * Generate branch conditions based on job type and process type
     */
    private function generateBranchConditions($jobType, $processType)
    {
        if ($jobType === 'proof') {
            // Proof (Normal) flow
            switch ($processType) {
                case 'ppic':
                    return [
                        'next_step' => 'rnd_verification',
                        'condition' => 'ppic_confirms_completion',
                        'description' => 'PPIC menentukan jadwal produksi dan konfirmasi selesai'
                    ];
                case 'rnd_verification':
                    return [
                        'next_step' => 'completed',
                        'condition' => 'rnd_verifies_ppic_completion',
                        'description' => 'RnD verifikasi hasil PPIC'
                    ];
                default:
                    return [
                        'next_step' => 'next_process',
                        'condition' => 'normal_flow',
                        'description' => 'Proses normal'
                    ];
            }
        } else {
            // Trial Item Khusus flow
            switch ($processType) {
                case 'purchasing':
                    return [
                        'next_step' => 'qc',
                        'condition' => 'purchasing_tracks_materials',
                        'description' => 'Purchasing tracking status barang (dipesan/diterima)'
                    ];
                case 'qc':
                    return [
                        'next_step' => 'rnd_verification',
                        'condition' => 'qc_verifies_item',
                        'description' => 'QC verifikasi item (OK/TIDAK OK)'
                    ];
                case 'rnd_verification':
                    return [
                        'next_step' => 'conditional',
                        'condition' => 'rnd_final_verification',
                        'description' => 'RnD verifikasi final berdasarkan hasil QC'
                    ];
                default:
                    return [
                        'next_step' => 'next_process',
                        'condition' => 'normal_flow',
                        'description' => 'Proses normal'
                    ];
            }
        }
    }

    /**
     * RnD Planning Update - Update planning yang sudah ada
     */
    public function rndPlanningUpdate(Request $request, $id)
    {
        try {
            $job = JobDevelopment::with('processes')->findOrFail($id);

            if ($job->status !== 'planning') {
                return response()->json([
                    'success' => false,
                    'message' => 'Job ini bukan status planning'
                ], 400);
            }

            // Check if any process is already in progress
            $inProgressProcesses = $job->processes()->where('status', 'in_progress')->count();
            if ($inProgressProcesses > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat edit planning karena ada proses yang sedang dikerjakan'
                ], 400);
            }

            $request->validate([
                'processes' => 'required|array|min:1',
                'processes.*.process_name' => 'required|string|max:255',
                'processes.*.department' => 'required|exists:divisi,id',
                'processes.*.assigned_user_id' => 'required|exists:users,id',
                'processes.*.estimated_duration' => 'required|integer|min:1',
                'processes.*.process_order' => 'required|integer|min:1'
            ]);

            // Delete existing processes
            $job->processes()->delete();

            // Create new processes
            foreach ($request->processes as $processData) {
                JobDevelopmentProcess::create([
                    'job_development_id' => $job->id,
                    'process_name' => $processData['process_name'],
                    'department_id' => $processData['department'],
                    'assigned_user_id' => $processData['assigned_user_id'],
                    'estimated_duration' => $processData['estimated_duration'],
                    'process_order' => $processData['process_order'],
                    'status' => 'pending'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Planning berhasil diupdate',
                'data' => $job->load('processes')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating RnD planning: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update planning'
            ], 500);
        }
    }

    /**
     * Get job processes for a specific job
     */
    public function getJobProcesses($id)
    {
        $job = JobDevelopment::with(['processes.assignedUser', 'processes.department'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * Show In Progress Processes - Monitoring semua proses yang sedang berjalan
     */
    public function inProgressProcesses()
    {
        return view('main.process.development.in-progress-processes');
    }

    /**
     * Get In Progress Processes Data - AJAX endpoint untuk DataTable
     */
    public function inProgressProcessesData()
    {
        $processes = JobDevelopmentProcess::where('status', 'in_progress')
            ->with(['jobDevelopment', 'assignedUser', 'department'])
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'data' => $processes
        ]);
    }

    /**
     * Get job details for modal view
     */
    public function getJobDetails($id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);
            return response()->json($job);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job tidak ditemukan'
            ], 404);
        }
    }

    /**
     * RnD Workspace - Halaman utama ruang kerja RnD
     */
    public function rndWorkspace()
    {
        $user = User::with('divisiUser')->where('id', auth()->user()->id)->first();
        return view('main.process.development.rnd-workspace', compact('user'));
    }

    public function masterProses()
    {
        $masterProsesService = new MasterProsesService();
        $statistics = $masterProsesService->getProsesStatistics();
        $department = Divisi::all();

        return view('main.process.development.master-proses', compact('statistics', 'department'));
    }

    public function masterProsesData(Request $request)
    {
        $query = MasterProsesDevelopment::all();


        return response()->json([
            'success' => true,
            'data' => $query
        ]);
    }

    private function getAssignedDepartment($status)
    {
        $departmentMap = [
            'DRAFT' => 'RnD',
            'OPEN' => 'Prepress',
            'IN_PROGRESS' => 'Prepress',
            'FINISH_PREPRESS' => 'Marketing',
            'MEETING_OPP' => 'Marketing',
            'CUSTOMER_APPROVED' => 'Marketing',
            'CUSTOMER_REJECTED' => 'Marketing',
            'READY_FOR_CUSTOMER' => 'PPIC',
            'SCHEDULED_FOR_PRODUCTION' => 'PPIC',
            'PRODUCTION_APPROVED_BY_RND' => 'Production',
            'PRODUCTION_COMPLETED' => 'Production',
            'SALES_ORDER_CREATED' => 'RnD',
            'COMPLETED' => 'Completed'
        ];

        return $departmentMap[$status] ?? 'Unknown';
    }

    private function getCurrentStage($job)
    {
        $stages = [
            'DRAFT' => 'Job Created',
            'OPEN' => 'Sent to Prepress',
            'IN_PROGRESS' => 'Prepress Processing',
            'FINISH_PREPRESS' => 'Prepress Completed',
            'MEETING_OPP' => 'Meeting OPP',
            'CUSTOMER_APPROVED' => 'Customer Approved',
            'CUSTOMER_REJECTED' => 'Customer Rejected',
            'READY_FOR_CUSTOMER' => 'Ready for Customer',
            'SCHEDULED_FOR_PRODUCTION' => 'Scheduled for Production',
            'PRODUCTION_APPROVED_BY_RND' => 'Production Approved by RnD',
            'PRODUCTION_COMPLETED' => 'Production Completed',
            'SALES_ORDER_CREATED' => 'Sales Order Created',
            'COMPLETED' => 'Development Completed'
        ];

        return $stages[$job->status_job] ?? 'Unknown Stage';
    }

    private function getProgressPercentage($status)
    {
        $progressMap = [
            'DRAFT' => 5,
            'OPEN' => 10,
            'IN_PROGRESS' => 20,
            'FINISH_PREPRESS' => 30,
            'MEETING_OPP' => 40,
            'CUSTOMER_APPROVED' => 50,
            'CUSTOMER_REJECTED' => 45,
            'READY_FOR_CUSTOMER' => 60,
            'SCHEDULED_FOR_PRODUCTION' => 70,
            'PRODUCTION_APPROVED_BY_RND' => 80,
            'PRODUCTION_COMPLETED' => 90,
            'SALES_ORDER_CREATED' => 95,
            'COMPLETED' => 100
        ];

        return $progressMap[$status] ?? 0;
    }

    private function getDaysRemaining($deadline)
    {
        if (!$deadline) return null;

        $deadline = \Carbon\Carbon::parse($deadline);
        $today = \Carbon\Carbon::today();

        if ($deadline->isPast()) {
            return 'Overdue';
        }

        return $deadline->diffInDays($today) . ' days';
    }

    public function masterProsesDetail($id)
    {
        try {
            $job = JobOrderDevelopment::all();
            $masterProsesService = new MasterProsesService();

            // Initialize master proses if not exists
            $masterProses = $masterProsesService->getMasterProses($id);
            if ($masterProses->isEmpty()) {
                $masterProsesService->initializeMasterProses($id);
                $masterProses = $masterProsesService->getMasterProses($id);
            }
            // dd($masterProses);

            $completedCount = $masterProsesService->getCompletedCount($id);
            $totalCount = $masterProsesService->getTotalCount($id);
            $progressPercentage = $masterProsesService->getProgressPercentage($id);
            $currentProses = $masterProsesService->getCurrentProses($id);

            return view('main.process.development.master-proses-detail', compact(
                'job',
                'masterProses',
                'completedCount',
                'totalCount',
                'progressPercentage',
                'currentProses'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading master proses detail: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading master proses detail');
        }
    }

    public function updateProsesStatus(Request $request)
    {
        try {
            $request->validate([
                'proses_id' => 'required|exists:tb_master_proses_developments,id',
                'status' => 'required|in:start,complete,skip',
                'notes' => 'nullable|string|max:1000'
            ]);

            $proses = MasterProsesDevelopment::all();
            $masterProsesService = new MasterProsesService();

            $success = $masterProsesService->updateProsesStatus(
                $proses->job_order_development_id,
                $proses->urutan_proses,
                $request->status,
                auth()->id(),
                $request->notes
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proses status updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update proses status'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error updating proses status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating proses status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RnD Workspace Data - AJAX endpoint untuk DataTable
     */
    public function rndWorkspaceData(Request $request)
    {
        // dd($request->all());
        $query = JobOrderDevelopment::whereIn('status_job', [
            'DRAFT',
            'IN_PROGRESS_PREPRESS',
            'PLANNING',
            'OPEN',
            'IN_PROGRESS',
            'COMPLETED',
            'ASSIGNED_TO_PPIC',
            'FINISH_PREPRESS',
            'MEETING_OPP',
            'READY_FOR_CUSTOMER',
            'REJECTED_BY_MARKETING',
            'REJECTED_BY_CUSTOMER',
            'SALES_ORDER_CREATED',
            'SCHEDULED_FOR_PRODUCTION',
            'IN_PRODUCTION',
            'PRODUCTION_COMPLETED',
            'PRODUCTION_CANCELLED',
            'PRODUCTION_REVISED',
            'PRODUCTION_APPROVED',
            'PRODUCTION_REJECTED',
            'PRODUCTION_APPROVED_BY_RND',
            'PRODUCTION_REJECTED_BY_RND',
            'PRODUCTION_REVISED_BY_RND',
            'WAITING_MPP',
            'MPP_APPROVED',
            'MPP_REJECTED',
        ]);

        // dd($request->has('status_filter'));

        // Apply status filter if provided
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            $statusFilter = $request->status_filter;
            
            // Debug: Log the filter being applied
            Log::info('Applying status filter:', [
                'status_filter' => $statusFilter,
                'request_all' => $request->all()
            ]);

            // Handle multiple statuses (comma-separated)
            if (strpos($statusFilter, ',') !== false) {
                $statuses = array_map('trim', explode(',', $statusFilter));
                $query->whereIn('status_job', $statuses);
            } else {
                $query->where('status_job', trim($statusFilter));
            }
        } else {
            // Debug: Log when no filter is applied
            Log::info('No status filter applied', [
                'has_status_filter' => $request->has('status_filter'),
                'status_filter_value' => $request->input('status_filter'),
                'request_all' => $request->all()
            ]);
        }

        // dd($query->get());

        // Apply search filter if provided
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('job_code', 'like', '%' . $searchValue . '%')
                  ->orWhere('customer', 'like', '%' . $searchValue . '%')
                  ->orWhere('product', 'like', '%' . $searchValue . '%')
                  ->orWhere('status_job', 'like', '%' . $searchValue . '%')
                  ->orWhere('job_type', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('marketingUser', function($userQuery) use ($searchValue) {
                      $userQuery->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        // Get total records count before any filtering
        $totalRecords = JobOrderDevelopment::whereIn('status_job', [
            'DRAFT', 'IN_PROGRESS_PREPRESS', 'PLANNING', 'OPEN', 'IN_PROGRESS', 'COMPLETED', 'ASSIGNED_TO_PPIC',
            'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'REJECTED_BY_MARKETING',
            'REJECTED_BY_CUSTOMER', 'SALES_ORDER_CREATED', 'SCHEDULED_FOR_PRODUCTION',
            'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_CANCELLED', 'PRODUCTION_REVISED',
            'PRODUCTION_APPROVED', 'PRODUCTION_REJECTED', 'PRODUCTION_APPROVED_BY_RND',
            'PRODUCTION_REJECTED_BY_RND', 'PRODUCTION_REVISED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'MPP_REJECTED'
        ])->count();

        // Get filtered records count (after search and status filter)
        $filteredRecords = $query->count();

        // Apply ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');

        // Map column index to actual column name
        $columns = [
            0 => 'id', // No column
            1 => 'job_code',
            2 => 'customer',
            3 => 'product',
            4 => 'tanggal',
            5 => 'job_deadline',
            6 => 'status_job',
            7 => 'marketingUser.name',
            8 => 'job_type',
            9 => 'id' // Actions column
        ];

        $orderColumnName = $columns[$orderColumn] ?? 'created_at';

        // Handle ordering for related models
        if ($orderColumnName === 'marketingUser.name') {
            $query->join('users', 'job_order_developments.marketing_user_id', '=', 'users.id')
                  ->orderBy('users.name', $orderDirection)
                  ->select('job_order_developments.*');
        } else {
            $query->orderBy($orderColumnName, $orderDirection);
        }

        // Check if only dashboard data is needed (before pagination)
        if ($request->has('dashboard_only') && $request->dashboard_only) {
            // Use same base query as DataTable to ensure consistency
            $baseQuery = JobOrderDevelopment::whereIn('status_job', [
                'DRAFT',
                'IN_PROGRESS_PREPRESS',
                'PLANNING',
                'OPEN',
                'IN_PROGRESS',
                'COMPLETED',
                'ASSIGNED_TO_PPIC',
                'FINISH_PREPRESS',
                'MEETING_OPP',
                'READY_FOR_CUSTOMER',
                'REJECTED_BY_MARKETING',
                'REJECTED_BY_CUSTOMER',
                'SALES_ORDER_CREATED',
                'SCHEDULED_FOR_PRODUCTION',
                'IN_PRODUCTION',
                'PRODUCTION_COMPLETED',
                'PRODUCTION_CANCELLED',
                'PRODUCTION_REVISED',
                'PRODUCTION_APPROVED',
                'PRODUCTION_REJECTED',
                'PRODUCTION_APPROVED_BY_RND',
                'PRODUCTION_REJECTED_BY_RND',
                'PRODUCTION_REVISED_BY_RND',
                'WAITING_MPP',
                'MPP_APPROVED',
                'MPP_REJECTED',
            ]);
            
            // Count using same base query as DataTable
            $dashboardData = [
                'draft' => (clone $baseQuery)->where('status_job', 'DRAFT')->count(),
                'open' => (clone $baseQuery)->where('status_job', 'OPEN')->count(),
                'in_progress' => (clone $baseQuery)->whereIn('status_job', ['IN_PROGRESS', 'PLANNING'])->count(),
                'completed' => (clone $baseQuery)->where('status_job', 'COMPLETED')->count(),
                'meeting_opp' => (clone $baseQuery)->where('status_job', 'MEETING_OPP')->count(),
                'scheduling' => (clone $baseQuery)->where('status_job', 'READY_FOR_CUSTOMER')->count(),
                'production' => (clone $baseQuery)->whereIn('status_job', ['SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_APPROVED_BY_RND'])->count(),
                'map_proof' => (clone $baseQuery)->whereIn('status_job', ['OPEN', 'WAITING_MPP', 'MPP_APPROVED', 'MPP_REJECTED'])->count(),
                'prepress' => (clone $baseQuery)->where('status_job', 'IN_PROGRESS_PREPRESS')->count(),
                'user_role' => auth()->user()->divisiUser->divisi ?? 'RnD'
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboardData
            ]);
        }

        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $jobs = $query->with(['marketingUser', 'prepressJob', 'materialPurchasing'])
            ->skip($start)
            ->take($length)
            ->get();

        // Debug info
        Log::info('RnD Workspace Data Query:', [
            'total_jobs' => $totalRecords,
            'filtered_jobs' => $filteredRecords,
            'returned_jobs' => $jobs->count(),
            'search_value' => $request->search['value'] ?? null,
            'status_filter' => $request->status_filter ?? null,
            'has_status_filter' => $request->has('status_filter'),
            'status_filter_value' => $request->input('status_filter'),
            'order_column' => $orderColumnName,
            'order_direction' => $orderDirection,
            'all_params' => $request->all()
        ]);

        // Format response untuk DataTables server-side processing
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $jobs->toArray()
        ]);
    }

    /**
     * Assign Job to PPIC - Mengalihkan job ke PPIC untuk prepress
     */
    public function assignJobToPPIC(Request $request, $id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);

            // Validasi status job
            if ($job->status_job !== 'OPEN' && $job->status_job !== 'IN_PROGRESS') {
                return response()->json([
                    'success' => false,
                    'message' => 'Job harus dalam status OPEN atau IN_PROGRESS untuk dialihkan ke PPIC'
                ], 400);
            }

            // Update status job
            $job->update([
                'status_job' => 'ASSIGNED_TO_PPIC',
                'assigned_to_ppic_at' => now(),
                'assigned_to_ppic_by' => auth()->id()
            ]);

            // Buat record di tabel job prepress
            $jobPrepress = JobPrepress::create([
                'nomor_job_order' => $job->job_code,
                'tanggal_job_order' => $job->tanggal,
                'tanggal_deadline' => $job->job_deadline ?? now()->addDays(7),
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design,
                'dimension' => $job->dimension,
                'material' => $job->material,
                'total_colour' => $job->total_color,
                'qty_order_estimation' => $job->qty_order_estimation,
                'job_order' => json_encode($job->job_order),
                'file_data' => json_encode($job->file_data),
                'created_by' => auth()->user()->name,
                'status_job' => 'PENDING',
                'prioritas_job' => $job->prioritas_job,
                'catatan' => $job->catatan,
                'planned_at' => now(),
                'planned_by' => auth()->user()->name,
                'assigned_from_development' => true,
                'development_job_id' => $job->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dialihkan ke PPIC untuk prepress',
                'data' => [
                    'job_development' => $job,
                    'job_prepress' => $jobPrepress
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning job to PPIC: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengalihkan job ke PPIC: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Job Details for RnD Workspace
     */
    public function getRndJobDetails($id)
    {
        try {
            $job = JobOrderDevelopment::with(['marketingUser', 'assignedToPPICBy', 'prepressJob'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update job (only if status is OPEN and user is creator)
     */
    public function updateJob(Request $request, $id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);

            // Log request data for debugging
            Log::info('Update job request data:', [
                'job_id' => $id,
                'request_data' => $request->all(),
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Check if job can be edited (only OPEN status and creator)
            if ($job->status_job !== 'OPEN' || $job->marketing_user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job dengan status ' . $job->status_job . ' tidak dapat diedit atau Anda bukan pembuat job ini'
                ], 403);
            }

            $request->validate([
                'customer' => 'nullable|string|max:255',
                'product' => 'nullable|string|max:255',
                'kode_design' => 'nullable|string|max:255',
                'dimension' => 'nullable|string|max:255',
                'material' => 'nullable|string|max:255',
                'total_color' => 'nullable|string|max:255',
                'qty_order_estimation' => 'nullable|string|max:255',
                'job_type' => 'nullable|in:new,repeat',
                'change_percentage' => 'nullable|integer|min:0|max:100',
                'change_details' => 'nullable|array',
                'proses' => 'nullable|array',
                'prioritas_job' => 'nullable|in:Urgent,Normal',
                'catatan' => 'nullable|string',
                'tanggal' => 'nullable|date',
                'job_deadline' => 'nullable|date',
                'status_job' => 'nullable|string',
                'job_id' => 'nullable|integer',
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                // Special materials validation
                'kertas_khusus' => 'nullable|boolean',
                'kertas_khusus_detail' => 'nullable|string|max:500',
                'tinta_khusus' => 'nullable|boolean',
                'tinta_khusus_detail' => 'nullable|string|max:500',
                'foil_khusus' => 'nullable|boolean',
                'foil_khusus_detail' => 'nullable|string|max:500',
                'pale_tooling_khusus' => 'nullable|boolean',
                'pale_tooling_khusus_detail' => 'nullable|string|max:500',
            ]);

            $updateData = [
                'customer' => $request->customer ?? $job->customer,
                'product' => $request->product ?? $job->product,
                'kode_design' => $request->kode_design ?? $job->kode_design,
                'dimension' => $request->dimension ?? $job->dimension,
                'material' => $request->material ?? $job->material,
                'total_color' => $request->total_color ?? $job->total_color,
                'qty_order_estimation' => $request->qty_order_estimation ?? $job->qty_order_estimation,
                'job_type' => $request->job_type ?? $job->job_type,
                'change_percentage' => $request->change_percentage ?? $job->change_percentage,
                'change_details' => $request->change_details ?? $job->change_details,
                'prioritas_job' => $request->prioritas_job ?? $job->prioritas_job,
                'catatan' => $request->catatan ?? $job->catatan,
                'tanggal' => $request->tanggal ?? $job->tanggal,
                'job_deadline' => $request->job_deadline ?? $job->job_deadline,
                // Special materials fields
                'kertas_khusus' => $request->boolean('kertas_khusus') ?? $job->kertas_khusus,
                'kertas_khusus_detail' => $request->kertas_khusus_detail ?? $job->kertas_khusus_detail,
                'tinta_khusus' => $request->boolean('tinta_khusus') ?? $job->tinta_khusus,
                'tinta_khusus_detail' => $request->tinta_khusus_detail ?? $job->tinta_khusus_detail,
                'foil_khusus' => $request->boolean('foil_khusus') ?? $job->foil_khusus,
                'foil_khusus_detail' => $request->foil_khusus_detail ?? $job->foil_khusus_detail,
                'pale_tooling_khusus' => $request->boolean('pale_tooling_khusus') ?? $job->pale_tooling_khusus,
                'pale_tooling_khusus_detail' => $request->pale_tooling_khusus_detail ?? $job->pale_tooling_khusus_detail,
            ];

            // dd($updateData);

            // Handle colors array
            if ($request->has('color')) {
                $colors = [];
                foreach ($request->color as $index => $color) {
                    if (!empty($color) && $color !== null) {
                        $colors[$index] = $color;
                    }
                }
                $updateData['colors'] = $colors;
            }

            // Handle job_order array
            if ($request->has('job_order')) {
                $jobOrder = [];
                $counter = 1;
                foreach ($request->job_order as $index => $order) {
                    if (!empty($order['jenis_pekerjaan']) && !empty($order['unit_job'])) {
                        $jobOrder[$counter] = $order;
                        $counter++;
                    }
                }
                $updateData['job_order'] = $jobOrder;
            }

            // Handle file_data array
            if ($request->has('file_data')) {
                $updateData['file_data'] = $request->file_data;
            }

            // Handle proses array
            if ($request->has('proses')) {
                $updateData['proses'] = $request->proses;
            }

            // Handle existing attachments deletion
            $attachmentPaths = $job->attachment_paths ?? [];
            if ($request->has('delete_attachments')) {
                foreach ($request->delete_attachments as $deleteIndex) {
                    if (isset($attachmentPaths[$deleteIndex])) {
                        $filePath = public_path($attachmentPaths[$deleteIndex]);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        unset($attachmentPaths[$deleteIndex]);
                    }
                }
                // Reindex array
                $attachmentPaths = array_values($attachmentPaths);
            }

            // Handle new file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/development'), $filename);
                    $attachmentPaths[] = 'uploads/development/' . $filename;
                }
            }

            $updateData['attachment_paths'] = $attachmentPaths;

            // Log update data for debugging
            Log::info('Update data being saved:', $updateData);
            Log::info('Original job data:', $job->toArray());

            $job->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil diupdate',
                'data' => $job
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating job: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update job'
            ], 500);
        }
    }

    /**
     * Delete job (only if status is OPEN and user is creator)
     */
    public function deleteJob($id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);

            // Check if job can be deleted (only OPEN status and creator)
            if ($job->status_job !== 'OPEN' || $job->marketing_user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job dengan status ' . $job->status_job . ' tidak dapat dihapus atau Anda bukan pembuat job ini'
                ], 403);
            }

            // Delete attachment files
            if ($job->attachment_paths) {
                foreach ($job->attachment_paths as $path) {
                    $filePath = public_path($path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Force delete the job
            $job->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting job: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus job'
            ], 500);
        }
    }

    /**
     * Show RnD Workspace View (Detail Job)
     */
    public function rndWorkspaceView($id)
    {
        // dd($id);
        try {
            $job = JobOrderDevelopment::with([
                'marketingUser',
                'assignedToPPICBy',
                'prepressJob',
                'meetingOpp1',
                'meetingOpp2',
                'schedulingDevelopment',
                'mapProof',
                'salesOrder',
                'materialPurchasing'
            ])->findOrFail($id);

            // Get current user and their division
            $user = auth()->user();
            $userDivision = $user->divisiUser->divisi;
            // dd($userDivision);

            // Get master proses development for current status
            // Map job status to master proses status
            $statusMapping = [
                'DRAFT' => 'DRAFT',
                'OPEN' => 'DRAFT', // OPEN masih dalam tahap DRAFT (RnD mengirim ke Prepress)
                'IN_PROGRESS_PREPRESS' => 'IN_PROGRESS_PREPRESS',
                'FINISH_PREPRESS' => 'FINISH_PREPRESS',
                'MEETING_OPP' => 'MEETING_OPP',
                'READY_FOR_CUSTOMER' => 'READY_FOR_CUSTOMER',
                'SCHEDULED_FOR_PRODUCTION' => 'SCHEDULED_FOR_PRODUCTION',
                'IN_PRODUCTION' => 'IN_PRODUCTION', // Tambahkan mapping untuk IN_PRODUCTION
                'PRODUCTION_COMPLETED' => 'PRODUCTION_COMPLETED',
                'PRODUCTION_APPROVED_BY_RND' => 'PRODUCTION_APPROVED_BY_RND',
                'WAITING_MPP' => 'WAITING_MPP',
                'MPP_APPROVED' => 'MPP_APPROVED',
                'SALES_ORDER_CREATED' => 'SALES_ORDER_CREATED',
                'COMPLETED' => 'COMPLETED'
            ];

            $masterProsesStatus = $statusMapping[$job->status_job] ?? 'DRAFT';
            // dd($masterProsesStatus);
            $masterProses = MasterProsesDevelopment::where('status_proses', $masterProsesStatus)
                ->orderBy('urutan_proses')
                ->first();


            return view('main.process.development.rnd-workspace-view', compact('job', 'user', 'userDivision', 'masterProses'));
        } catch (\Exception $e) {
            Log::error('Error showing RnD workspace view: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Job tidak ditemukan');
        }
    }

    public function rndWorkspaceTimeline($id)
    {
        try {
            $job = JobOrderDevelopment::with([
                'marketingUser',
                'assignedToPPICBy',
                'prepressJob',
                'meetingOpp1',
                'meetingOpp2',
                'schedulingDevelopment',
                'mapProof',
                'salesOrder',
                'materialPurchasing'
            ])->findOrFail($id);

            // Get current user and their division
            $user = auth()->user();
            $userDivision = $user->divisiUser->divisi;

            // Get master proses development for current status
            $statusMapping = [
                'DRAFT' => 'DRAFT',
                'OPEN' => 'DRAFT',
                'IN_PROGRESS_PREPRESS' => 'IN_PROGRESS_PREPRESS',
                'FINISH_PREPRESS' => 'FINISH_PREPRESS',
                'MEETING_OPP' => 'MEETING_OPP',
                'READY_FOR_CUSTOMER' => 'READY_FOR_CUSTOMER',
                'SCHEDULED_FOR_PRODUCTION' => 'SCHEDULED_FOR_PRODUCTION',
                'IN_PRODUCTION' => 'IN_PRODUCTION', // Tambahkan mapping untuk IN_PRODUCTION
                'PRODUCTION_COMPLETED' => 'PRODUCTION_COMPLETED',
                'PRODUCTION_APPROVED_BY_RND' => 'PRODUCTION_APPROVED_BY_RND',
                'WAITING_MPP' => 'WAITING_MPP',
                'MPP_APPROVED' => 'MPP_APPROVED',
                'SALES_ORDER_CREATED' => 'SALES_ORDER_CREATED',
                'COMPLETED' => 'COMPLETED'
            ];

            $masterProsesStatus = $statusMapping[$job->status_job] ?? 'DRAFT';
            $masterProses = MasterProsesDevelopment::where('status_proses', $masterProsesStatus)
                ->orderBy('urutan_proses')
                ->first();

            // Render timeline HTML
            $timelineHtml = view('main.process.development.partials.timeline', compact('job', 'user', 'userDivision', 'masterProses'))->render();

            return response()->json([
                'success' => true,
                'html' => $timelineHtml
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading timeline: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Timeline tidak dapat dimuat'
            ]);
        }
    }

    /**
     * Send Job to Prepress (for RnD)
     * Mengubah status dari DRAFT ke OPEN dan membuat job prepress
     */
    public function sendJobToPrepress($id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);
            // dd($job);
            $deadline = Carbon::parse($job->tanggal)->addDays(3);

            // Parse job_order untuk create multiple prepress jobs
            $jobOrders = $job->job_order ? (is_string($job->job_order) ? json_decode($job->job_order, true) : $job->job_order) : [];
            $createdPrepressJobs = [];

            if (empty($jobOrders)) {
                // Jika tidak ada job_order, create single prepress job
                $prepressJob = JobPrepress::create([
                    'nomor_job_order' => $job->job_code,
                    'tanggal_job_order' => $job->tanggal,
                    'tanggal_deadline' => $deadline,
                    'customer' => $job->customer,
                    'product' => $job->product,
                    'kode_design' => $job->job_code,
                    'dimension' => $job->dimension,
                    'material' => $job->material,
                    'total_color' => $job->total_color,
                    'total_color_details' => $job->colors ? json_encode($job->colors) : null,
                    'qty_order_estimation' => $job->qty_order_estimation,
                    'job_order' => 'General Job', // Default job order jika tidak ada
                    'sub_unit_job' => 'General Unit', // Default sub unit jika tidak ada
                    'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                    'created_by' => auth()->id(),
                    'status_job' => 'OPEN',
                ]);
                $createdPrepressJobs[] = $prepressJob;
            } else {
                // Create multiple prepress jobs berdasarkan job_order
                $jobCount = count($jobOrders);

                foreach ($jobOrders as $index => $jobOrderItem) {
                    $jobNumber = $index + 1;
                    $nomorJobOrder = $jobCount > 1 ? $job->job_code . '_' . $jobNumber : $job->job_code;

                    // Extract jenis_pekerjaan dan unit_job dari jobOrderItem
                    $jenisPekerjaan = $jobOrderItem['jenis_pekerjaan'] ?? '';
                    $unitJob = $jobOrderItem['unit_job'] ?? '';

                    $job_title  = 'DEVELOPMENT - ' . $job->job_code . ' - ' . $jenisPekerjaan . ' - ' . $job->product;

                    $est_job_default = JenisPekerjaanPrepress::where('nama_jenis', $jenisPekerjaan)->first();
                    $est_job_default = MasterDataPrepress::where('job', $est_job_default->id)->where('unit_job', $unitJob)->first();
                    $est_job_default = $est_job_default->waktu_job;

                    $prepressJob = JobPrepress::create([
                        'nomor_job_order' => $nomorJobOrder,
                        'tanggal_job_order' => $job->tanggal,
                        'tanggal_deadline' => $deadline,
                        'customer' => $job->customer,
                        'product' => $job->product,
                        'kode_design' => $job->job_code,
                        'dimension' => $job->dimension,
                        'material' => $job->material,
                        'total_color' => $job->total_color,
                        'total_color_details' => $job->colors ? json_encode($job->colors) : null,
                        'qty_order_estimation' => $job->qty_order_estimation,
                        'job_order' => $jenisPekerjaan, // jenis_pekerjaan masuk ke job_order
                        'sub_unit_job' => $unitJob, // unit_job masuk ke sub_unit_job
                        'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                        'created_by' => auth()->user()->name,
                        'job_title' => $job_title,
                        'est_job_default' => $est_job_default,
                        'prioritas_job' => $job->prioritas_job,
                        'status_job' => 'OPEN',
                    ]);

                    $createdPrepressJobs[] = $prepressJob;
                }
            }

            // Update development job status
            $oldStatus = $job->status_job;
            $job->update([
                'status_job' => 'IN_PROGRESS_PREPRESS',
            ]);

            // Log the action ke handling developments untuk setiap prepress job
            foreach ($createdPrepressJobs as $prepressJob) {
                DevelopmentHandlingService::logSentToPrepress($job, $prepressJob->id);
            }

            // Log ke system logs
            SystemLogService::logJobDevelopmentStatusChange($job, $oldStatus, 'IN_PROGRESS_PREPRESS', 'Job dikirim ke tahap prepress', request());

            // Kirim notifikasi progress job
            $this->sendProgressJobNotification($job, $oldStatus, 'IN_PROGRESS_PREPRESS', 'Job dikirim ke tahap prepress');

            // Log the action
            $prepressJobIds = collect($createdPrepressJobs)->pluck('id')->toArray();
            Log::info('Job sent to prepress:', [
                'job_development_id' => $job->id,
                'prepress_job_ids' => $prepressJobIds,
                'job_count' => count($createdPrepressJobs),
                'deadline' => $deadline,
                'assigned_by' => auth()->id()
            ]);

            // Send email notification
            $emailService = new DevelopmentEmailNotificationService();

            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design,
                'dimension' => $job->dimension,
                'material' => $job->material,
                'total_color' => $job->total_color,
                'colors' => $job->colors,
                'qty_order_estimation' => $job->qty_order_estimation,
                'job_type' => $job->job_type,
                'prioritas_job' => $job->prioritas_job,
                'tanggal' => $job->tanggal,
                'prepress_deadline' => $deadline,
                'catatan' => $job->catatan,
                'job_order' => $jobOrders
            ];

            $emailService->sendPrepressNotification($jobData);

            // Prepare response message
            $jobCount = count($createdPrepressJobs);
            $message = $jobCount > 1
                ? "Job berhasil dikirim ke prepress dengan {$jobCount} sub-job (deadline: " . $deadline->format('d/m/Y') . ")"
                : "Job berhasil dikirim ke prepress dengan deadline " . $deadline->format('d/m/Y');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'job_development' => $job,
                    'prepress_jobs' => $createdPrepressJobs,
                    'job_count' => $jobCount,
                    'prepress_job_ids' => $prepressJobIds
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending job to prepress: ' . $e->getMessage(), [
                'job_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim job ke prepress: ' . $e->getMessage()
            ], 500);
        }
    }

    // Meeting OPP Methods
    public function inputMeetingOPP($id)
    {
        $job = JobOrderDevelopment::with(['meetingOpp1', 'meetingOpp2'])->findOrFail($id);
        return view('main.process.development.meeting-opp-input', compact('job'));
    }

    public function storeMeetingOPP(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'meeting_number' => 'required|in:1,2',
            'meeting_date' => 'required|date',
            'status' => 'required|in:belum_berjalan,berjalan,selesai',
            'customer_response' => 'required|in:pending,acc,reject',
            'customer_notes' => 'nullable|string',
            'marketing_notes' => 'nullable|string',
            'rnd_notes' => 'nullable|string'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        $meetingData = [
            'job_development_id' => $job->id,
            'meeting_number' => $request->meeting_number,
            'meeting_date' => $request->meeting_date,
            'status' => $request->status,
            'customer_response' => $request->customer_response,
            'customer_notes' => $request->customer_notes,
            'marketing_notes' => $request->marketing_notes,
            'rnd_notes' => $request->rnd_notes,
            'created_by' => auth()->id()
        ];

        // Check if this is auto-update
        $isAutoUpdate = $request->has('is_auto_update') && $request->is_auto_update;

        if ($request->meeting_number == 1) {
            $meeting = $job->meetingOpp1()->updateOrCreate(['job_development_id' => $job->id], $meetingData);
        } else {
            $meeting = $job->meetingOpp2()->updateOrCreate(['job_development_id' => $job->id], $meetingData);
        }

        // Handle customer response
        if (!$isAutoUpdate) {
            $oldStatus = $job->status_job;
            if ($request->customer_response === 'acc') {
                // Customer ACC - lanjut ke proses berikutnya
                $job->update(['status_job' => 'READY_FOR_CUSTOMER']);
                $actionDescription = 'Meeting OPP ' . $request->meeting_number . ' - Customer ACC - Status job berubah menjadi READY_FOR_CUSTOMER';
                // Kirim notifikasi progress job
                $this->sendProgressJobNotification($job, $oldStatus, 'READY_FOR_CUSTOMER', 'Meeting OPP ' . $request->meeting_number . ' - Customer ACC');
            } elseif ($request->customer_response === 'reject') {
                // Customer REJECT - tetap di MEETING_OPP, akan dikembalikan ke Prepress
                $job->update(['status_job' => 'MEETING_OPP']);
                $actionDescription = 'Meeting OPP ' . $request->meeting_number . ' - Customer REJECT - Job akan dikembalikan ke Prepress untuk revisi';
                // Kirim notifikasi progress job
                $this->sendProgressJobNotification($job, $oldStatus, 'MEETING_OPP', 'Meeting OPP ' . $request->meeting_number . ' - Customer REJECT');
            } else {
                // Pending - tetap di MEETING_OPP
                $job->update(['status_job' => 'MEETING_OPP']);
                $actionDescription = 'Meeting OPP ' . $request->meeting_number . ' - Customer PENDING - Status job tetap MEETING_OPP';
                // Kirim notifikasi progress job
                $this->sendProgressJobNotification($job, $oldStatus, 'MEETING_OPP', 'Meeting OPP ' . $request->meeting_number . ' - Customer PENDING');
            }
        } else {
            // Auto-update
            $actionDescription = 'Status Meeting OPP ' . $request->meeting_number . ' otomatis berubah menjadi "Berjalan" karena tanggal meeting adalah hari ini';
        }

        // Log the action
        DevelopmentHandlingService::logMeetingOPP($job, $request->meeting_number, $request->status, $request->customer_response, $actionDescription);

        // Send email notification
        // $emailService = new DevelopmentEmailNotificationService();
        // $emailService->sendMeetingOPPNotification($job, $meetingData, $request->meeting_number);

        return response()->json([
            'success' => true,
            'message' => 'Meeting OPP ' . $request->meeting_number . ' berhasil disimpan',
            'data' => $meeting
        ]);
    }

    public function approveMeetingOPP($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Update status job berdasarkan response customer
        if ($job->meetingOpp1 && $job->meetingOpp1->customer_response === 'acc') {
            $oldStatus = $job->status_job;
            $job->update(['status_job' => 'APPROVED']);
            // Kirim notifikasi progress job
            $this->sendProgressJobNotification($job, $oldStatus, 'APPROVED', 'Meeting OPP diapprove - Job siap untuk tahap selanjutnya');
        }

        return response()->json([
            'success' => true,
            'message' => 'Meeting OPP berhasil diapprove'
        ]);
    }

    public function rndApproveMeetingOPP(Request $request, $id)
    {
        $request->validate([
            'meeting_number' => 'required|in:1,2',
            'rnd_approval' => 'required|in:pending,approve,reject',
            'rnd_notes' => 'nullable|string'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        if ($request->meeting_number == 1) {
            $meeting = $job->meetingOpp1;
        } else {
            $meeting = $job->meetingOpp2;
        }

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting OPP tidak ditemukan'
            ], 404);
        }

        // Update meeting dengan RnD approval
        $meeting->update([
            'rnd_approval' => $request->rnd_approval,
            'rnd_approval_notes' => $request->rnd_notes,
            'rnd_approved_at' => now(),
            'rnd_approved_by' => auth()->id()
        ]);

        // Log the action
        DevelopmentHandlingService::logAction(
            $job->id,
            'rnd_approve_meeting_opp_' . $request->meeting_number,
            'RnD ' . ($request->rnd_approval === 'approve' ? 'menyetujui' : 'menolak') . ' Meeting OPP ' . $request->meeting_number,
            null,
            null,
            [
                'meeting_number' => $request->meeting_number,
                'rnd_approval' => $request->rnd_approval,
                'rnd_notes' => $request->rnd_notes
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'RnD approval berhasil disimpan'
        ]);
    }

    public function marketingApproveMeetingOPP(Request $request, $id)
    {
        $request->validate([
            'meeting_number' => 'required|in:1,2',
            'marketing_approval' => 'required|in:pending,approve,reject',
            'marketing_notes' => 'nullable|string'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        if ($request->meeting_number == 1) {
            $meeting = $job->meetingOpp1;
        } else {
            $meeting = $job->meetingOpp2;
        }

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting OPP tidak ditemukan'
            ], 404);
        }

        // Update meeting dengan Marketing approval
        $meeting->update([
            'marketing_approval' => $request->marketing_approval,
            'marketing_approval_notes' => $request->marketing_notes,
            'marketing_approved_at' => now(),
            'marketing_approved_by' => auth()->id()
        ]);

        // Update status job berdasarkan customer response
        if ($request->marketing_approval === 'approve') {
            // Customer approve → Lanjut ke step berikutnya
            $job->update(['status_job' => 'READY_FOR_CUSTOMER']);
        } elseif ($request->marketing_approval === 'reject') {
            // Customer reject → Kembali ke prepress untuk revisi
            $job->update(['status_job' => 'REJECTED_BY_CUSTOMER']);
        }
        // Jika pending, tidak ubah status job (tetap MEETING_OPP)

        // Log the action
        $approvalText = '';
        if ($request->marketing_approval === 'approve') {
            $approvalText = 'Customer menyetujui (ACC)';
        } elseif ($request->marketing_approval === 'reject') {
            $approvalText = 'Customer menolak (REJECT)';
        } else {
            $approvalText = 'Marketing sedang memproses untuk kirim ke Customer';
        }

        DevelopmentHandlingService::logAction(
            $job->id,
            'customer_response_meeting_opp_' . $request->meeting_number,
            $approvalText . ' Meeting OPP ' . $request->meeting_number,
            null,
            null,
            [
                'meeting_number' => $request->meeting_number,
                'marketing_approval' => $request->marketing_approval,
                'marketing_notes' => $request->marketing_notes
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Marketing approval berhasil disimpan'
        ]);
    }

    // Scheduling Development Methods
    public function schedulingDevelopment($id)
    {
        $job = JobOrderDevelopment::with(['schedulingDevelopment'])->findOrFail($id);
        return view('main.process.development.scheduling-development', compact('job'));
    }

    public function storeLeadTime(Request $request, $id)
    {
        $request->validate([
            'tinta_material_days' => 'required|integer|min:0',
            'kertas_baru_days' => 'required|integer|min:0',
            'foil_days' => 'required|integer|min:0',
            'tooling_days' => 'required|integer|min:0',
            'produksi_hours' => 'required|numeric|min:0',
            'total_lead_time_days' => 'required|integer|min:0'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        // Update atau create lead time configuration
        $leadTime = $job->leadTimeConfiguration()->updateOrCreate(
            ['job_order_development_id' => $job->id],
            [
                'tinta_material_days' => $request->tinta_material_days,
                'kertas_baru_days' => $request->kertas_baru_days,
                'foil_days' => $request->foil_days,
                'tooling_days' => $request->tooling_days,
                'produksi_hours' => $request->produksi_hours,
                'total_lead_time_days' => $request->total_lead_time_days,
                'created_by' => auth()->user()->name ?? 'System'
            ]
        );

        DevelopmentHandlingService::logAction(
            $job->id,
            'lead_time_configuration',
            'Lead Time Configuration disimpan - Tinta: ' . $request->tinta_material_days . ' hari, Kertas: ' . $request->kertas_baru_days . ' hari, Foil: ' . $request->foil_days . ' hari, Tooling: ' . $request->tooling_days . ' hari',
            null,
            null,
            [
                'tinta_material_days' => $request->tinta_material_days,
                'kertas_baru_days' => $request->kertas_baru_days,
                'foil_days' => $request->foil_days,
                'tooling_days' => $request->tooling_days,
                'produksi_hours' => $request->produksi_hours,
                'total_lead_time_days' => $request->total_lead_time_days
            ]
        );

        // Kirim notifikasi email untuk proses produksi
        $this->sendProsesProduksiNotification($job, $leadTime);

        return response()->json([
            'success' => true,
            'message' => 'Lead Time Configuration berhasil disimpan',
            'data' => $leadTime
        ]);
    }

    public function storeScheduling(Request $request, $id)
    {
        $request->validate([
            'development_days' => 'required|integer|min:1',
            'max_lead_time_days' => 'required|integer|min:0',
            'total_estimated_days' => 'required|integer|min:1',
            'produksi_hours' => 'required|numeric|min:0',
            'ppic_notes' => 'nullable|string',
            'purchasing_notes' => 'nullable|string'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        $schedulingData = [
            'job_development_id' => $job->id,
            'default_days' => $request->default_days,
            'kertas_khusus_days' => $request->kertas_khusus_days ?? 0,
            'foil_khusus_days' => $request->foil_khusus_days ?? 0,
            'total_estimated_days' => $request->total_estimated_days,
            'ppic_notes' => $request->ppic_notes,
            'purchasing_notes' => $request->purchasing_notes,
            'created_by' => auth()->id()
        ];

        $scheduling = $job->schedulingDevelopment()->updateOrCreate(['job_development_id' => $job->id], $schedulingData);

        // Log the action
        DevelopmentHandlingService::logScheduling($job, $request->total_estimated_days);

        return response()->json([
            'success' => true,
            'message' => 'Scheduling Development berhasil disimpan',
            'data' => $scheduling
        ]);
    }

    // Map Proof Methods
    public function uploadMapProof($id)
    {
        $job = JobOrderDevelopment::with(['mapProof'])->findOrFail($id);
        return view('main.process.development.map-proof-upload', compact('job'));
    }

    public function storeMapProof(Request $request, $id)
    {
        // dd($request->all());
        $job = JobOrderDevelopment::findOrFail($id);

        // Check if this is update progress action
        if ($request->action === 'update_progress') {
            $request->validate([
                'progress' => 'required|in:proses_kirim,reject,accept',
                'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'customer_notes' => 'nullable|string',
                'marketing_notes' => 'nullable|string'
            ]);

            $proofData = [
                'job_development_id' => $job->id,
                'proof_type' => 'digital', // Default untuk progress update
                'customer_response' => $request->progress === 'proses_kirim' ? 'pending' : ($request->progress === 'accept' ? 'acc' : 'reject'),
                'customer_notes' => $request->customer_notes,
                'marketing_notes' => $request->marketing_notes,
                'created_by' => auth()->id(),
                'status' => $request->mapProofProgress,
            ];

            // Handle file upload
            if ($request->hasFile('proof_file')) {
                $file = $request->file('proof_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/map_proofs', $filename);
                $proofData['proof_file_path'] = 'map_proofs/' . $filename;
            }

            $mapProof = $job->mapProof()->updateOrCreate(['job_development_id' => $job->id], $proofData);

            // Update job status based on progress
            $oldStatus = $job->status_job;
            if ($request->progress === 'proses_kirim') {
                $job->status_job = 'WAITING_MPP';
                $job->save();
            } elseif ($request->progress === 'accept') {
                $job->status_job = 'MPP_APPROVED';
                $job->save();
            } elseif ($request->progress === 'reject') {
                $job->status_job = 'MPP_REJECTED';
                $job->save();
            }

            // Kirim notifikasi progress job
            $this->sendProgressJobNotification($job, $oldStatus, 'WAITING_MPP', 'Progress Map Proof berhasil diupdate - ' . $request->progress);

            // Log the action
            DevelopmentHandlingService::logMapProofProgressUpdate($job, $request->progress);

            $statusMessage = '';
            if ($request->progress === 'proses_kirim') {
                $statusMessage = ' dan status job berubah menjadi WAITING_MPP';
            } elseif ($request->progress === 'accept') {
                $statusMessage = ' dan status job berubah menjadi MPP_APPROVED';
            } elseif ($request->progress === 'reject') {
                $statusMessage = ' dan status job berubah menjadi MPP_REJECTED';
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress Map Proof berhasil diupdate' . $statusMessage,
                'data' => $mapProof,
                'status_changed' => $oldStatus !== $job->status_job,
                'old_status' => $oldStatus,
                'new_status' => $job->status_job
            ]);
        }

        // Original validation for regular upload
        $request->validate([
            'proof_type' => 'required|in:digital,fisik',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'customer_response' => 'nullable|in:pending,acc,reject',
            'customer_notes' => 'nullable|string',
            'marketing_notes' => 'nullable|string'
        ]);

        $proofData = [
            'job_development_id' => $job->id,
            'proof_type' => $request->proof_type,
            'customer_response' => $request->customer_response ?? 'pending',
            'customer_notes' => $request->customer_notes,
            'marketing_notes' => $request->marketing_notes,
            'created_by' => auth()->id(),
            'status' => $request->mapProofProgress
        ];

        // Handle file upload
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/map_proofs', $filename);
            $proofData['proof_file_path'] = 'map_proofs/' . $filename;
        }

        $mapProof = $job->mapProof()->updateOrCreate(['job_development_id' => $job->id], $proofData);

        // Log the action
        DevelopmentHandlingService::logMapProofUpload($job, $request->proof_type);

        return response()->json([
            'success' => true,
            'message' => 'Map Proof berhasil diupload',
            'data' => $mapProof
        ]);
    }

    public function deleteProofFile($id)
    {
        $job = JobOrderDevelopment::with(['mapProof'])->findOrFail($id);

        if (!$job->mapProof || !$job->mapProof->proof_file_path) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file proof yang bisa dihapus'
            ], 400);
        }

        // Delete file from storage
        if (Storage::exists('public/' . $job->mapProof->proof_file_path)) {
            Storage::delete('public/' . $job->mapProof->proof_file_path);
        }

        // Update database - clear file path
        $job->mapProof->update([
            'proof_file_path' => null
        ]);

        // Log the action
        DevelopmentHandlingService::logMapProofFileDeleted($job);

        return response()->json([
            'success' => true,
            'message' => 'File proof berhasil dihapus'
        ]);
    }

    public function sendToCustomer($id)
    {
        $job = JobOrderDevelopment::with(['mapProof'])->findOrFail($id);

        if (!$job->mapProof) {
            return response()->json([
                'success' => false,
                'message' => 'Map Proof belum diupload'
            ], 400);
        }

        // Update status map proof
        $oldStatus = $job->status_job;
        $job->mapProof->update([
            'status' => 'sent_to_customer',
            'sent_at' => now()
        ]);

        // Kirim notifikasi progress job
        $this->sendProgressJobNotification($job, $oldStatus, 'WAITING_MPP', 'Map Proof berhasil dikirim ke customer');

        // Log the action
        DevelopmentHandlingService::logMapProofSent($job);

        return response()->json([
            'success' => true,
            'message' => 'Map Proof berhasil dikirim ke customer'
        ]);
    }

    // Sales Order Methods
    public function createSalesOrder($id)
    {
        $job = JobOrderDevelopment::with(['salesOrder'])->findOrFail($id);
        return view('main.process.development.sales-order-create', compact('job'));
    }

    public function storeSalesOrder(Request $request, $id)
    {
        $request->validate([
            'order_number' => 'required|string|unique:tb_sales_orders,order_number',
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        $salesOrderData = [
            'job_development_id' => $job->id,
            'order_number' => $request->order_number,
            'order_date' => $request->order_date,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $request->total_price,
            'status' => 'pending',
            'created_by' => auth()->id()
        ];

        $salesOrder = $job->salesOrder()->updateOrCreate(['job_development_id' => $job->id], $salesOrderData);

        // Update status job
        $oldStatus = $job->status_job;
        $job->update(['status_job' => 'SALES_ORDER_CREATED']);

        // Log the action
        DevelopmentHandlingService::logSalesOrderCreated($job, $request->order_number);

        // Kirim notifikasi progress job
        $this->sendProgressJobNotification($job, $oldStatus, 'SALES_ORDER_CREATED', 'Sales Order berhasil dibuat - Order Number: ' . $request->order_number);

        return response()->json([
            'success' => true,
            'message' => 'Sales Order berhasil dibuat',
            'data' => $salesOrder
        ]);
    }

    public function closeDevelopment($id)
    {
        $job = JobOrderDevelopment::findOrFail($id);

        // Validasi bahwa Sales Order sudah dibuat
        if (!$job->salesOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Sales Order belum dibuat. Tidak bisa menutup development item.'
            ], 400);
        }

        // Update status job menjadi COMPLETED
        $oldStatus = $job->status_job;
        $job->update([
            'status_job' => 'COMPLETED',
            'completed_at' => now()
        ]);

        // Log the action
        DevelopmentHandlingService::logDevelopmentClosed($job);

        // Kirim notifikasi progress job
        $this->sendProgressJobNotification($job, $oldStatus, 'COMPLETED', 'Development item berhasil ditutup - Alur development telah selesai');

        return response()->json([
            'success' => true,
            'message' => 'Development item berhasil ditutup. Alur development telah selesai.'
        ]);
    }

    /**
     * Get timeline untuk job development
     */
    public function getJobTimeline($id)
    {
        try {
            $timeline = DevelopmentHandlingService::getJobTimeline($id);

            return response()->json([
                'success' => true,
                'data' => $timeline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting timeline: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report data untuk periode tertentu
     */
    public function getReportData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $actionType = $request->input('action_type');

            $data = DevelopmentHandlingService::getReportData($startDate, $endDate, $actionType);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting report data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update purchasing info for specific material
     */
    public function updatePurchasingInfo(Request $request, $id)
    {
        $request->validate([
            'material_type' => 'required|in:kertas,tinta,foil,pale_tooling',
            'purchasing_status' => 'required|in:belum,sudah',
            'purchasing_info' => 'nullable|string'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        // Get material detail from job
        $materialDetailField = $request->material_type . '_khusus_detail';
        $materialDetail = $job->$materialDetailField ?? 'Ya, menggunakan ' . $request->material_type . ' khusus';

        // Find or create material purchasing record
        $materialPurchasing = MaterialPurchasing::updateOrCreate(
            [
                'job_order_development_id' => $job->id,
                'material_type' => $request->material_type
            ],
            [
                'material_detail' => $materialDetail,
                'purchasing_status' => $request->purchasing_status,
                'purchasing_info' => $request->purchasing_info,
                'updated_by' => auth()->id()
            ]
        );

        // Log the action
        DevelopmentHandlingService::logAction(
            $job->id,
            'update_purchasing_info_' . $request->material_type,
            'Update info purchasing ' . $request->material_type . ' - Status: ' . $request->purchasing_status,
            null,
            null,
            [
                'material_type' => $request->material_type,
                'purchasing_status' => $request->purchasing_status,
                'purchasing_info' => $request->purchasing_info,
                'material_purchasing_id' => $materialPurchasing->id
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Info purchasing ' . $request->material_type . ' berhasil disimpan'
        ]);
    }

    /**
     * Update all purchasing info at once
     */
    public function updateAllPurchasingInfo(Request $request, $id)
    {
        $request->validate([
            'purchasing_data' => 'required|array'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);
        $updatedMaterials = [];

        foreach ($request->purchasing_data as $materialType => $data) {
            if (in_array($materialType, ['kertas', 'tinta', 'foil', 'pale_tooling'])) {
                // Get material detail from job
                $materialDetailField = $materialType . '_khusus_detail';
                $materialDetail = $job->$materialDetailField ?? 'Ya, menggunakan ' . $materialType . ' khusus';

                // Find or create material purchasing record
                $materialPurchasing = MaterialPurchasing::updateOrCreate(
                    [
                        'job_order_development_id' => $job->id,
                        'material_type' => $materialType
                    ],
                    [
                        'material_detail' => $materialDetail,
                        'purchasing_status' => $data['status'],
                        'purchasing_info' => $data['info'],
                        'updated_by' => auth()->id()
                    ]
                );

                $updatedMaterials[] = $materialType;
            }
        }

        if (!empty($updatedMaterials)) {
            // Log the action
            DevelopmentHandlingService::logAction(
                $job->id,
                'update_all_purchasing_info',
                'Update semua info purchasing material khusus: ' . implode(', ', $updatedMaterials),
                null,
                null,
                $request->purchasing_data
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua info purchasing berhasil disimpan'
        ]);
    }

    /**
     * PPIC Form - Comprehensive form untuk production scheduling
     */
    public function ppicForm($id)
    {
        $job = JobOrderDevelopment::with([
            'marketingUser',
            'assignedToPPICBy',
            'prepressJob',
            'meetingOpp1',
            'meetingOpp2',
            'schedulingDevelopment',
            'leadTimeConfiguration',
            'mapProof',
            'salesOrder',
            'materialPurchasing',
            'productionSchedules'
        ])->findOrFail($id);

        // Cek apakah job sudah siap untuk PPIC
        if (!in_array($job->status_job, ['READY_FOR_CUSTOMER', 'MEETING_OPP', 'SCHEDULED_FOR_PRODUCTION'])) {
            return redirect()->back()->with('error', 'Job belum siap untuk PPIC processing');
        }

        // Get available machines from database
        $machines = DB::connection('mysql3')
            ->table('mastermachine')
            ->where('Description', 'not like', '%JANGAN DIPAKAI%')
            ->select(
                'Code as code',
                'Description as name',
                'CapacityPerHour as capacity'
            )
            ->get()
            ->map(function ($machine) {
                return (object) [
                    'code' => $machine->code,
                    'name' => $machine->name,
                    'capacity' => $machine->capacity ?? 'All'
                ];
            });

        // Debug: Log machines data
        Log::info('Machines data for PPIC form:', [
            'count' => $machines->count(),
            'machines' => $machines->toArray()
        ]);

        return view('main.process.development.ppic-form', compact('job', 'machines'));
    }

    /**
     * Store Production Schedule for Specific Process
     */
    public function storeProcessSchedule(Request $request, $id)
    {
        // dd($request->all(), $id);

        $request->validate([
            'process_name' => 'required|string|max:255',
            'process_index' => 'required|integer|min:0',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
            'estimated_duration' => 'required|numeric|min:0.5',
            'machine_assignment' => 'required|string|max:255',
            'schedule_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $job = JobOrderDevelopment::findOrFail($id);
            // dd($job);

            // Cek apakah job sudah siap untuk PPIC
            if (!in_array($job->status_job, ['READY_FOR_CUSTOMER', 'MEETING_OPP', 'SCHEDULED_FOR_PRODUCTION'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job belum siap untuk PPIC processing'
                ], 400);
            }

            // Get machine details from database
            $machine = DB::connection('mysql3')
                ->table('mastermachine')
                ->where('Code', $request->machine_assignment)
                ->select('Code as code', 'Description as name', 'CapacityPerHour as capacity')
                ->first();

            if (!$machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesin tidak ditemukan'
                ], 400);
            }

            // Create production schedule
            $productionSchedule = ProductionSchedule::create([
                'job_order_development_id' => $job->id,
                'production_date' => $request->schedule_date,
                'production_time' => $request->schedule_time,
                'machine_name' => $machine->name,
                'machine_code' => $machine->code,
                'status' => 'scheduled',
                'production_notes' => $request->schedule_notes,
                'created_by' => auth()->id(),
                'proses' => $request->process_name, // Store process name in proses column
                // Store additional process information in notes
                'quality_notes' => "Process Index: {$request->process_index} | Duration: {$request->estimated_duration} hours"
            ]);

            // Update job status if this is the first schedule
            if ($job->status_job !== 'SCHEDULED_FOR_PRODUCTION') {
                $oldStatus = $job->status_job;
                $job->update(['status_job' => 'SCHEDULED_FOR_PRODUCTION']);

                // Log action
                DevelopmentHandlingService::logProductionScheduled(
                    $job->id,
                    $request->schedule_date,
                    $machine->name,
                    $machine->code,
                    'scheduled',
                    $request->schedule_notes
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal produksi berhasil disimpan',
                'data' => [
                    'id' => $productionSchedule->id,
                    'process_name' => $request->process_name,
                    'proses' => $productionSchedule->proses,
                    'schedule_date' => $productionSchedule->production_date->format('Y-m-d'),
                    'schedule_time' => $productionSchedule->production_time->format('H:i'),
                    'machine_name' => $productionSchedule->machine_name,
                    'machine_code' => $productionSchedule->machine_code,
                    'status' => $productionSchedule->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store PPIC Production Schedule
     */
    public function storeProductionSchedule(Request $request, $id)
    {
        $request->validate([
            'production_date' => 'required|date|after_or_equal:today',
            'production_time' => 'required',
            'machine_name' => 'required|string|max:255',
            'machine_code' => 'required|string|max:50',
            'production_notes' => 'nullable|string',
            'quality_notes' => 'nullable|string',
            'production_status' => 'required|in:scheduled,ready,in_progress'
        ]);

        $job = JobOrderDevelopment::findOrFail($id);

        // Cek apakah semua material sudah ready
        $materialNotReady = $job->materialPurchasing()
            ->where('purchasing_status', 'belum')
            ->exists();

        if ($materialNotReady) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menjadwalkan produksi karena masih ada material yang belum ready'
            ], 400);
        }

        $productionSchedule = ProductionSchedule::create([
            'job_order_development_id' => $job->id,
            'production_date' => $request->production_date,
            'production_time' => $request->production_time,
            'machine_name' => $request->machine_name,
            'machine_code' => $request->machine_code,
            'production_notes' => $request->production_notes,
            'quality_notes' => $request->quality_notes,
            'status' => $request->production_status,
            'created_by' => Auth::id()
        ]);

        // Update job status
        $oldStatus = $job->status_job;
        $job->update(['status_job' => 'SCHEDULED_FOR_PRODUCTION']);

        // Kirim notifikasi progress job
        $this->sendProgressJobNotification($job, $oldStatus, 'SCHEDULED_FOR_PRODUCTION', 'Production schedule berhasil dibuat - ' . $request->production_date);

        // Log action
        DevelopmentHandlingService::logProductionScheduled(
            $job->id,
            $request->production_date,
            $request->machine_name,
            $request->machine_code,
            $request->production_status,
            $request->production_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Production schedule berhasil dibuat',
            'data' => $productionSchedule
        ]);
    }

    /**
     * Show Production Schedule for Edit
     */
    public function showProductionSchedule($id, $scheduleId)
    {
        try {
            $schedule = ProductionSchedule::where('job_order_development_id', $id)
                ->findOrFail($scheduleId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $schedule->id,
                    'production_date' => $schedule->production_date->format('Y-m-d'),
                    'production_time' => $schedule->production_time ? $schedule->production_time->format('H:i') : '',
                    'machine_name' => $schedule->machine_name,
                    'machine_code' => $schedule->machine_code,
                    'status' => $schedule->status,
                    'production_notes' => $schedule->production_notes,
                    'quality_notes' => $schedule->quality_notes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update Production Schedule
     */
    public function updateProductionSchedule(Request $request, $id, $scheduleId)
    {
        $request->validate([
            'production_date' => 'required|date|after_or_equal:today',
            'production_time' => 'required',
            'machine_name' => 'required|string|max:255',
            'machine_code' => 'required|string|max:50',
            'production_notes' => 'nullable|string',
            'quality_notes' => 'nullable|string',
            'production_status' => 'required|in:scheduled,ready'
        ]);

        try {
            $schedule = ProductionSchedule::where('job_order_development_id', $id)
                ->findOrFail($scheduleId);

            // Only allow edit if status is 'scheduled'
            if ($schedule->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya schedule dengan status "scheduled" yang dapat diedit'
                ], 400);
            }

            $oldData = $schedule->toArray();

            $schedule->update([
                'production_date' => $request->production_date,
                'production_time' => $request->production_time,
                'machine_name' => $request->machine_name,
                'machine_code' => $request->machine_code,
                'production_notes' => $request->production_notes,
                'quality_notes' => $request->quality_notes,
                'status' => $request->production_status
            ]);

            // Log action
            $job = JobOrderDevelopment::findOrFail($id);
            DevelopmentHandlingService::logProductionScheduleUpdated(
                $job->id,
                $schedule->id,
                $oldData,
                $schedule->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => 'Production schedule berhasil diupdate',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate schedule'
            ], 500);
        }
    }

    /**
     * Update Production Schedule Status
     */
    public function updateProductionScheduleStatus(Request $request, $id, $scheduleId)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'quality_notes' => 'nullable|string'
        ]);

        $schedule = ProductionSchedule::where('job_order_development_id', $id)
            ->findOrFail($scheduleId);

        $oldStatus = $schedule->status;
        $schedule->update([
            'status' => $request->status,
            'quality_notes' => $request->quality_notes
        ]);

        // Update job status berdasarkan production status
        $job = JobOrderDevelopment::findOrFail($id);
        if ($request->status === 'completed') {
            // Cek apakah SEMUA production schedules sudah completed
            $allSchedules = ProductionSchedule::where('job_order_development_id', $id)->get();
            $allCompleted = $allSchedules->every(function ($schedule) {
                return $schedule->status === 'completed';
            });

            // Hanya update status job jika SEMUA proses produksi selesai
            if ($allCompleted) {
                $job->update(['status_job' => 'PRODUCTION_COMPLETED']);
            } else {
                // Jika belum semua selesai, tetap di status production
                $job->update(['status_job' => 'IN_PRODUCTION']);
            }
        } elseif ($request->status === 'in_progress') {
            $job->update(['status_job' => 'IN_PRODUCTION']);
        }

        // Log action
        DevelopmentHandlingService::logProductionStatusUpdate(
            $job->id,
            $oldStatus,
            $request->status,
            $request->quality_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Production status berhasil diupdate'
        ]);
    }

    /**
     * Test MySQL3 Connection
     */
    public function testMysql3Connection()
    {
        // try {
        //     // Test connection dan lihat struktur tabel
        //     $machines = DB::connection('mysql3')
        //         ->table('mastermachine')
        //         ->select('*')
        //         ->limit(3)
        //         ->get();

        //     // Cek kolom yang tersedia
        //     $columns = DB::connection('mysql3')
        //         ->getSchemaBuilder()
        //         ->getColumnListing('mastermachine');

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'MySQL3 connection successful',
        //         'columns' => $columns,
        //         'sample_data' => $machines
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'MySQL3 connection failed: ' . $e->getMessage(),
        //         'error_details' => [
        //             'file' => $e->getFile(),
        //             'line' => $e->getLine(),
        //             'trace' => $e->getTraceAsString()
        //         ]
        //     ]);
        // }
    }

    /**
     * Get Available Machines
     */
    public function getAvailableMachines(Request $request)
    {

        $machines = DB::connection('mysql3')
            ->table('mastermachine')
            ->where('Description', 'not like', '%JANGAN DIPAKAI%')
            ->select(
                'Code as code',
                'Description as name',
                'CapacityPerHour as capacity'
            )
            ->get()
            ->map(function ($machine) {
                return [
                    'code' => $machine->code,
                    'name' => $machine->name,
                    'capacity' => $machine->capacity ?? 'All'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $machines->values()
        ]);
    }

    /**
     * Production Report View
     */
    public function productionReport($id)
    {
        $job = JobOrderDevelopment::with([
            'productionSchedules.createdBy',
            'productionSchedules.rndApprovedBy',
            'marketingUser',
            'assignedToPPICBy'
        ])->findOrFail($id);

        // If requesting specific schedule data for revision
        if (request()->has('schedule_id')) {
            $scheduleId = request()->get('schedule_id');
            $schedule = $job->productionSchedules()->find($scheduleId);

            if ($schedule) {
                return response()->json([
                    'success' => true,
                    'schedule' => $schedule
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }
        }

        return view('main.process.development.production-report', compact('job'));
    }

    /**
     * Store Production Report
     */
    public function storeProductionReport(Request $request, $id)
    {
        $request->validate([
            'schedule_id' => 'required|exists:tb_production_schedules,id',
            'production_status' => 'required|in:completed,cancelled',
            'completion_date' => 'nullable|date',
            'production_qty' => 'nullable|integer|min:0',
            'reject_qty' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'production_notes' => 'nullable|string',
            'quality_notes' => 'nullable|string',
            'issues_found' => 'nullable|string',
            'recommendations' => 'nullable|string'
        ]);

        $schedule = ProductionSchedule::findOrFail($request->schedule_id);

        // Update production schedule with results
        $schedule->update([
            'status' => $request->production_status,
            'production_notes' => $request->production_notes,
            'quality_notes' => $request->quality_notes,
            'production_qty' => $request->production_qty,
            'reject_qty' => $request->reject_qty,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'completion_date' => $request->completion_date,
            'issues_found' => $request->issues_found,
            'recommendations' => $request->recommendations,
            'rnd_approval_status' => 'pending', // Reset to pending for RnD approval
            'revision_count' => $schedule->revision_count + 1
        ]);


        // Update job status based on production status
        $job = JobOrderDevelopment::findOrFail($id);
        $oldStatus = $job->status_job;
        if ($request->production_status === 'completed') {
            // Cek apakah SEMUA production schedules sudah completed
            $allSchedules = ProductionSchedule::where('job_order_development_id', $id)->get();
            $allCompleted = $allSchedules->every(function ($schedule) {
                return $schedule->status === 'completed';
            });

            // Hanya update status job jika SEMUA proses produksi selesai
            if ($allCompleted) {
                $job->update(['status_job' => 'PRODUCTION_COMPLETED']);
            } else {
                // Jika belum semua selesai, tetap di status production
                $job->update(['status_job' => 'IN_PRODUCTION']);
            }
        } else {
            $job->update(['status_job' => 'PRODUCTION_CANCELLED']);
        }

        // Kirim notifikasi progress job
        $this->sendProgressJobNotification($job, $oldStatus, 'PRODUCTION_COMPLETED', 'Production report berhasil disimpan - ' . $request->production_status);

        // Log action
        DevelopmentHandlingService::logProductionStatusUpdate(
            $job->id,
            $schedule->id,
            $request->production_status,
            $request->production_notes,
            $request->quality_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Production report berhasil disimpan'
        ]);
    }

    /**
     * RnD Approve Production Report
     */
    public function rndApproveProductionReport(Request $request, $id, $scheduleId)
    {
        $request->validate([
            'rnd_approval_status' => 'required|in:approved,rejected',
            'rnd_approval_notes' => 'nullable|string'
        ]);

        $schedule = ProductionSchedule::where('job_order_development_id', $id)
            ->findOrFail($scheduleId);

        $schedule->update([
            'rnd_approval_status' => $request->rnd_approval_status,
            'rnd_approval_notes' => $request->rnd_approval_notes,
            'rnd_approved_by' => Auth::id(),
            'rnd_approved_at' => now()
        ]);

        // Update job status based on RnD approval
        $job = JobOrderDevelopment::findOrFail($id);
        if ($request->rnd_approval_status === 'approved') {
            // Cek apakah SEMUA production schedules sudah completed
            $allSchedules = ProductionSchedule::where('job_order_development_id', $id)->get();
            $allCompleted = $allSchedules->every(function ($schedule) {
                return $schedule->status === 'completed';
            });

            // Cek apakah SEMUA production schedules sudah di-approve RnD
            $allApproved = $allSchedules->every(function ($schedule) {
                return $schedule->rnd_approval_status === 'approved';
            });

            // Hanya update status job jika SEMUA proses produksi selesai DAN di-approve
            if ($allCompleted && $allApproved) {
                $job->update(['status_job' => 'PRODUCTION_APPROVED_BY_RND']);
            }
            // Jika belum semua di-approve, status job tetap tidak berubah
        } else {
            $job->update(['status_job' => 'PRODUCTION_REJECTED_BY_RND']);
        }

        // Log action
        DevelopmentHandlingService::logRndProductionApproval(
            $job->id,
            $scheduleId,
            $request->rnd_approval_status,
            $request->rnd_approval_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'RnD approval berhasil disimpan'
        ]);
    }

    /**
     * Revise Production Report (for Production team)
     */
    public function reviseProductionReport(Request $request, $id, $scheduleId)
    {
        $schedule = ProductionSchedule::where('job_order_development_id', $id)
            ->findOrFail($scheduleId);

        // Check if can be revised
        if (!$schedule->canBeRevised()) {
            return response()->json([
                'success' => false,
                'message' => 'Production report sudah disetujui RnD, tidak dapat direvisi'
            ], 400);
        }

        $request->validate([
            'production_status' => 'required|in:completed,cancelled',
            'completion_date' => 'nullable|date',
            'production_qty' => 'nullable|integer|min:0',
            'reject_qty' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'production_notes' => 'nullable|string',
            'quality_notes' => 'nullable|string',
            'issues_found' => 'nullable|string',
            'recommendations' => 'nullable|string'
        ]);

        // Update production schedule with revised results
        $schedule->update([
            'status' => $request->production_status,
            'production_notes' => $request->production_notes,
            'quality_notes' => $request->quality_notes,
            'production_qty' => $request->production_qty,
            'reject_qty' => $request->reject_qty,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'completion_date' => $request->completion_date,
            'issues_found' => $request->issues_found,
            'recommendations' => $request->recommendations,
            'rnd_approval_status' => 'pending', // Reset to pending for RnD approval
            'revision_count' => $schedule->revision_count + 1
        ]);

        // Log revision
        $job = JobOrderDevelopment::findOrFail($id);
        DevelopmentHandlingService::logProductionRevision(
            $job->id,
            $scheduleId,
            $request->production_notes,
            $request->quality_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Production report berhasil direvisi'
        ]);
    }

    /**
     * RnD Production Approval View
     */
    public function rndProductionApproval($id)
    {
        $job = JobOrderDevelopment::with([
            'productionSchedules.createdBy',
            'productionSchedules.rndApprovedBy',
            'marketingUser',
            'assignedToPPICBy'
        ])->findOrFail($id);

        return view('main.process.development.rnd-production-approval', compact('job'));
    }

    /**
     * RnD Approve Production Report (AJAX)
     */
    public function rndApproveProductionReportAjax(Request $request, $id, $scheduleId)
    {
        $request->validate([
            'rnd_approval_status' => 'required|in:approved,rejected',
            'rnd_approval_notes' => 'nullable|string'
        ]);

        $schedule = ProductionSchedule::where('job_order_development_id', $id)
            ->findOrFail($scheduleId);

        $schedule->update([
            'rnd_approval_status' => $request->rnd_approval_status,
            'rnd_approval_notes' => $request->rnd_approval_notes,
            'rnd_approved_by' => Auth::id(),
            'rnd_approved_at' => now()
        ]);

        // Update job status based on RnD approval
        $job = JobOrderDevelopment::findOrFail($id);

        if ($request->rnd_approval_status === 'approved') {
            // Cek apakah SEMUA production schedules sudah completed
            $allSchedules = ProductionSchedule::where('job_order_development_id', $id)->get();
            $allCompleted = $allSchedules->every(function ($schedule) {
                return $schedule->status === 'completed';
            });

            // Cek apakah SEMUA production schedules sudah di-approve RnD
            $allApproved = $allSchedules->every(function ($schedule) {
                return $schedule->rnd_approval_status === 'approved';
            });

            // Debug logging
            Log::info('RnD Approval Debug', [
                'job_id' => $id,
                'schedule_id' => $scheduleId,
                'total_schedules' => $allSchedules->count(),
                'all_completed' => $allCompleted,
                'all_approved' => $allApproved,
                'schedules_status' => $allSchedules->map(function($s) {
                    return [
                        'id' => $s->id,
                        'status' => $s->status,
                        'rnd_approval_status' => $s->rnd_approval_status
                    ];
                })
            ]);

            // Hanya update status job jika SEMUA proses produksi selesai DAN di-approve
            if ($allCompleted && $allApproved) {
                $job->update(['status_job' => 'PRODUCTION_APPROVED_BY_RND']);

                // Auto-create Map Proof entry if not exists
                if (!$job->mapProof) {
                    $job->mapProof()->create([
                        'job_development_id' => $job->id,
                        'proof_type' => 'digital', // Default to digital
                        'status' => 'ready_to_send',
                        'created_by' => auth()->id()
                    ]);
                }

                Log::info('Job status updated to PRODUCTION_APPROVED_BY_RND', ['job_id' => $id]);
            } else {
                Log::info('Job status NOT updated - waiting for all schedules', [
                    'job_id' => $id,
                    'all_completed' => $allCompleted,
                    'all_approved' => $allApproved
                ]);
            }
        } else {
            // Jika reject, langsung update status
            $job->update(['status_job' => 'PRODUCTION_REJECTED_BY_RND']);
        }

        // Log action
        DevelopmentHandlingService::logRndProductionApproval(
            $job->id,
            $scheduleId,
            $request->rnd_approval_status,
            $request->rnd_approval_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'RnD approval berhasil disimpan'
        ]);
    }

    public function addProses(Request $request)
    {
        try {
            $request->validate([
                'urutan_proses' => 'required|integer|min:1|max:20',
                'department_responsible' => 'required|string|max:100',
                'proses_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'expected_days' => 'nullable|integer|min:1|max:30',
                'is_required' => 'required|in:0,1'
            ]);

            // Check if urutan already exists
            $existingProses = MasterProsesDevelopment::where('urutan_proses', $request->urutan_proses)->first();
            if ($existingProses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Urutan proses ' . $request->urutan_proses . ' sudah ada. Silakan pilih urutan yang berbeda.'
                ], 400);
            }

            // Create new proses
            $proses = MasterProsesDevelopment::create([
                'proses_name' => $request->proses_name,
                'urutan_proses' => $request->urutan_proses,
                'department_responsible' => $request->department_responsible,
                'status_proses' => 'pending',
                'notes' => $request->description,
                'expected_days' => $request->expected_days,
                'is_required' => $request->is_required
            ]);

            Log::info('New master proses added: ' . $proses->proses_name);

            return response()->json([
                'success' => true,
                'message' => 'Proses berhasil ditambahkan!',
                'data' => $proses
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error adding master proses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding proses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProses($id)
    {
        try {
            $proses = MasterProsesDevelopment::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $proses
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting proses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting proses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProses(Request $request)
    {
        try {
            $request->validate([
                'proses_id' => 'required|exists:tb_master_proses_developments,id',
                'urutan_proses' => 'required|integer|min:1|max:20',
                'department_responsible' => 'required|string|max:100',
                'proses_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'expected_days' => 'nullable|integer|min:1|max:30',
                'is_required' => 'required|in:0,1'
            ]);

            $proses = MasterProsesDevelopment::findOrFail($request->proses_id);

            // Check if urutan already exists (excluding current proses)
            $existingProses = MasterProsesDevelopment::where('urutan_proses', $request->urutan_proses)
                ->where('id', '!=', $request->proses_id)
                ->first();
            if ($existingProses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Urutan proses ' . $request->urutan_proses . ' sudah ada. Silakan pilih urutan yang berbeda.'
                ], 400);
            }

            // Update proses
            $proses->update([
                'proses_name' => $request->proses_name,
                'urutan_proses' => $request->urutan_proses,
                'department_responsible' => $request->department_responsible,
                'notes' => $request->description,
                'expected_days' => $request->expected_days,
                'is_required' => $request->is_required
            ]);

            Log::info('Master proses updated: ' . $proses->proses_name);

            return response()->json([
                'success' => true,
                'message' => 'Proses berhasil diupdate!',
                'data' => $proses
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating master proses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating proses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProses(Request $request)
    {
        try {
            $request->validate([
                'proses_id' => 'required|exists:tb_master_proses_developments,id'
            ]);

            $proses = MasterProsesDevelopment::findOrFail($request->proses_id);
            $prosesName = $proses->proses_name;

            // Check if proses is being used in any job development
            $usedInJobs = MasterProsesDevelopment::where('proses_name', $proses->proses_name)
                ->where('job_order_development_id', '!=', 0)
                ->count();

            if ($usedInJobs > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses ini sedang digunakan dalam ' . $usedInJobs . ' job development. Tidak dapat dihapus.'
                ], 400);
            }

            // Delete proses
            $proses->delete();

            Log::info('Master proses deleted: ' . $prosesName);

            return response()->json([
                'success' => true,
                'message' => 'Proses berhasil dihapus!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error deleting master proses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting proses: ' . $e->getMessage()
            ], 500);
        }
    }

    // Return to Prepress Method
    public function returnToPrepress(Request $request, $id)
    {
        $request->validate([
            'return_to_prepress_notes' => 'required|string|max:1000',
            'revision_priority' => 'required|in:urgent,normal,low'
        ]);

        $job = JobOrderDevelopment::with(['meetingOpp1'])->findOrFail($id);

        if (!$job->meetingOpp1 || $job->meetingOpp1->customer_response !== 'reject') {
            return response()->json([
                'success' => false,
                'message' => 'Job tidak dapat dikembalikan ke Prepress. Pastikan customer telah reject Meeting OPP 1.'
            ], 400);
        }

        if ($job->meetingOpp1->returned_to_prepress) {
            return response()->json([
                'success' => false,
                'message' => 'Job sudah dikembalikan ke Prepress sebelumnya.'
            ], 400);
        }

        $job->meetingOpp1->update([
            'returned_to_prepress' => true,
            'returned_to_prepress_at' => now(),
            'return_to_prepress_notes' => $request->return_to_prepress_notes,
            'revision_priority' => $request->revision_priority
        ]);

        $job->update([
            'status_job' => 'IN_PROGRESS_PREPRESS' // Keep original status
        ]);

        // Parse job_order untuk create prepress job (sama seperti sendJobToPrepress)
        $jobOrders = $job->job_order ? (is_string($job->job_order) ? json_decode($job->job_order, true) : $job->job_order) : [];
        $createdPrepressJobs = [];

        if (empty($jobOrders)) {
            // Jika tidak ada job_order, create single prepress job
            $jenisPekerjaan = 'General Job';
            $unitJob = 'General Unit';
            $est_job_default = null;

            $prepressJob = JobPrepress::create([
                'nomor_job_order' => $job->job_code . '-REV-' . now()->format('YmdHis'),
                'tanggal_job_order' => now(),
                'tanggal_deadline' => $this->calculateRevisionDeadline($request->revision_priority),
                'customer' => $job->customer,
                'product' => $job->product,
                'kode_design' => $job->kode_design ?? '',
                'dimension' => $job->dimension ?? '',
                'material' => $job->material ?? '',
                'total_color' => $job->total_color ?? '',
                'total_color_details' => $job->total_color_details ?? '',
                'qty_order_estimation' => $job->qty_order_estimation ?? '',
                'job_order' => $jenisPekerjaan, // jenis_pekerjaan masuk ke job_order
                'sub_unit_job' => $unitJob, // unit_job masuk ke sub_unit_job
                'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                'created_by' => auth()->user()->name ?? 'System',
                'job_title' => 'DEVELOPMENT - ' . $job->job_code . ' - ' . $jenisPekerjaan . ' - ' . $job->product . ' (Revisi)',
                'status_job' => 'OPEN',
                'prioritas_job' => $request->revision_priority === 'urgent' ? 'Urgent' : 'Normal',
                'est_job_default' => $est_job_default,
                'catatan' => $request->return_to_prepress_notes,
                'kategori_job' => 'REVISION',
                'catatan_job' => $request->return_to_prepress_notes,
                'assigned_from_development' => true,
                'development_job_id' => $job->id,
                'is_revision' => true,
                'original_job_id' => $job->id,
                'revision_notes' => $request->return_to_prepress_notes,
                'revision_priority' => $request->revision_priority,
                'revision_requested_at' => now()
            ]);
            $createdPrepressJobs[] = $prepressJob;
        } else {
            // Create multiple prepress jobs berdasarkan job_order (sama seperti sendJobToPrepress)
            $jobCount = count($jobOrders);

            foreach ($jobOrders as $index => $jobOrderItem) {
                $jobNumber = $index + 1;
                $nomorJobOrder = $jobCount > 1 ? $job->job_code . '-REV-' . now()->format('YmdHis') . '_' . $jobNumber : $job->job_code . '-REV-' . now()->format('YmdHis');

                // Extract jenis_pekerjaan dan unit_job dari jobOrderItem
                $jenisPekerjaan = $jobOrderItem['jenis_pekerjaan'] ?? '';
                $unitJob = $jobOrderItem['unit_job'] ?? '';

                $job_title = 'DEVELOPMENT - ' . $job->job_code . ' - ' . $jenisPekerjaan . ' - ' . $job->product . ' (Revisi)';

                // Hitung est_job_default
                $est_job_default = null;
                if ($jenisPekerjaan) {
                    $jenisPekerjaanModel = JenisPekerjaanPrepress::where('nama_jenis', $jenisPekerjaan)->first();
                    if ($jenisPekerjaanModel) {
                        $masterData = MasterDataPrepress::where('job', $jenisPekerjaanModel->id)->where('unit_job', $unitJob)->first();
                        if ($masterData) {
                            $est_job_default = $masterData->waktu_job;
                        }
                    }
                }

                $prepressJob = JobPrepress::create([
                    'nomor_job_order' => $nomorJobOrder,
                    'tanggal_job_order' => now(),
                    'tanggal_deadline' => $this->calculateRevisionDeadline($request->revision_priority),
                    'customer' => $job->customer,
                    'product' => $job->product,
                    'kode_design' => $job->kode_design ?? '',
                    'dimension' => $job->dimension ?? '',
                    'material' => $job->material ?? '',
                    'total_color' => $job->total_color ?? '',
                    'total_color_details' => $job->total_color_details ?? '',
                    'qty_order_estimation' => $job->qty_order_estimation ?? '',
                    'job_order' => $jenisPekerjaan, // jenis_pekerjaan masuk ke job_order
                    'sub_unit_job' => $unitJob, // unit_job masuk ke sub_unit_job
                    'file_data' => $job->file_data ? json_encode($job->file_data) : null,
                    'created_by' => auth()->user()->name ?? 'System',
                    'job_title' => $job_title,
                    'status_job' => 'OPEN',
                    'prioritas_job' => $request->revision_priority === 'urgent' ? 'Urgent' : 'Normal',
                    'est_job_default' => $est_job_default,
                    'catatan' => $request->return_to_prepress_notes,
                    'kategori_job' => 'REVISION',
                    'catatan_job' => $request->return_to_prepress_notes,
                    'assigned_from_development' => true,
                    'development_job_id' => $job->id,
                    'is_revision' => true,
                    'original_job_id' => $job->id,
                    'revision_notes' => $request->return_to_prepress_notes,
                    'revision_priority' => $request->revision_priority,
                    'revision_requested_at' => now()
                ]);

                $createdPrepressJobs[] = $prepressJob;
            }
        }

        // Log the action untuk setiap prepress job
        foreach ($createdPrepressJobs as $prepressJob) {
            DevelopmentHandlingService::logAction(
                $job->id,
                'return_to_prepress',
                'Job dikembalikan ke Prepress untuk revisi - Prioritas: ' . ucfirst($request->revision_priority) . ' - Prepress Job: ' . $prepressJob->nomor_job_order,
                null,
                null,
                [
                    'return_to_prepress_notes' => $request->return_to_prepress_notes,
                    'revision_priority' => $request->revision_priority,
                    'returned_at' => now()->format('Y-m-d H:i:s'),
                    'prepress_job_id' => $prepressJob->id,
                    'prepress_job_code' => $prepressJob->nomor_job_order
                ]
            );
        }

        // Prepare response message
        $jobCount = count($createdPrepressJobs);
        $message = $jobCount > 1
            ? "Job berhasil dikembalikan ke Prepress untuk revisi dengan {$jobCount} sub-job"
            : "Job berhasil dikembalikan ke Prepress untuk revisi";

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'status_job' => $job->status_job,
                'returned_at' => $job->meetingOpp1->returned_to_prepress_at ? $job->meetingOpp1->returned_to_prepress_at->format('Y-m-d H:i:s') : null,
                'revision_priority' => $job->meetingOpp1->revision_priority,
                'prepress_jobs' => $createdPrepressJobs,
                'job_count' => $jobCount,
                'prepress_job_codes' => collect($createdPrepressJobs)->pluck('nomor_job_order')->toArray()
            ]
        ]);
    }

    /**
     * Calculate revision deadline based on priority
     */
    private function calculateRevisionDeadline($priority)
    {
        switch ($priority) {
            case 'urgent':
                return now()->addDays(1)->format('Y-m-d H:i:s'); // 1 hari untuk urgent
            case 'normal':
                return now()->addDays(3)->format('Y-m-d H:i:s'); // 3 hari untuk normal
            case 'low':
                return now()->addDays(7)->format('Y-m-d H:i:s'); // 1 minggu untuk low priority
            default:
                return now()->addDays(3)->format('Y-m-d H:i:s'); // 3 hari default
        }
    }

    /**
     * Preview PIC Prepress email template
     */
    public function previewPicPrepressEmailTemplate(Request $request)
    {
        $type = $request->get('type', 'H-3'); // 'H-3', 'H-2', 'H-1'

        // Sample data for preview
        $setting = (object) [
            'process_name' => 'PIC Prepress Reminder',
            'description' => 'Notifikasi reminder untuk PIC Prepress'
        ];

        $reminder = [
            'days' => $type === 'H-3' ? '3' : ($type === 'H-2' ? '2' : '1'),
            'description' => $type . ' Reminder'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250912-0001',
            'job_name' => 'Sample Job Name',
            'customer' => 'PT. Sample Customer',
            'product' => 'Sample Product',
            'qty_order_estimation' => 1000,
            'prioritas_job' => 'Urgent',
            'prepress_deadline' => now()->addDays($type === 'H-3' ? 3 : ($type === 'H-2' ? 2 : 1))->format('Y-m-d'),
            'catatan' => 'Sample catatan untuk job prepress'
        ];

        $additionalData = [
            'notification_type' => 'pic_prepress_reminder',
            'reminder_type' => $type,
            'action_text' => 'Lihat Job Prepress'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Sample PIC Prepress',
            'email' => 'pic@example.com'
        ];

        return view('emails.pic-prepress-reminder', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview Finish Prepress email template
     */
    public function previewFinishPrepressEmailTemplate(Request $request)
    {
        // Sample data for preview
        $setting = (object) [
            'process_name' => 'Job Prepress Selesai',
            'description' => 'Notifikasi ketika job prepress selesai dikerjakan'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Job Prepress Selesai'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250912-0001',
            'job_name' => 'Sample Job Name',
            'customer' => 'PT. Sample Customer',
            'product' => 'Sample Product',
            'qty_order_estimation' => 1000,
            'prioritas_job' => 'Urgent',
            'finished_at' => now()->format('Y-m-d H:i:s'),
            'pic_name' => 'John Doe (PIC Prepress)',
            'pic_email' => 'john.doe@krisanthium.com',
            'catatan' => 'Job prepress telah selesai dengan baik. Semua file sudah siap untuk tahap selanjutnya.'
        ];

        $additionalData = [
            'notification_type' => 'finish_prepress',
            'action_text' => 'Lihat Job Prepress'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        return view('emails.finish-prepress-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview email template untuk proses produksi
     */
    public function previewProsesProduksiEmailTemplate(Request $request)
    {
        $setting = (object) [
            'process_name' => 'Proses Produksi',
            'description' => 'Notifikasi untuk proses produksi berdasarkan lead time configuration'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Lead Time Configuration Disimpan'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250912-0001',
            'job_name' => 'Sample Job Name',
            'customer' => 'PT. Sample Customer',
            'product' => 'Sample Product',
            'qty_order_estimation' => (float)1000,
            'total_lead_time_days' => 44,
            'max_lead_time_days' => 30,
            'produksi_hours' => 8.5,
            'lead_time_started_at' => now()->format('d/m/Y H:i'),
            'production_deadline' => now()->addDays(44)->format('d/m/Y H:i'),
            'days_left' => 44,
            'materials' => [
                ['name' => 'Tinta & Material', 'days' => 30],
                ['name' => 'Kertas Baru', 'days' => 25],
                ['name' => 'Foil', 'days' => 20]
            ]
        ];

        $additionalData = [
            'notification_type' => 'proses_produksi',
            'action_text' => 'Lihat Detail Job'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        return view('emails.proses-produksi-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview email template untuk job deadline fulltime
     */
    public function previewJobDeadlineFulltimeEmailTemplate(Request $request)
    {
        $setting = (object) [
            'process_name' => 'Job Deadline Fulltime',
            'description' => 'Notifikasi reminder deadline job berdasarkan tanggal job_deadline'
        ];

        $reminder = [
            'days' => '5',
            'description' => 'H-5 Deadline'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250912-0001',
            'job_name' => 'Sample Job Name',
            'customer' => 'PT. Sample Customer',
            'product' => 'Sample Product',
            'qty_order_estimation' => (float)1000,
            'job_deadline' => now()->addDays(5)->format('d/m/Y'),
            'days_left' => 5,
            'status_job' => 'IN_PROGRESS_PREPRESS',
            'progress_percentage' => 50,
            'last_updated' => now()->format('d/m/Y H:i')
        ];

        $additionalData = [
            'notification_type' => 'job_deadline_fulltime',
            'action_text' => 'Lihat Detail Job'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        return view('emails.job-deadline-fulltime-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview email template untuk progress job
     */
    public function previewProgressJobEmailTemplate(Request $request)
    {
        $setting = (object) [
            'process_name' => 'Progress Job',
            'description' => 'Notifikasi setiap perpindahan status/progress job development'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Status Job Berubah'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250912-0001',
            'job_name' => 'Sample Job Name',
            'customer' => 'PT. Sample Customer',
            'product' => 'Sample Product',
            'qty_order_estimation' => (float)1000,
            'status_before' => 'IN_PROGRESS',
            'status_after' => 'IN_PROGRESS_PREPRESS',
            'change_time' => now()->format('d/m/Y H:i'),
            'changed_by' => 'John Doe (PPIC)',
            'progress_percentage' => 50,
            'job_deadline' => now()->addDays(10)->format('d/m/Y'),
            'days_left' => 10,
            'action_description' => 'Job telah dikirim ke tahap prepress untuk proses selanjutnya',
            'notes' => 'File design sudah siap dan telah disetujui oleh customer'
        ];

        $additionalData = [
            'notification_type' => 'progress_job',
            'action_text' => 'Lihat Detail Job'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        return view('emails.progress-job-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview email template untuk progress job terstruktur (seperti form CIR)
     */
    public function previewProgressJobStructuredEmailTemplate(Request $request)
    {
        $setting = (object) [
            'process_name' => 'Progress Job',
            'description' => 'Notifikasi setiap perpindahan status/progress job development'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Status Job Berubah'
        ];

        // Sample production schedules data
        $productionSchedules = [
            [
                'proses' => 'Printing',
                'status' => 'completed',
                'status_label' => 'Completed',
                'rnd_approval_status' => 'approved',
                'rnd_approval_status_label' => 'Approved',
                'production_date_time' => '19/09/2025 08:00',
                'deadline' => '20/09/2025',
                'days_difference' => -1
            ],
            [
                'proses' => 'Cutting',
                'status' => 'completed',
                'status_label' => 'Completed',
                'rnd_approval_status' => 'pending',
                'rnd_approval_status_label' => 'Pending',
                'production_date_time' => '20/09/2025 10:00',
                'deadline' => '21/09/2025',
                'days_difference' => 0
            ],
            [
                'proses' => 'Folding',
                'status' => 'in_progress',
                'status_label' => 'In Progress',
                'rnd_approval_status' => 'pending',
                'rnd_approval_status_label' => 'Pending',
                'production_date_time' => '21/09/2025 14:00',
                'deadline' => '22/09/2025',
                'days_difference' => 1
            ],
            [
                'proses' => 'Packaging',
                'status' => 'pending',
                'status_label' => 'Pending',
                'rnd_approval_status' => 'pending',
                'rnd_approval_status_label' => 'Pending',
                'production_date_time' => '-',
                'deadline' => '23/09/2025',
                'days_difference' => 2
            ]
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250919-0001',
            'job_name' => 'TEST - PT. Asia Prima Konjac',
            'customer' => 'PT. Asia Prima Konjac',
            'product' => 'TEST',
            'qty_order_estimation' => (float)1000,
            'status_before' => 'IN_PROGRESS',
            'status_after' => 'IN_PROGRESS_PRODUCTION',
            'status_after_desc' => 'In Progress Production',
            'change_time' => now()->format('d/m/Y H:i'),
            'changed_by' => 'John Doe (PPIC)',
            'progress_percentage' => 65,
            'job_deadline' => now()->addDays(5)->format('d/m/Y'),
            'days_left' => 5,
            'action_description' => 'Job telah masuk ke tahap produksi',
            'notes' => 'Semua material sudah siap dan production schedule sudah dibuat',
            'production_schedules' => $productionSchedules
        ];

        $additionalData = [
            'notification_type' => 'progress_job_structured',
            'action_text' => 'Lihat Detail Job',
            'notes' => 'Silakan periksa status produksi dan lakukan approval sesuai jadwal'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        return view('emails.development.progress-job-structured-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser'
        ));
    }

    /**
     * Preview email template untuk input awal development dengan format terstruktur
     */
    public function previewInputAwalStructuredEmailTemplate(Request $request)
    {
        $setting = (object) [
            'process_name' => 'Input Awal Development',
            'description' => 'Notifikasi input awal development job'
        ];

        $reminder = [
            'days' => 'first',
            'description' => 'Job Development Baru'
        ];

        // Sample data untuk input awal
        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250919-0001',
            'job_name' => 'TEST - PT. Asia Prima Konjac',
            'customer' => 'PT. Asia Prima Konjac',
            'product' => 'TEST',
            'kode_design' => 'TEST-001',
            'dimension' => '20x30 cm',
            'material' => 'Art Paper 150gsm',
            'total_color' => '4',
            'qty_order_estimation' => (float)1000,
            'job_type' => 'new',
            'prioritas_job' => 'Normal',
            'tanggal' => now()->format('Y-m-d'),
            'job_deadline' => now()->addDays(30)->format('Y-m-d'),
            'catatan' => 'Test job untuk preview email template input awal',
            'kertas_khusus' => true,
            'kertas_khusus_detail' => 'Art Paper 200gsm dengan finishing glossy',
            'tinta_khusus' => false,
            'tinta_khusus_detail' => null,
            'foil_khusus' => true,
            'foil_khusus_detail' => 'Gold foil untuk logo',
            'pale_tooling_khusus' => false,
            'pale_tooling_khusus_detail' => null,
            'status_after' => 'INPUT_AWAL',
            'status_after_desc' => 'Input Awal',
            'progress_percentage' => 0,
            'job_deadline' => now()->addDays(30)->format('d/m/Y'),
            'days_left' => 30,
            'production_schedules' => [
                [
                    'proses' => 'Printing',
                    'status' => 'pending',
                    'status_label' => 'Pending',
                    'rnd_approval_status' => 'pending',
                    'rnd_approval_status_label' => 'Pending',
                    'production_date_time' => '-',
                    'deadline' => now()->addDays(5)->format('d/m/Y'),
                    'days_difference' => 5
                ],
                [
                    'proses' => 'Cutting',
                    'status' => 'pending',
                    'status_label' => 'Pending',
                    'rnd_approval_status' => 'pending',
                    'rnd_approval_status_label' => 'Pending',
                    'production_date_time' => '-',
                    'deadline' => now()->addDays(7)->format('d/m/Y'),
                    'days_difference' => 7
                ],
                [
                    'proses' => 'Folding',
                    'status' => 'pending',
                    'status_label' => 'Pending',
                    'rnd_approval_status' => 'pending',
                    'rnd_approval_status_label' => 'Pending',
                    'production_date_time' => '-',
                    'deadline' => now()->addDays(10)->format('d/m/Y'),
                    'days_difference' => 10
                ],
                [
                    'proses' => 'Packaging',
                    'status' => 'pending',
                    'status_label' => 'Pending',
                    'rnd_approval_status' => 'pending',
                    'rnd_approval_status_label' => 'Pending',
                    'production_date_time' => '-',
                    'deadline' => now()->addDays(12)->format('d/m/Y'),
                    'days_difference' => 12
                ]
            ]
        ];

        $additionalData = [
            'notification_type' => 'input_awal_structured',
            'action_text' => 'Lihat Detail Job'
        ];

        $currentUser = (object) [
            'id' => 1,
            'name' => 'Marketing Team',
            'email' => 'marketing@example.com'
        ];

        $recipient = (object) [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];

        return view('emails.development.progress-job-structured-notification', compact(
            'setting', 'reminder', 'jobData', 'additionalData', 'currentUser', 'recipient'
        ));
    }

    /**
     * Kirim notifikasi proses produksi ketika lead time configuration disimpan
     */
    private function sendProsesProduksiNotification($job, $leadTime)
    {
        try {
            $emailService = new DevelopmentEmailNotificationService();

            // Hitung deadline produksi berdasarkan lead time
            $leadTimeStartedAt = Carbon::parse($leadTime->created_at);
            $productionDeadline = $leadTimeStartedAt->copy()->addDays($leadTime->total_lead_time_days);
            $now = Carbon::now();
            $daysLeft = $now->diffInDays($productionDeadline, false);

            // Siapkan data job untuk email
            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'qty_order_estimation' => (float)$job->qty_order_estimation,
                'total_lead_time_days' => $leadTime->total_lead_time_days,
                'max_lead_time_days' => max($leadTime->tinta_material_days, $leadTime->kertas_baru_days, $leadTime->foil_days, $leadTime->tooling_days),
                'produksi_hours' => $leadTime->produksi_hours,
                'lead_time_started_at' => $leadTimeStartedAt->format('d/m/Y H:i'),
                'production_deadline' => $productionDeadline->format('d/m/Y H:i'),
                'days_left' => $daysLeft
            ];

            // Tambahkan informasi material khusus
            $materials = [];
            if ($job->tinta_khusus && $leadTime->tinta_material_days > 0) {
                $materials[] = ['name' => 'Tinta & Material', 'days' => $leadTime->tinta_material_days];
            }
            if ($job->kertas_khusus && $leadTime->kertas_baru_days > 0) {
                $materials[] = ['name' => 'Kertas Baru', 'days' => $leadTime->kertas_baru_days];
            }
            if ($job->foil_khusus && $leadTime->foil_days > 0) {
                $materials[] = ['name' => 'Foil', 'days' => $leadTime->foil_days];
            }
            if ($job->pale_tooling_khusus && $leadTime->tooling_days > 0) {
                $materials[] = ['name' => 'Tooling', 'days' => $leadTime->tooling_days];
            }
            $jobData['materials'] = $materials;

            // Kirim notifikasi email
            $emailService->sendProsesProduksiNotification($jobData, 'first');

            Log::info("Proses produksi notification sent for job: {$job->job_code}");
        } catch (\Exception $e) {
            Log::error("Failed to send proses produksi notification for job {$job->job_code}: " . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi progress job dengan format terstruktur
     */
    public function sendProgressJobStructuredNotification($job, $statusChange = null)
    {
        try {
            $emailService = new DevelopmentEmailNotificationService();

            // Ambil production schedules dari job
            $productionSchedules = [];
            if ($job->productionSchedules) {
                foreach ($job->productionSchedules as $index => $schedule) {
                    // Hitung selisih hari dari deadline
                    $daysDifference = null;
                    if ($schedule->deadline) {
                        $deadline = \Carbon\Carbon::parse($schedule->deadline);
                        $now = \Carbon\Carbon::now();
                        $daysDifference = $now->diffInDays($deadline, false);
                    }

                    $productionSchedules[] = [
                        'proses' => $schedule->proses ?? 'Process ' . ($index + 1),
                        'status' => $schedule->status ?? 'pending',
                        'status_label' => $schedule->status_label ?? ucfirst($schedule->status ?? 'Pending'),
                        'rnd_approval_status' => $schedule->rnd_approval_status ?? 'pending',
                        'rnd_approval_status_label' => $schedule->rnd_approval_status_label ?? 'Pending',
                        'production_date_time' => $schedule->production_date_time ?? '-',
                        'deadline' => $schedule->deadline ? \Carbon\Carbon::parse($schedule->deadline)->format('d/m/Y') : '-',
                        'days_difference' => $daysDifference
                    ];
                }
            }

            // Siapkan data job untuk email
            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'qty_order_estimation' => (float)$job->qty_order_estimation,
                'status_after' => $job->status_job,
                'status_after_desc' => $job->status_job_desc ?? ucfirst(str_replace('_', ' ', $job->status_job)),
                'progress_percentage' => $job->progress_percentage ?? 0,
                'job_deadline' => $job->job_deadline ? \Carbon\Carbon::parse($job->job_deadline)->format('d/m/Y') : '-',
                'days_left' => $job->days_left ?? 0,
                'notes' => $job->notes ?? '',
                'production_schedules' => $productionSchedules
            ];

            // Tambahkan informasi perubahan status jika ada
            if ($statusChange) {
                $jobData['status_before'] = $statusChange['status_before'] ?? '';
                $jobData['change_time'] = $statusChange['change_time'] ?? now()->format('d/m/Y H:i');
                $jobData['changed_by'] = $statusChange['changed_by'] ?? 'System';
                $jobData['action_description'] = $statusChange['action_description'] ?? '';
            }

            // Kirim notifikasi email terstruktur
            $emailService->sendProgressJobStructuredNotification($jobData, [
                'notification_type' => 'progress_job_structured',
                'action_text' => 'Lihat Detail Job'
            ]);

            Log::info("Progress job structured notification sent for job: {$job->job_code}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send progress job structured notification for job {$job->job_code}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi progress job ketika status berubah
     */
    private function sendProgressJobNotification($job, $oldStatus, $newStatus, $actionDescription)
    {
        try {
            $emailService = new DevelopmentEmailNotificationService();

            // Hitung progress berdasarkan status
            $progressPercentage = $this->calculateProgressPercentage($newStatus);

            // Hitung deadline dan sisa waktu
            $jobDeadline = $job->job_deadline ? Carbon::parse($job->job_deadline) : null;
            $daysLeft = $jobDeadline ? Carbon::now()->diffInDays($jobDeadline, false) : null;

            // Siapkan data job untuk email
            $jobData = [
                'id' => $job->id,
                'job_code' => $job->job_code,
                'job_name' => $job->job_name,
                'customer' => $job->customer,
                'product' => $job->product,
                'qty_order_estimation' => (float)$job->qty_order_estimation,
                'status_before' => $oldStatus,
                'status_after' => $newStatus,
                'status_before_desc' => $this->getStatusDescription($oldStatus),
                'status_after_desc' => $this->getStatusDescription($newStatus),
                'change_time' => now()->format('d/m/Y H:i'),
                'changed_by' => auth()->user()->name . ' (' . auth()->user()->jabatan . ')',
                'progress_percentage' => $progressPercentage,
                'job_deadline' => $jobDeadline ? $jobDeadline->format('d/m/Y') : 'Tidak ada deadline',
                'days_left' => $daysLeft ?? 'Tidak ada deadline',
                'action_description' => $actionDescription,
                'notes' => 'Status job development berubah dari "' . $this->getStatusDescription($oldStatus) . '" menjadi "' . $this->getStatusDescription($newStatus) . '"'
            ];

            // Kirim notifikasi email
            $emailService->sendProgressJobNotification($jobData);

            // Log ke system logs
            SystemLogService::logJobDevelopmentStatusChange($job, $oldStatus, $newStatus, $actionDescription, request());

            Log::info("Progress job notification sent for job: {$job->job_code} - Status changed from {$oldStatus} to {$newStatus}");
        } catch (\Exception $e) {
            Log::error("Failed to send progress job notification for job {$job->job_code}: " . $e->getMessage());
        }
    }

    /**
     * Hitung progress percentage berdasarkan status job
     */
    private function calculateProgressPercentage($statusJob)
    {
        $statusProgress = [
            'PENDING' => 0,
            'OPEN' => 5,
            'IN_PROGRESS' => 25,
            'IN_PROGRESS_PREPRESS' => 50,
            'MEETING_OPP' => 60,
            'READY_FOR_CUSTOMER' => 70,
            'APPROVED' => 80,
            'SALES_ORDER_CREATED' => 90,
            'COMPLETED' => 100,
            'CANCELLED' => 0,
            'REJECTED_BY_CUSTOMER' => 0
        ];

        return $statusProgress[$statusJob] ?? 0;
    }

    /**
     * Konversi status job ke bahasa yang mudah dipahami
     */
    private function getStatusDescription($statusJob)
    {
        $statusDescriptions = [
            'PENDING' => 'Menunggu Proses',
            'DRAFT' => 'Draft Job Development',
            'OPEN' => 'Job Baru Dibuat',
            'IN_PROGRESS' => 'Sedang Dikerjakan RnD',
            'IN_PROGRESS_PREPRESS' => 'Sedang Dikerjakan Prepress (Langsung dari Marketing)',
            'FINISH_PREPRESS' => 'PREPRESS SELESAI MENGERJAKAN',
            'MEETING_OPP' => 'Meeting dengan Customer',
            'READY_FOR_CUSTOMER' => 'BISA DIJADWALKAN OLEH PPIC & DIPROSES OLEH PRODUKSI',
            'APPROVED' => 'Disetujui Customer',
            'SALES_ORDER_CREATED' => 'Sales Order Dibuat ',
            'COMPLETED' => 'Selesai',
            'CANCELLED' => 'Dibatalkan',
            'REJECTED_BY_CUSTOMER' => 'Ditolak Customer',
            'SCHEDULED_FOR_PRODUCTION' => 'SUDAH JADWALKAN OLEH PPIC & MENUNGGU DIPROSES OLEH PRODUKSI'
        ];

        return $statusDescriptions[$statusJob] ?? $statusJob;
    }
/**
     * Get report data
     */
    public function reportDataDevelopment()
    {
        return view('main.report.data-reportdevelopment');
    }

    /**
     * Get report data for DataTable
     */
    public function getReportDataDevelopment(Request $request)
    {
        try {
            $query = JobOrderDevelopment::with('marketingUser')
                ->select([
                    'id',
                    'job_code',
                    'job_name',
                    'marketing_user_id',
                    'tanggal',
                    'job_deadline',
                    'status_job',
                    'catatan',
                    'customer',
                    'product',
                    'prioritas_job',
                    'job_type'
                ]);



            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            if ($request->filled('status_filter')) {
                $query->where('status_job', $request->status_filter);
            }

            // dd($query->get());

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('job_id', function ($row) {
                    return $row->job_code;
                })
                ->addColumn('marketing_name', function ($row) {
                    return $row->marketingUser->name ?? '-';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-';
                })
                ->addColumn('end_date', function ($row) {
                    return $row->job_deadline ? \Carbon\Carbon::parse($row->job_deadline)->format('d/m/Y') : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status_job;
                })
                ->addColumn('notes', function ($row) {
                    return $row->catatan ?? '-';
                })
                ->addColumn('progress', function ($row) {
                    // Calculate progress based on status
                    $statusProgress = [
                        'DRAFT' => 10,
                        'OPEN' => 20,
                        'IN_PROGRESS_PREPRESS' => 30,
                        'FINISH_PREPRESS' => 40,
                        'MEETING_OPP' => 50,
                        'READY_FOR_CUSTOMER' => 60,
                        'SCHEDULED_FOR_PRODUCTION' => 70,
                        'PRODUCTION_COMPLETED' => 80,
                        'PRODUCTION_APPROVED_BY_RND' => 90,
                        'WAITING_MPP' => 95,
                        'MPP_APPROVED' => 98,
                        'SALES_ORDER_CREATED' => 99,
                        'COMPLETED' => 100
                    ];
                    return $statusProgress[$row->status_job] ?? 0;
                })
                ->addIndexColumn()
                ->rawColumns(['job_id', 'marketing_name', 'start_date', 'end_date', 'status', 'notes', 'progress'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report to Excel
     */
    public function exportReportDevelopmentExcel(Request $request)
    {
        try {
            $query = JobOrderDevelopment::with('marketingUser');

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('job_deadline', '<=', $request->end_date);
            }

            if ($request->filled('status')) {
                $query->where('status_job', $request->status);
            }

            $data = $query->get();

            $filename = 'report_development_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new class($data) implements FromCollection, WithHeadings, WithMapping {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return [
                        'No',
                        'Job Code',
                        'Job Name',
                        'Customer',
                        'Product',
                        'Marketing',
                        'Tanggal Mulai',
                        'Deadline',
                        'Status',
                        'Progress (%)',
                        'Prioritas',
                        'Job Type',
                        'Keterangan'
                    ];
                }

                public function map($row): array
                {
                    static $index = 0;
                    $index++;

                    // Calculate progress based on status
                    $statusProgress = [
                        'DRAFT' => 10,
                        'OPEN' => 20,
                        'IN_PROGRESS_PREPRESS' => 30,
                        'FINISH_PREPRESS' => 40,
                        'MEETING_OPP' => 50,
                        'READY_FOR_CUSTOMER' => 60,
                        'SCHEDULED_FOR_PRODUCTION' => 70,
                        'PRODUCTION_COMPLETED' => 80,
                        'PRODUCTION_APPROVED_BY_RND' => 90,
                        'WAITING_MPP' => 95,
                        'MPP_APPROVED' => 98,
                        'SALES_ORDER_CREATED' => 99,
                        'COMPLETED' => 100
                    ];
                    $progress = $statusProgress[$row->status_job] ?? 0;

                    return [
                        $index,
                        $row->job_code,
                        $row->job_name,
                        $row->customer,
                        $row->product,
                        $row->marketingUser->name ?? '-',
                        $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
                        $row->job_deadline ? \Carbon\Carbon::parse($row->job_deadline)->format('d/m/Y') : '-',
                        $row->status_job,
                        $progress,
                        $row->prioritas_job,
                        $row->job_type === 'new' ? 'Produk Baru' : ($row->job_type === 'repeat' ? 'Produk Repeat' : $row->job_type),
                        $row->catatan ?? '-'
                    ];
                }
            }, $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Export report to PDF
     */
    public function exportReportDevelopmentPdf(Request $request)
    {
        // try {
        //     $query = JobOrderDevelopment::with('marketingUser');

        //     // Apply filters
        //     if ($request->filled('start_date')) {
        //         $query->whereDate('tanggal', '>=', $request->start_date);
        //     }

        //     if ($request->filled('end_date')) {
        //         $query->whereDate('job_deadline', '<=', $request->end_date);
        //     }

        //     if ($request->filled('status')) {
        //         $query->where('status_job', $request->status);
        //     }

        //     $data = $query->get();

        //     $pdf = PDF::loadView('main.report.pdf-reportdevelopment', [
        //         'data' => $data,
        //         'start_date' => $request->start_date,
        //         'end_date' => $request->end_date,
        //         'status_filter' => $request->status
        //     ]);

        //     $filename = 'report_development_' . date('Y-m-d_H-i-s') . '.pdf';

        //     return $pdf->download($filename);

        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        // }
    }

    /**
     * RnD Customer Approval - Approve setelah customer ACC
     */
    public function rndCustomerApproval(Request $request, $id)
    {
        try {
            $job = JobOrderDevelopment::findOrFail($id);

            // Debug logging
            Log::info('RnD Customer Approval Debug', [
                'job_id' => $id,
                'meeting_opp1_exists' => $job->meetingOpp1 ? 'yes' : 'no',
                'marketing_approval' => $job->meetingOpp1 ? $job->meetingOpp1->marketing_approval : 'null',
                'request_data' => $request->all()
            ]);

            // Validasi hanya bisa approve jika customer sudah ACC atau pending
            if (!$job->meetingOpp1 || !in_array($job->meetingOpp1->marketing_approval, ['approve', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer belum ACC, tidak dapat melakukan RnD approval'
                ], 400);
            }

            $request->validate([
                'rnd_customer_approval' => 'required|in:pending,approve,reject',
                'rnd_customer_notes' => 'nullable|string|max:1000'
            ]);

            $job->update([
                'rnd_customer_approval' => $request->rnd_customer_approval,
                'rnd_customer_notes' => $request->rnd_customer_notes,
                'rnd_customer_approved_at' => now(),
                'rnd_customer_approved_by' => auth()->user()->name
            ]);

            // Update status job jika approve
            if ($request->rnd_customer_approval === 'approve') {
                $job->update(['status_job' => 'READY_FOR_CUSTOMER']);
            }

            return response()->json([
                'success' => true,
                'message' => 'RnD approval berhasil disimpan',
                'data' => $job
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}
