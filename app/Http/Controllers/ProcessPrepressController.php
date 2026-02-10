<?php

namespace App\Http\Controllers;

use App\Models\AssignJobPrepress;
use App\Models\AttachmentJobOrder;
use App\Models\HandlingJobPrepress;
use App\Models\JenisPekerjaanPrepress;
use Illuminate\Http\Request;
use App\Models\JobPrepress;
use App\Models\Machine;
use App\Models\MasterDataPrepress;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\JobOrderNumberService;
use App\Services\DevelopmentEmailNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessPrepressController extends Controller
{
    public function indexJobOrderPrepress()
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::get();

        return view('main.process.prepress.input-job-order-prepress-new', compact('jenisPekerjaan'));
    }

    public function indexJobOrderPrepressNew()
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::get();

        return view('main.process.prepress.input-job-order-prepress-new', compact('jenisPekerjaan'));
    }

    /**
     * Get nomor job order berikutnya untuk tanggal tertentu
     */
    public function getNextJobOrderNumber(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $jobOrderNumberService = new JobOrderNumberService();
        $nextNumber = $jobOrderNumberService->generateJobOrderNumber($tanggal);

        return response()->json([
            'success' => true,
            'next_number' => $nextNumber
        ]);
    }

    /**
     * Generate nomor job order baru untuk multiple job orders
     */
    private function generateNewJobOrderNumber($tanggal)
    {
        $jobOrderNumberService = new JobOrderNumberService();
        // Gunakan method yang lebih aman untuk mencegah duplicate
        return $jobOrderNumberService->generateSafeJobOrderNumber($tanggal, 5);
    }

    public function submitJobOrderPrepress(Request $request)
    {
        // dd($request->all());
        try {
            $data = $request->all();
            // dd($data);

            // Cek limit job order berdasarkan tanggal deadline
            $jobDeadline = $data['job_deadline'];
            $limitSetting = Setting::getValue('limit_job_order_date', 7); // Default 7 jika setting tidak ada

            // Hitung jumlah job order yang sudah ada pada tanggal deadline yang sama
            // Exclude status CLOSED dan PENDING
            $existingJobCount = JobPrepress::where('tanggal_deadline', $jobDeadline)
                ->whereNotIn('status_job', ['CLOSED', 'PENDING'])
                ->count();

            // Jika sudah melebihi limit, return error
            if ($existingJobCount >= $limitSetting) {
                return response()->json([
                    'success' => false,
                    'message' => "Job Prepress sudah mencapai limit untuk tanggal deadline {$jobDeadline}. Saat ini sudah ada {$existingJobCount} job order. Silakan koordinasi terlebih dahulu dengan Head/SPV Prepress.",
                    'limit_reached' => true,
                    'current_count' => $existingJobCount,
                    'limit' => $limitSetting,
                    'deadline_date' => $jobDeadline
                ], 400);
            }

            // Simpan color detail sebagai array/JSON
            $color_detail = [];
            for ($i = 1; $i <= 10; $i++) {
                if (!empty($data['color'][$i])) {
                    $color_detail[] = $data['color'][$i];
                }
            }

            // Handle file_data - pastikan selalu ada array
            $file_data = $data['file_data'] ?? [];
            if (!is_array($file_data)) {
                $file_data = [];
            }

            $id_job = $data['id_job'] ?? null;
            $jobOrder = JobPrepress::find($id_job);

            // Generate nomor job order otomatis jika tidak ada ID job (create baru)
            if (!$id_job) {
                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
            }

            // Pastikan nomor job order selalu ada
            if (empty($data['nomor_job_order'])) {
                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
            }

            // dd($data);

            // Loop job_order, simpan satu-satu
            $createdJobOrder = null;

            foreach ($data['job_order'] as $job_order) {

                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
                // dd($data);
                if ($job_order == 'Lainnya') {
                    // Handle multiple selection untuk job_order_lainnya_detail
                    if (is_array($data['job_order_lainnya_detail'])) {
                        // Buat job order terpisah untuk setiap jenis pekerjaan yang dipilih
                        foreach ($data['job_order_lainnya_detail'] as $jenisPekerjaanId) {
                            $jenisPekerjaan = JenisPekerjaanPrepress::find($jenisPekerjaanId);
                            $job_order_detail = $jenisPekerjaan ? $jenisPekerjaan->nama_jenis : 'Unknown';

                            // Generate nomor job order baru untuk setiap job order
                            $nomorJobOrder = $this->generateNewJobOrderNumber($data['tanggal']);

                            $createdJobOrder = JobPrepress::create([
                                'nomor_job_order' => $nomorJobOrder,
                                'tanggal_job_order' => $data['tanggal'],
                                'product' => $data['product'],
                                'tanggal_deadline' => $data['job_deadline'],
                                'customer' => $data['customer'],
                                'kode_design' => $data['kode_design'],
                                'dimension' => $data['dimension'],
                                'material' => $data['material'],
                                'total_color' => $data['total_color'],
                                'total_color_details' => json_encode($color_detail),
                                'qty_order_estimation' => $data['qty_order_estimation'],
                                'job_order' => $job_order_detail,
                                'job_title' => $data['kode_design'] . '-' . $job_order_detail . '-' . $data['product'],
                                'file_data' => json_encode($file_data),
                                'created_by' => Auth::user()->name,
                                'status_job' => $data['status_job'] ?? 'PENDING',
                                'prioritas_job' => $data['prioritas_job'] ?? 'Normal',
                                'catatan' => $data['catatan'] ?? null,
                                'sub_unit_job' => $data['sub_unit_job'] ?? null,
                            ]);
                        }
                    } else {
                        // Fallback untuk single selection
                        $job_order = $data['job_order_lainnya_detail'];

                        $createdJobOrder = JobPrepress::updateOrCreate([
                            'id' => $id_job,
                        ], [
                            'nomor_job_order' => $data['nomor_job_order'],
                            'tanggal_job_order' => $data['tanggal'],
                            'product' => $data['product'],
                            'tanggal_deadline' => $data['job_deadline'],
                            'customer' => $data['customer'],
                            'kode_design' => $data['kode_design'],
                            'dimension' => $data['dimension'],
                            'material' => $data['material'],
                            'total_color' => $data['total_color'],
                            'total_color_details' => json_encode($color_detail),
                            'qty_order_estimation' => $data['qty_order_estimation'],
                            'job_order' => $job_order,
                            'job_title' => $data['kode_design'] . '-' . $job_order . '-' . $data['product'],
                            'file_data' => json_encode($file_data),
                            'created_by' => Auth::user()->name,
                            'status_job' => $data['status_job'] ?? ($jobOrder ? $jobOrder->status_job : 'PENDING'),
                            'prioritas_job' => $data['prioritas_job'] ?? ($jobOrder ? $jobOrder->prioritas_job : 'Normal'),
                            'catatan' => $data['catatan'] ?? null,
                            'sub_unit_job' => $data['sub_unit_job'] ?? null,
                        ]);
                    }
                } else {
                    // Untuk job order yang bukan 'Lainnya'
                    $createdJobOrder = JobPrepress::updateOrCreate([
                        'id' => $id_job,
                    ], [
                        'nomor_job_order' => $data['nomor_job_order'],
                        'tanggal_job_order' => $data['tanggal'],
                        'product' => $data['product'],
                        'tanggal_deadline' => $data['job_deadline'],
                        'customer' => $data['customer'],
                        'kode_design' => $data['kode_design'],
                        'dimension' => $data['dimension'],
                        'material' => $data['material'],
                        'total_color' => $data['total_color'],
                        'total_color_details' => json_encode($color_detail),
                        'qty_order_estimation' => $data['qty_order_estimation'],
                        'job_order' => $job_order,
                        'job_title' => $data['kode_design'] . '-' . $job_order . '-' . $data['product'],
                        'file_data' => json_encode($file_data),
                        'created_by' => Auth::user()->name,
                        'status_job' => $data['status_job'] ?? ($jobOrder ? $jobOrder->status_job : 'PENDING'),
                        'prioritas_job' => $data['prioritas_job'] ?? ($jobOrder ? $jobOrder->prioritas_job : 'Normal'),
                        'catatan' => $data['catatan'] ?? null,
                        'sub_unit_job' => $data['sub_unit_job'] ?? null,
                        'rejected_at' => null,
                        'rejected_by' => null,
                        'reason_reject' => null,
                    ]);
                }
            }

            // Kirim notifikasi menggunakan job order yang baru dibuat
            if ($createdJobOrder) {
                try {
                    $notificationService = new NotificationService();
                    $notificationService->sendJobOrderPrepressNotification($createdJobOrder);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification: ' . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Job Order submitted successfully.']);
        } catch (\ErrorException $e) {
            // Handle undefined index errors
            if (strpos($e->getMessage(), 'Undefined index:') !== false) {
                $field = str_replace('Undefined index: ', '', $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => "Data tidak lengkap. Field '{$field}' tidak ditemukan.",
                    'type' => 'data_type_error',
                    'details' => "Field yang diperlukan: {$field}. Mohon periksa kembali form input."
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada data input.',
                'type' => 'data_type_error',
                'details' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Job Order Prepress Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'type' => 'database_error',
                'details' => 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
            ], 500);
        }
    }

    public function submitJobOrderPrepressNew(Request $request)
    {
        try {
            $data = $request->all();
            // dd($data['job_order']);

            // Handle file attachments
            $attachmentPaths = [];
            $attachmentData = []; // Simpan data file (path, name, type) untuk digunakan nanti
            $processedFiles = []; // Track file yang sudah di-process untuk menghindari duplikasi

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    try {
                        $fileSize = $file->getSize(); // Size in bytes
                        $maxSizeForStorage = 100 * 1024 * 1024; // 100 MB

                        // Simpan extension sebelum file di-move
                        $fileExtension = $file->getClientOriginalExtension() ?: 'unknown';
                        $originalName = $file->getClientOriginalName();

                        // Cek duplikasi berdasarkan original name dan size
                        $fileKey = $originalName . '_' . $fileSize;
                        if (isset($processedFiles[$fileKey])) {
                            // File sudah di-process, skip
                            continue;
                        }

                        // Mark file sebagai sudah di-process
                        $processedFiles[$fileKey] = true;

                        // Generate unique filename
                        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

                        if ($fileSize >= $maxSizeForStorage) {
                            // File >= 100 MB: simpan di public folder
                            $path = 'uploads/prepress/large';
                            if (!file_exists(public_path($path))) {
                                mkdir(public_path($path), 0755, true);
                            }
                            $file->move(public_path($path), $filename);
                            $fullPath = $path . '/' . $filename;
                        } else {
                            // File < 100 MB: simpan di storage biasa
                            $path = 'uploads/prepress';
                            if (!file_exists(public_path($path))) {
                                mkdir(public_path($path), 0755, true);
                            }
                            $file->move(public_path($path), $filename);
                            $fullPath = $path . '/' . $filename;
                        }

                        // Simpan data file untuk digunakan nanti
                        $attachmentPaths[] = $fullPath;
                        $attachmentData[] = [
                            'path' => $fullPath,
                            'name' => basename($fullPath),
                            'type' => $fileExtension,
                            'original_name' => $originalName
                        ];
                    } catch (\Exception $e) {
                        Log::error('Error uploading attachment file: ' . $e->getMessage());
                        // Continue dengan file berikutnya
                    }
                }
            }

            // Cek limit job order berdasarkan tanggal deadline
            // $jobDeadline = $data['job_deadline'];
            // $limitSetting = Setting::getValue('limit_job_order_date', 7); // Default 7 jika setting tidak ada

            // Hitung jumlah job order yang sudah ada pada tanggal deadline yang sama
            // Exclude status CLOSED dan PENDING
            // $existingJobCount = JobPrepress::where('tanggal_deadline', $jobDeadline)
            //     ->whereNotIn('status_job', ['CLOSED', 'PENDING'])
            //     ->count();

            // // Jika sudah melebihi limit, return error
            // if ($existingJobCount >= $limitSetting) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => "Job Prepress sudah mencapai limit untuk tanggal deadline {$jobDeadline}. Saat ini sudah ada {$existingJobCount} job order. Silakan koordinasi terlebih dahulu dengan Head/SPV Prepress.",
            //         'limit_reached' => true,
            //         'current_count' => $existingJobCount,
            //         'limit' => $limitSetting,
            //         'deadline_date' => $jobDeadline
            //     ], 400);
            // }

            // Simpan color detail sebagai array/JSON
            $color_detail = [];
            for ($i = 1; $i <= 10; $i++) {
                if (!empty($data['color'][$i])) {
                    $color_detail[] = $data['color'][$i];
                }
            }

            // Handle file_data - pastikan selalu ada array
            $file_data = $data['file_data'] ?? [];
            if (!is_array($file_data)) {
                $file_data = [];
            }

            $id_job = $data['id_job'] ?? null;
            $jobOrder = JobPrepress::find($id_job);

            // Generate nomor job order otomatis jika tidak ada ID job (create baru)
            if (!$id_job) {
                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
            }

            // Pastikan nomor job order selalu ada
            if (empty($data['nomor_job_order'])) {
                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
            }

            // dd($data);

            // Loop job_order, simpan satu-satu
            $createdJobOrders = []; // Simpan semua job order yang dibuat

            foreach ($data['job_order'] as $job_order) {

                // dd($job_order);

                $data['nomor_job_order'] = $this->generateNewJobOrderNumber($data['tanggal']);
                // dd($data);
                if ($job_order == 'Lainnya') {
                    // Handle multiple selection untuk job_order_lainnya_detail
                    if (is_array($data['job_order_lainnya_detail'])) {
                        // Buat job order terpisah untuk setiap jenis pekerjaan yang dipilih
                        foreach ($data['job_order_lainnya_detail'] as $jenisPekerjaanId) {
                            $jenisPekerjaan = JenisPekerjaanPrepress::find($jenisPekerjaanId);
                            $job_order_detail = $jenisPekerjaan ? $jenisPekerjaan->nama_jenis : 'Unknown';

                            // Generate nomor job order baru untuk setiap job order
                            $nomorJobOrder = $this->generateNewJobOrderNumber($data['tanggal']);

                            $createdJobOrder = JobPrepress::create([
                                'nomor_job_order' => $nomorJobOrder,
                                'tanggal_job_order' => $data['tanggal'],
                                'product' => $data['product'],
                                'tanggal_deadline' => $data['job_deadline'],
                                'customer' => $data['customer'],
                                'kode_design' => $data['kode_design'],
                                'dimension' => $data['dimension'],
                                'material' => $data['material'],
                                'total_color' => $data['total_color'],
                                'total_color_details' => json_encode($color_detail),
                                'qty_order_estimation' => $data['qty_order_estimation'],
                                'job_order' => $job_order_detail,
                                'job_title' => $data['kode_design'] . '-' . $job_order_detail . '-' . $data['product'],
                                'file_data' => json_encode($file_data),
                                'created_by' => Auth::user()->name,
                                'status_job' => $data['status_job'] ?? 'PENDING',
                                'prioritas_job' => $data['prioritas_job'] ?? 'Normal',
                                'catatan' => $data['catatan'] ?? null,
                                'sub_unit_job' => $data['sub_unit_job'] ?? null,
                            ]);
                            $createdJobOrders[] = $createdJobOrder; // Simpan ke array
                        }
                    } else {
                        // Fallback untuk single selection
                        $job_order = $data['job_order_lainnya_detail'];

                        $createdJobOrder = JobPrepress::updateOrCreate([
                            'id' => $id_job,
                        ], [
                            'nomor_job_order' => $data['nomor_job_order'],
                            'tanggal_job_order' => $data['tanggal'],
                            'product' => $data['product'],
                            'tanggal_deadline' => $data['job_deadline'],
                            'customer' => $data['customer'],
                            'kode_design' => $data['kode_design'],
                            'dimension' => $data['dimension'],
                            'material' => $data['material'],
                            'total_color' => $data['total_color'],
                            'total_color_details' => json_encode($color_detail),
                            'qty_order_estimation' => $data['qty_order_estimation'],
                            'job_order' => $job_order,
                            'job_title' => $data['kode_design'] . '-' . $job_order . '-' . $data['product'],
                            'file_data' => json_encode($file_data),
                            'created_by' => Auth::user()->name,
                            'status_job' => $data['status_job'] ?? ($jobOrder ? $jobOrder->status_job : 'PENDING'),
                            'prioritas_job' => $data['prioritas_job'] ?? ($jobOrder ? $jobOrder->prioritas_job : 'Normal'),
                            'catatan' => $data['catatan'] ?? null,
                            'sub_unit_job' => $data['sub_unit_job'] ?? null,
                        ]);
                        if (!in_array($createdJobOrder->id, array_column($createdJobOrders, 'id'))) {
                            $createdJobOrders[] = $createdJobOrder; // Simpan ke array jika belum ada
                        }
                    }
                } else {

                    $job_order_detail = JenisPekerjaanPrepress::where('id', $job_order)->first();
                    // dd($job_order_detail);
                    // Untuk job order yang bukan 'Lainnya'
                    $createdJobOrder = JobPrepress::updateOrCreate([
                        'id' => $id_job,
                    ], [
                        'nomor_job_order' => $data['nomor_job_order'],
                        'tanggal_job_order' => $data['tanggal'],
                        'product' => $data['product'],
                        'tanggal_deadline' => $data['job_deadline'],
                        'customer' => $data['customer'],
                        'kode_design' => $data['kode_design'],
                        'dimension' => $data['dimension'],
                        'material' => $data['material'],
                        'total_color' => $data['total_color'],
                        'total_color_details' => json_encode($color_detail),
                        'qty_order_estimation' => $data['qty_order_estimation'],
                        'job_order' => $job_order_detail->nama_jenis,
                        'job_title' => $data['kode_design'] . '-' . $job_order_detail->nama_jenis . '-' . $data['product'],
                        'file_data' => json_encode($file_data),
                        'created_by' => Auth::user()->name,
                        'status_job' => $data['status_job'] ?? ($jobOrder ? $jobOrder->status_job : 'PENDING'),
                        'prioritas_job' => $data['prioritas_job'] ?? ($jobOrder ? $jobOrder->prioritas_job : 'Normal'),
                        'catatan' => $data['catatan'] ?? null,
                        'sub_unit_job' => $job_order['unit_job'] ?? null,
                    ]);
                    if (!in_array($createdJobOrder->id, array_column($createdJobOrders, 'id'))) {
                        $createdJobOrders[] = $createdJobOrder; // Simpan ke array jika belum ada
                    }
                }
            }

            // Simpan attachment ke database untuk semua job order yang dibuat
            // Setiap job order mendapat attachment yang sama (jika ada multiple job order dari 1 form)
            if (!empty($createdJobOrders) && !empty($attachmentData)) {
                foreach ($createdJobOrders as $jobOrder) {
                    foreach ($attachmentData as $attachmentInfo) {
                        try {
                            // Cek apakah attachment sudah ada untuk job order ini (untuk menghindari duplicate)
                            $existingAttachment = AttachmentJobOrder::where('id_job_order', (string)$jobOrder->id)
                                ->where('file_path', $attachmentInfo['path'])
                                ->first();

                            if (!$existingAttachment) {
                                AttachmentJobOrder::create([
                                    'id_job_order' => (string)$jobOrder->id, // Convert ke string sesuai migration
                                    'file_path' => $attachmentInfo['path'],
                                    'file_name' => $attachmentInfo['name'],
                                    'file_type' => $attachmentInfo['type'],
                                    'created_by' => Auth::user()->name,
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error saving attachment to database: ' . $e->getMessage());
                            Log::error('Attachment data: ' . json_encode([
                                'id_job_order' => $jobOrder->id,
                                'file_path' => $attachmentInfo['path'],
                                'file_name' => $attachmentInfo['name'],
                                'file_type' => $attachmentInfo['type'],
                            ]));
                            // Continue dengan file berikutnya meskipun ada error
                        }
                    }
                }
            }

            // Kirim notifikasi menggunakan job order yang baru dibuat
            if (!empty($createdJobOrders)) {
                foreach ($createdJobOrders as $jobOrder) {
                    try {
                        $notificationService = new NotificationService();
                        $notificationService->sendJobOrderPrepressNotification($jobOrder);
                    } catch (\Exception $e) {
                        Log::error('Failed to send notification: ' . $e->getMessage());
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Job Order submitted successfully.']);
        } catch (\ErrorException $e) {
            // Handle undefined index errors
            if (strpos($e->getMessage(), 'Undefined index:') !== false) {
                $field = str_replace('Undefined index: ', '', $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => "Data tidak lengkap. Field '{$field}' tidak ditemukan.",
                    'type' => 'data_type_error',
                    'details' => "Field yang diperlukan: {$field}. Mohon periksa kembali form input."
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada data input.',
                'type' => 'data_type_error',
                'details' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Job Order Prepress Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'type' => 'database_error',
                'details' => 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
            ], 500);
        }
    }

    /**
     * Cek limit job order berdasarkan tanggal deadline
     */
    public function checkJobOrderLimit(Request $request)
    {
        $request->validate([
            'deadline_date' => 'required|date'
        ]);

        $deadlineDate = $request->deadline_date;
        $limitSetting = Setting::getValue('limit_job_order_date', 7);

        // Hitung jumlah job order yang sudah ada pada tanggal deadline yang sama
        // Exclude status CLOSED dan PENDING
        $existingJobCount = JobPrepress::where('tanggal_deadline', $deadlineDate)
            // ->whereNotIn('status_job', ['CLOSED', 'PENDING'])
            ->count();

        return response()->json([
            'success' => true,
            'current_count' => $existingJobCount,
            'limit' => $limitSetting,
            'remaining_slots' => max(0, $limitSetting - $existingJobCount)
        ]);
    }
    public function checkJobOrderLimitTime(Request $request)
    {
        $request->validate([
            'deadline_date' => 'required|date'
        ]);

        $deadlineDate = $request->deadline_date;
        $limitSetting = Setting::getValue('limit_job_order_time', 1680); // Default 1680 menit

        // Ambil job prepress yang aktif pada tanggal deadline yang sama
        // Exclude status CLOSED dan PENDING
        $activeJobs = JobPrepress::where('tanggal_deadline', $deadlineDate)
            ->whereNotIn('status_job', ['CLOSED', 'PENDING'])
            ->get();

        // Hitung total waktu pengerjaan dari kolom est_job_default
        $totalWorkTime = $activeJobs->sum('est_job_default');

        // Debug: Log data yang diambil
        \Illuminate\Support\Facades\Log::info('Job Order Limit Check', [
            'deadline_date' => $deadlineDate,
            'active_jobs' => $activeJobs->toArray(),
            'total_work_time' => $totalWorkTime,
            'limit_setting' => $limitSetting
        ]);

        // Cek apakah sudah melebihi limit
        $isOverLimit = $totalWorkTime >= $limitSetting;
        $remainingTime = $limitSetting - $totalWorkTime;

        return response()->json([
            'success' => true,
            'deadline_date' => $deadlineDate,
            'total_work_time' => $totalWorkTime,
            'limit_setting' => $limitSetting,
            'is_over_limit' => $isOverLimit,
            'remaining_time' => $remainingTime > 0 ? $remainingTime : 0,
            'active_jobs_count' => $activeJobs->count(),
            'message' => $isOverLimit
                ? "Tanggal deadline {$deadlineDate} sudah mencapai limit pengerjaan tim prepress. Total waktu: {$totalWorkTime} menit, Limit: {$limitSetting} menit. Silakan ambil deadline lain."
                : "Tanggal deadline {$deadlineDate} masih tersedia. Total waktu: {$totalWorkTime} menit, Sisa waktu: {$remainingTime} menit."
        ]);

        return response()->json([
            'success' => true,
            'current_count' => '',
            'limit' => $limitSetting,
            'remaining_slots' => max(0, $limitSetting - '')
        ]);
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

            // Cari di tabel tb_jenis_pekerjaan_prepresses
            $jenisPekerjaanData = MasterDataPrepress::where('job', $jenisPekerjaan)->get();

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

    public function indexJobOrderPrepressData()
    {
        return view('main.process.prepress.data-job-order-prepress');
    }

    public function jobOrderPrepressData()
    {
        if (request()->ajax()) {
            $query = JobPrepress::query();

            // Filter tanggal (wajib)
            if (request()->filled('tanggal_dari') && request()->filled('tanggal_sampai')) {
                $query->whereBetween('tanggal_job_order', [
                    request('tanggal_dari'),
                    request('tanggal_sampai')
                ]);
            }

            // Optional filter by created_by (Laporan Pribadi)
            if (request()->filled('created_by')) {
                $query->where('created_by', request('created_by'));
            }

            // Filter status (bisa single atau array)
            if (request()->filled('status_job')) {
                $statusJob = request('status_job');
                // Jika array (multiple selection), gunakan whereIn
                if (is_array($statusJob)) {
                    $query->whereIn('status_job', $statusJob);
                } else {
                    // Jika single value, gunakan where
                    $query->where('status_job', $statusJob);
                }
            } else {
                // Jika tidak ada filter status, exclude CLOSED
                $query->whereNotIn('status_job', ['CLOSED']);
            }

            $jobOrders = $query->orderBy('prioritas_job', 'ASC')->get();
            // dd($jobOrders);
            return datatables()->of($jobOrders)
                ->addColumn('file_data', function ($row) {
                    return json_decode($row->file_data, true);
                })
                ->addColumn('pic', function ($row) {
                    $pic = AssignJobPrepress::with('user')->where('id_job_order', $row->id)->first();
                    if ($pic) {
                        return $pic->user->name;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('pending_reason', function ($row) {
                    // Ambil reason dari HandlingJobPrepress untuk status PENDING
                    if ($row->status_job == 'PENDING') {
                        $handling = \App\Models\HandlingJobPrepress::where('id_job_order', $row->id)
                            ->where('status_handling', 'PENDING')
                            ->orderBy('date_handling', 'desc')
                            ->first();
                        if ($handling && $handling->reason) {
                            return $handling->reason;
                        }
                    }
                    return null;
                })
                ->addColumn('status_job', function ($row) {
                    $statusButton = '';
                    $rejectionInfo = '';

                    // Get PIC name
                    $pic = AssignJobPrepress::with('user')->where('id_job_order', $row->id)->first();
                    $picName = $pic ? $pic->user->name : '';

                    // Status button utama
                    if ($row->status_job == 'OPEN') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-success w-100">OPEN</button>';
                    } else if ($row->status_job == 'PLAN') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-primary w-100">PLAN</button>';
                    } else if ($row->status_job == 'ASSIGNED') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-warning w-100">ASSIGN</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'FINISH') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-success w-100">FINISH</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'APPROVED') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-info w-100">APPROVED</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'CLOSED') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-danger w-100">CLOSED</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'IN PROGRESS') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-info w-100">IN PROGRESS</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'COMPLETED') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-warning w-100">COMPLETED</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'SHIFT_2') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-danger w-100">SHIFT 2</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                    } else if ($row->status_job == 'PENDING') {
                        $statusButton = '<button type="button" class="btn btn-sm btn-warning w-100">PENDING</button><br>';
                        if ($picName) {
                            $statusButton .= '<br><small class="text-muted">PIC: ' . $picName . '</small>';
                        }
                        // Ambil reason dari HandlingJobPrepress untuk status PENDING
                        $handling = \App\Models\HandlingJobPrepress::where('id_job_order', $row->id)
                            ->where('status_handling', 'PENDING')
                            ->orderBy('date_handling', 'desc')
                            ->first();
                        if ($handling && $handling->reason) {
                            $statusButton .= '<br><small class="text-danger"><strong>Reason: ' . htmlspecialchars($handling->reason) . '</strong></small>';
                        }
                    }

                    // Cek apakah pernah di-reject
                    if ($row->rejected_at) {
                        $rejectionInfo = '<br><button type="button" class="btn btn-sm btn-danger mt-1 w-100 rejection-info-btn"
                                          data-job-id="' . $row->id . '"
                                          data-rejection-reason="' . htmlspecialchars($row->reason_reject ?? '') . '"
                                          data-rejected-by="' . htmlspecialchars($row->rejected_by ?? '') . '"
                                          data-rejected-at="' . ($row->rejected_at ? \Carbon\Carbon::parse($row->rejected_at)->format('d-m-Y H:i') : '') . '">
                                          TELAH DI REJECT
                                          </button>';
                    }

                    return $statusButton . $rejectionInfo;
                })
                ->addColumn('action', function ($row) {
                    if ($row->status_job == 'APPROVED' || $row->status_job == 'CLOSED') {
                        return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                    } else {
                        return '<a href="' . route('prepress.job-order.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['status_job', 'action'])
                ->make(true);
        }
    }

    public function jobOrderPrepressDataPlan()
    {
        if (request()->ajax()) {
            $jobOrders = JobPrepress::where('status_job', 'OPEN')->get();
            return datatables()->of($jobOrders)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="job-order-checkbox" data-id="' . $row->id . '">';
                })

                ->addColumn('file_data', function ($row) {
                    return json_decode($row->file_data, true);
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('prepress.job-order.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;<a href="' . route('prepress.job-order.update', $row->id) . '" class="btn btn-warning btn-sm">Edit</a> &nbsp;&nbsp;<a href="' . route('prepress.job-order.delete', $row->id) . '" class="btn btn-danger btn-sm">Delete</a>';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function jobOrderPrepressDataPlanSelected()
    {
        if (request()->ajax()) {
            $jobOrders = JobPrepress::whereNotIn('status_job', ['CLOSED'])->get();
            // $jobOrders = JobPrepress::where('status_job', 'PLAN')->get();
            return datatables()->of($jobOrders)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="job-order-checkbox" data-id="' . $row->id . '">';
                })

                ->addColumn('file_data', function ($row) {
                    return json_decode($row->file_data, true);
                })
                ->addColumn('pic', function ($row) {
                    $pic = AssignJobPrepress::with('user')->where('id_job_order', $row->id)->first();
                    // dd($pic);
                    if ($pic) {
                        return $pic->user->name;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('rejected_by_prepress', function ($row) {
                    return $row->rejected_by_prepress ? true : false;
                })
                ->addColumn('rejection_reason', function ($row) {
                    return $row->rejection_reason ?? '';
                })
                ->addColumn('action', function ($row) {
                    // Assign Job
                    if ($row->status_job == 'FINISH') {
                        return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail for Approval</a>&nbsp;&nbsp;';
                    } else if ($row->status_job == 'APPROVED') {
                        return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                    } else if ($row->status_job == 'CLOSED') {
                        return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                    } else if ($row->status_job == 'ASSIGNED') {
                        if ($row->nomor_job_order) {
                            return '
                            <a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp';
                        } else {
                            return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                        }
                    } else if ($row->status_job == 'IN PROGRESS') {
                        return '<a href="' . route('prepress.job-order.assign-job-data', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;';
                    } else if ($row->status_job == 'OPEN' && $row->rejected_by_prepress) {
                        // Job yang di-reject oleh prepress - tidak bisa di-assign lagi
                        return '<a href="' . route('prepress.job-order.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;
                                <span class="badge badge-danger">Rejected by Prepress</span>';
                    } else {
                        return '
                        <a href="' . route('prepress.job-order.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;

                       <button type="button" class="btn btn-success btn-sm assign-job" data-id="' . $row->id . '">Assign Job</button>
                       <button type="button" class="btn btn-danger btn-sm reject-job-order" data-id="' . $row->id . '">Reject</button>';
                    }


                    // return '<a href="' . route('prepress.job-order.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>&nbsp;&nbsp;

                    //    <button type="button" class="btn btn-success btn-sm assign-job" data-id="' . $row->id . '">Assign Job</button>';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function jobOrderPrepressDataPlanSelectedWorkOrder()
    {
        if (request()->ajax()) {
            $workOrder = DB::connection('mysql3')->table('workorderh')
                ->where(function ($query) {
                    $query->where('DocNo', 'like', 'WOT%')
                        ->orWhere('DocNo', 'like', 'WOP%');
                })
                ->whereDate('DocDate', '>=', Carbon::now()->subMonths(12))
                // ->where('DocNo', 'like', '%WOT-250714-001%')
                ->get();

            // Get all existing job order numbers from JobPrepress table
            // $existingJobOrders = JobPrepress::pluck('nomor_job_order')->toArray();

            // Filter out work orders that already exist in JobPrepress
            // $filteredWorkOrder = $workOrder->filter(function ($item) use ($existingJobOrders) {
            //     return !in_array($item->DocNo, $existingJobOrders);
            // });


            // Pastikan hasil akhir adalah collection datar
            return datatables()->of($workOrder)
                ->addColumn('nomor_job_order', function ($row) {
                    return $row->DocNo;
                })
                ->addColumn('tanggal_job_order', function ($row) {
                    return $row->DocDate;
                })
                ->addColumn('tanggal_deadline', function ($row) {
                    return $row->DocDate;
                })
                ->addColumn('product', function ($row) {
                    $material = DB::connection('mysql3')->table('mastermaterial')->where('Code', $row->MaterialCode)->first();
                    return '[' . $row->MaterialCode . '] ' . $material->Name;
                })
                ->addColumn('qty_order_estimation', function ($row) {
                    return $row->Qty;
                })
                ->addColumn('job_order', function ($row) {
                    return 'Plate';
                })
                ->addColumn('file_data', function ($row) {
                    return json_decode('', true);
                })
                ->addColumn('prioritas_job', function ($row) {
                    return 'Normal';
                })
                ->addColumn('pic', function ($row) {
                    return '-';
                })
                ->addColumn('status_job', function ($row) {
                    return 'PLAN';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->CreatedBy;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->CreatedDate;
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-success btn-sm assign-job-wo" data-id="' . $row->DocNo . '">Assign Job</button>';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function jobOrderPrepressDataPlanAssigned()
    {
        if (request()->ajax()) {

            $jobOrders = JobPrepress::with('assignJob')
                ->whereNotIn('status_job', ['CLOSED', 'OPEN', 'APPROVED'])
                ->whereHas('assignJob', function ($q) {
                    $q->where('id_user_pic', auth()->user()->id);
                })
                ->orderBy('prioritas_job', 'DESC')
                ->get();

            return datatables()->of($jobOrders)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="job-order-checkbox" data-id="' . $row->id . '">';
                })

                ->addColumn('file_data', function ($row) {
                    return json_decode($row->file_data, true);
                })
                ->addColumn('action', function ($row) {
                    $buttonText = 'Kerjakan';
                    $buttonClass = 'btn-info';

                    if ($row->status_job === 'SHIFT_2') {
                        $buttonText = 'Lanjutkan Shift 2';
                        $buttonClass = 'btn-primary';
                    } elseif ($row->status_job === 'PENDING') {
                        $buttonText = 'Resume';
                        $buttonClass = 'btn-warning';
                    }

                    return '<button type="button" class="btn ' . $buttonClass . ' btn-sm job-order-assign-detail" data-id="' . $row->id . '">' . $buttonText . '</button>';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    // Detail Job
    public function jobOrderDetail($id)
    {
        // Logic to display details of a specific job order
        $jobOrder = JobPrepress::find($id);
        $jenisPekerjaan = JenisPekerjaanPrepress::get();

        return view('main.process.prepress.detail-job-order-prepress', compact('jobOrder', 'jenisPekerjaan'));
    }

    // Detail Job
    public function jobOrderDetailAssign($id)
    {
        // Logic to display details of a specific job order
        $jobOrder = JobPrepress::find($id);

        return response()->json([
            'success' => true,
            'data' => $jobOrder,
            // 'message' => view('main.process.prepress.detail-job-order-prepress-assign', compact('jobOrder'))->render(),
        ]);
        // dd($jobOrder);
        // return respons
        // return view('main.process.prepress.detail-job-order-prepress', compact('jobOrder'));
    }

    // View PPIC
    public function indexDataPlanHarianPPIC()
    {
        $jobOrders = JobPrepress::all();

        return view('main.process.prepress.data.data-planharian', compact('jobOrders'));
    }

    // View Timeline
    public function indexDataTimelineTask()
    {
        $jobOrders = JobPrepress::all();
        $userDetail = User::with(['divisiUser', 'jabatanUser', 'levelUser'])->where('id', auth()->user()->id)->first();
        // dd($userDetail->divisiUser->divisi);
        $pic = User::where('divisi', auth()->user()->divisi)->get();
        $masterData = MasterDataPrepress::all();
        $truePPic = false;
        $headDivisi = false;
        if ($userDetail->divisiUser->divisi == 'PPIC') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePPic = true;
        }

        $truePrepress = false;
        if ($userDetail->divisiUser->divisi == 'PREPRESS') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePrepress = true;
        }

        $trueAdmin = false;
        if ($userDetail->levelUser->level == 'SUPER ADMIN') {
            $trueAdmin = true;
        }

        return view('main.process.prepress.data.data-timelinetask', compact('jobOrders', 'trueAdmin', 'pic', 'masterData', 'truePPic', 'truePrepress', 'headDivisi'));
    }

    // View Prepress Admin
    public function indexDataListPlan()
    {
        $jobOrders = JobPrepress::where('status_job', '!=', 'CLOSED')->get();
        // dd($jobOrders);
        $userDetail = User::with(['divisiUser', 'jabatanUser', 'levelUser'])->where('id', auth()->user()->id)->first();
        // dd($userDetail->divisiUser->divisi);
        $pic = User::where('divisi', auth()->user()->divisi)->get();
        $masterData = MasterDataPrepress::all();
        $truePPic = false;
        $headDivisi = false;
        if ($userDetail->divisiUser->divisi == 'PPIC') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePPic = true;
        }

        $truePrepress = false;
        if ($userDetail->divisiUser->divisi == 'PREPRESS') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePrepress = true;
        }

        $trueAdmin = false;
        if ($userDetail->levelUser->level == 'SUPER ADMIN') {
            $trueAdmin = true;
        }

        return view('main.process.prepress.data.data-listplan', compact('jobOrders', 'trueAdmin', 'pic', 'masterData', 'truePPic', 'truePrepress', 'headDivisi'));
    }

    // View Prepress PIC
    public function indexDataListTask()
    {
        $jobOrders = JobPrepress::all();
        $userDetail = User::with(['divisiUser', 'jabatanUser', 'levelUser'])->where('id', auth()->user()->id)->first();
        // dd($userDetail->divisiUser->divisi);
        $pic = User::where('divisi', auth()->user()->divisi)->get();
        $masterData = MasterDataPrepress::all();
        $truePPic = false;
        $headDivisi = false;
        if ($userDetail->divisiUser->divisi == 'PPIC') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePPic = true;
        }

        $truePrepress = false;
        if ($userDetail->divisiUser->divisi == 'PREPRESS') {
            if ($userDetail->jabatanUser->jabatan == 'HEAD') {
                $headDivisi = true;
            }
            $truePrepress = true;
        }

        $trueAdmin = false;
        if ($userDetail->levelUser->level == 'SUPER ADMIN') {
            $trueAdmin = true;
        }

        return view('main.process.prepress.data.data-listtask', compact('jobOrders', 'trueAdmin', 'pic', 'masterData', 'truePPic', 'truePrepress', 'headDivisi'));
    }

    public function submitJobOrderPrepressPlan(Request $request)
    {
        $data = $request->all();

        foreach ($data['selected_ids'] as $id) {
            $jobOrder = JobPrepress::find($id);
            if ($jobOrder) {
                $jobOrder->update([
                    'status_job' => 'PLAN',
                    'planned_by' => auth()->user()->name,
                    'planned_at' => now(),
                ]);
            }
        }


        // dd($data);
        return response()->json(['success' => true, 'message' => 'Job Order Prepress Plan submitted successfully.']);
    }

    public function assignJobOrderPrepress($id)
    {
        // dd($id);
        // Logic to assign a job order to a machine or user
        $jobOrder = JobPrepress::find($id);
        // dd($jobOrder);
        if (!$jobOrder) {
            return redirect()->back()->with('error', 'Job Order not found.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Job Order assigned successfully.',
            'job_order' => $jobOrder,
            'machines' => Machine::all(), // Assuming you have a Machine model
        ]);
    }

    public function assignWorkOrderPrepress($id)
    {
        // dd($id);
        // Logic to assign a job order to a machine or user
        $workOrder = DB::connection('mysql3')
            ->table('workorderh')
            ->leftJoin('salesorderh', 'workorderh.SODocNo', '=', 'salesorderh.DocNo')
            ->leftJoin('mastercustomer', 'salesorderh.CustomerCode', '=', 'mastercustomer.Code')
            ->leftJoin('mastermaterial', 'workorderh.MaterialCode', '=', 'mastermaterial.Code')
            ->where('workorderh.DocNo', $id)
            ->select('workorderh.*', 'mastercustomer.Name as Customer', 'mastermaterial.Name as Material')
            ->first();

        if (!$workOrder) {
            return redirect()->back()->with('error', 'Job Order not found.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Job Order assigned successfully.',
            'job_order' => $workOrder,
            'machines' => Machine::all(), // Assuming you have a Machine model
        ]);
    }

    public function assignJobOrderPrepressUser(Request $request)
    {
        // dd($request->all());
        // Logic to assign a job order to a user
        $id = $request->input('id_job') ?? '';
        $id_wo = $request->input('id_wo') ?? '';

        if ($id_wo) {
            $jobOrder = JobPrepress::where('nomor_job_order', $id_wo)->first();
            // dd($jobOrder);
            $detailWO = DB::connection('mysql3')
                ->table('workorderh')
                ->leftJoin('salesorderh', 'workorderh.SODocNo', '=', 'salesorderh.DocNo')
                ->leftJoin('mastercustomer', 'salesorderh.CustomerCode', '=', 'mastercustomer.Code')
                ->leftJoin('mastermaterial', 'workorderh.MaterialCode', '=', 'mastermaterial.Code')
                ->where('workorderh.DocNo', $id_wo)
                ->select('workorderh.*', 'mastercustomer.Name as Customer', 'mastermaterial.Name as Material')
                ->first();

            $material = DB::connection('mysql3')->table('mastermaterial')->where('Code', $detailWO->MaterialCode)->first();

            // dd($detailWO->DocDate);
            if ($jobOrder == null) {
                $jobOrder = JobPrepress::create([
                    'tanggal_job_order' => date('Y-m-d', strtotime(now())) ?? null,
                    'nomor_job_order' => $detailWO->DocNo ?? null,
                    'product' => $material->Code . ' - ' . $material->Name,
                    'tanggal_deadline' => date('Y-m-d', strtotime(now())) ?? null,
                    'customer' => $detailWO->Customer,
                    'kode_design' => $detailWO->MaterialCode,
                    'dimension' => null,
                    'material' => null,
                    'total_color' => 0,
                    'total_color_details' => null,
                    'qty_order_estimation' => intval($detailWO->Qty),
                    'job_order' => 'Plate',
                    'job_title' => $detailWO->MaterialCode . '-' . 'Plate' . '-' . $detailWO->Material,
                    'file_data' => null,
                    'created_by' => auth()->user()->name,
                    'status_job' => 'PLAN',
                    'prioritas_job' => 'Normal',
                    'planned_at' => now(),
                    'catatan' => null,
                ]);
            }
        } else {
            $jobOrder = JobPrepress::find($id);
        }



        $id_plan = $request->input('id_plan');
        $id_user = $request->input('assigned_to');

        if ($id) {
            $jobOrder = JobPrepress::find($id);
        } else {
            $jobOrder = JobPrepress::where('nomor_job_order', $id_wo)->first();
        }

        // dd($jobOrder);

        $kategori_job = $request->input('kategori_job') ?? $request->input('kategori_wo');

        $estimasi = MasterDataPrepress::where('kode', $kategori_job)->first();
        // dd($estimasi);

        if (!$jobOrder) {
            return redirect()->back()->with('error', 'Job Order not found.');
        }
        $jobOrder->update([
            'kategori_job' => $request->input('kategori_job'),
            'status_job' => 'ASSIGNED',
            'est_job_default' => $estimasi->waktu_job,
            'received_by' => auth()->user()->name,
            'received_at' => now(),
        ]);

        $nameUser = User::where('id', $id_user)->first() ?? User::where('id', $request->input('assigned_to_wo'))->first();
        // dd($nameUser);
        if (!$nameUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ]);
        }

        $assigned = AssignJobPrepress::where('id_job_order', $jobOrder->id)->first();
        if ($assigned) {
            // dd($assigned);
            $assigned->update([
                'id_user_pic' => $id_user ?? $request->input('assigned_to_wo'),
                'name_user_pic' => $nameUser->name,
            ]);
        } else {
            $assigned = AssignJobPrepress::create([
                'id_job_order' => $jobOrder->id,
                'id_user_pic' => $id_user ?? $request->input('assigned_to_wo'),
                'name_user_pic' => $nameUser->name,
                'est_waktu_job' => $estimasi->waktu_job,
                'id_jenis_job' => $estimasi->id,
                'created_by' => auth()->user()->name,
            ]);
        }

        // $assigned = AssignJobPrepress::create([
        //     'id_job_order' => $jobOrder->id,
        //     'id_user_pic' => $id_user ?? $request->input('assigned_to_wo'),
        //     'name_user_pic' => $nameUser->name,

        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Job Order assigned successfully.',
        ]);
    }

    public function assignJobOrderPrepressData($id)
    {
        // Logic to get data for assigning a job order
        // dd($id);
        $jobOrder = JobPrepress::with('attachmentJobOrder')->where('id', $id)->first();
        // dd($jobOrder);
        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order not found.']);
        }

        $machines = Machine::all();
        $users = User::where('divisi', auth()->user()->divisi)->get();
        $picUsers = User::where('divisi', '4')->where('jabatan', '!=', '4')->get();

        $handlingJobPrepress = HandlingJobPrepress::where('id_job_order', $id)->first();
        if ($handlingJobPrepress) {
            $inProgress = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'IN PROGRESS')->first();
            $finish = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'FINISH')->first();
            $approved = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'APPROVED')->first();
            $closed = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'CLOSED')->first();
            $paused = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'PAUSED')->first();
            $pending = HandlingJobPrepress::where('id_job_order', $id)->where('status_handling', 'PENDING')->latest()->first();
        } else {
            $inProgress = null;
            $finish = null;
            $approved = null;
            $closed = null;
            $paused = null;
            $pending = null;
        }

        return view('main.process.prepress.detail-job-order-assign-prepress', compact('jobOrder', 'machines', 'users', 'picUsers', 'handlingJobPrepress', 'inProgress', 'finish', 'approved', 'closed', 'paused', 'pending'));
    }

    public function submitProgressDataPrepress(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        $jobOrder = JobPrepress::find($data['id_job_order']);

        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order not found.']);
        }

        $notifyPriority = 0;


        if ($data['status_job'] == 'ASSIGNED') {
            $notifyPriority = 1;
        } else if ($data['status_job'] == 'DATA TERISI') {
            $notifyPriority = 2;
        } else if ($data['status_job'] == 'FINISH') {
            $notifyPriority = 3;
        } else if ($data['status_job'] == 'APPROVED') {
            $notifyPriority = 4;
        } else if ($data['status_job'] == 'CLOSED') {
            $notifyPriority = 5;
        } else if ($data['status_job'] == 'REJECT') {
            $notifyPriority = 6;
        } else if ($data['status_job'] == 'PAUSED') {
            $notifyPriority = 7;
        } else if ($data['status_job'] == 'COMPLETED') {
            $notifyPriority = 8;
        } else if ($data['status_job'] == 'CHANGE') {
            $notifyPriority = 9;
        } else if ($data['status_job'] == 'DISSAPPROVE') {
            $notifyPriority = 10;
        }

        if ($request->file('file')) {
            $file = $request->file('file');
            $file_data = [];
            foreach ($file as $item) {
                $fileName = time() . '.' . $item->getClientOriginalName();
                $item->move(public_path('uploads'), $fileName);
                $file_data[] = $fileName;

                $attachment = AttachmentJobOrder::create([
                    'id_job_order' => $data['id_job_order'],
                    'file_name' => $fileName,
                    'file_path' => $fileName,
                    'file_type' => $item->getClientOriginalExtension(),
                    'created_by' => auth()->user()->name,
                ]);
            }
        } else {
            $file_data = null;
        }

        // dd($data['status_job']);

        $pending_opt = $data['pending_opt'] ?? null;

        if ($pending_opt == 'shift_2') {
            for ($i = 0; $i < 2; $i++) {
                $handlingJobPrepress = HandlingJobPrepress::create([
                    'id_job_order' => $data['id_job_order'],
                    'status_handling' => $i == 0 ? 'FINISH' : 'PENDING',
                    'notify_priority' => $notifyPriority,
                    'id_user_handle' => auth()->user()->id,
                    'date_handling' => now(),
                    'name_user_handle' => auth()->user()->name,
                    'created_by' => auth()->user()->name,
                    'changed_by' => auth()->user()->name,
                    'reason' => $data['reason_pause'] ?? null,
                ]);
            }
        } else {

            if ($data['status_job'] == 'CHANGE') {
                // $est_real_time = $data['waktu_setup'] + $data['waktu_downtime'];
            } else {
                if ($data['status_job'] == 'REJECT') {
                    // Cari id berdasarkan nomor_job_order
                    $jobOrder = JobPrepress::where('nomor_job_order', $data['id_job_order'])->first();
                    if ($jobOrder) {
                        $handlingJobPrepress = HandlingJobPrepress::where('id_job_order', $jobOrder->id)->where('status_handling', 'FINISH')->first();
                        if ($handlingJobPrepress) {
                            $handlingJobPrepress->delete();
                        }
                    }
                } else if ($data['status_job'] == 'DISSAPPROVE') {
                    // Cari id berdasarkan nomor_job_order
                    $jobOrder = JobPrepress::where('nomor_job_order', $data['id_job_order'])->first();
                    if ($jobOrder) {
                        $handlingJobPrepress = HandlingJobPrepress::where('id_job_order', $jobOrder->id)->where('status_handling', 'APPROVED')->first();
                        if ($handlingJobPrepress) {
                            $handlingJobPrepress->delete();
                        }
                    }
                } else {
                    // Cari id berdasarkan nomor_job_order
                    $jobOrder = JobPrepress::where('id', $data['id_job_order'])->first();
                    // dd($jobOrder);
                    if ($jobOrder) {
                        $handlingJobPrepress = HandlingJobPrepress::create([
                            'id_job_order' => $jobOrder->id,
                            'status_handling' => $data['status_job'],
                            'notify_priority' => $notifyPriority,
                            'id_user_handle' => auth()->user()->id,
                            'date_handling' => now(),
                            'name_user_handle' => auth()->user()->name,
                            'created_by' => auth()->user()->name,
                            'changed_by' => auth()->user()->name,
                            'reason' => $data['reason_pause'] ?? null,
                        ]);
                    }
                }
            }
        }



        // Cari id berdasarkan nomor_job_order
        $dataJobOrder = JobPrepress::where('id', $data['id_job_order'])->first();
        $est_real_time = 0;



        if ($data['status_job'] == 'FINISH' && $dataJobOrder) {
            // Ambil data handling yang masih IN PROGRESS untuk job order tersebut
            $inProgress = HandlingJobPrepress::where('id_job_order', $dataJobOrder->id)
                ->where('status_handling', 'IN PROGRESS')
                ->first();

            if ($inProgress) {
                $finish = HandlingJobPrepress::where('id_job_order', $dataJobOrder->id)
                    ->where('status_handling', 'FINISH')
                    ->first();

                if ($finish) {
                    $finishTime = $finish->date_handling;
                } else {
                    $finishTime = 0;
                }
                $startTime = strtotime($inProgress->date_handling); // waktu mulai handling (timestamp)
                $endTime = strtotime($finishTime); // waktu sekarang (timestamp)

                // Hitung selisih waktu dalam detik
                $diffSeconds = $endTime - $startTime;

                // dd($diffSeconds);

                // Ambil waktu setup dan downtime dalam menit, default 0
                $setupTimeMinutes = $data['waktu_setup'] ?? 0;
                $downtimeMinutes = $data['waktu_downtime'] ?? 0;

                // Konversi setup dan downtime ke detik
                $setupSeconds = $setupTimeMinutes * 60;
                $downtimeSeconds = $downtimeMinutes * 60;



                // Hitung estimasi waktu sebenarnya dalam detik
                $est_real_time_seconds = $diffSeconds - $setupSeconds - $downtimeSeconds;

                // Pastikan hasilnya tidak negatif
                if ($est_real_time_seconds < 0) {
                    $est_real_time_seconds = 0;
                }

                // Jika ingin simpan dalam jam, konversi detik ke jam
                $est_real_time = $est_real_time_seconds;
            } else {
                $est_real_time = 0;
            }
        } else {
            $est_real_time = 0;
        }

        $two_shift = $dataJobOrder->two_shift ?? null;
        // dd($two_shift);

        if ($dataJobOrder) {
            if ($data['status_job'] == 'CHANGE') {
                // dd('1');

                if ($two_shift == 1) {

                    // dd($data);
                    // Ambil data setup dan downtime untuk shift 1 dan shift 2
                    $shift1_setup = $data['waktu_setup_shift'] ?? 0;
                    $shift1_downtime = $data['waktu_downtime_shift'] ?? 0;
                    $catatan_shift = $data['catatan_shift'] ?? null;

                    $shift2_setup = $data['waktu_setup_shift_2'] ?? 0;
                    $shift2_downtime = $data['waktu_downtime_shift_2'] ?? 0;
                    $catatan_shift_2 = $data['catatan_shift_2'] ?? null;

                    $est_job_setup = implode(',', [$shift1_setup, $shift2_setup]);
                    $est_job_downtime = implode(',', [$shift1_downtime, $shift2_downtime]);
                    $catatan_job = implode(',', [$catatan_shift, $catatan_shift_2]);

                    // dd($est_job_setup, $est_job_downtime, $catatan_job);

                    $dataJobOrder->update([
                        'status_job' => 'COMPLETED',
                        'catatan_job' => $catatan_job,
                        'est_job_setup' => $est_job_setup, // Setup yang digabungkan
                        'est_job_downtime' => $est_job_downtime, // Downtime yang digabungkan
                        'est_job_realtime' => $est_real_time,
                        'two_shift' => $two_shift ?? '',
                    ]);
                } else {

                    // dd('CHANGE');
                    $dataJobOrder->update([
                        'catatan_job' => $data['catatan'] ?? null,
                        'est_job_setup' => $data['waktu_setup'],
                        'est_job_downtime' => $data['waktu_downtime'],
                        'est_job_realtime' => $est_real_time,
                        'status_job' => 'COMPLETED',
                        'two_shift' => $two_shift ?? '',
                    ]);
                }
            } else {
                // dd('2');
                if ($data['status_job'] == 'REJECT') {
                    if ($two_shift == 1) {
                        $dataJobOrder->update([
                            'status_job' => 'COMPLETED'
                        ]);
                    } else {
                        $dataJobOrder->update([
                            'status_job' => 'COMPLETED',
                            'catatan_job' => $data['catatan'] ?? null,
                            'two_shift' => $two_shift ?? '',
                        ]);
                    }
                } else if ($data['status_job'] == 'DISSAPPROVE') {
                    if ($two_shift == 1) {
                        $dataJobOrder->update([
                            'status_job' => 'FINISH'
                        ]);
                    } else {
                        $dataJobOrder->update([
                            'status_job' => 'FINISH',
                            'catatan_job' => $data['catatan'] ?? null,
                            'two_shift' => $two_shift ?? '',
                        ]);
                    }
                } else {

                    $dataPending = $data['pending_opt'] ?? null;

                    // two shift
                    if ($dataPending == 'shift_2') {
                        if ($data['status_job'] == 'IN PROGRESS') {
                            $dataJobOrder->update([
                                'status_job' => 'IN PROGRESS',
                                'two_shift' => '1' ?? $two_shift,
                            ]);
                        } else {
                            $dataJobOrder->update([
                                'status_job' => 'SHIFT_2',
                                'catatan_job' => $data['catatan'] ?? null,
                                'two_shift' => '1' ?? $two_shift,
                            ]);
                        }
                    } else {

                        // Jika two_shift bernilai 1, kita akan menambahkan setup dan downtime untuk shift 2.
                        if ($two_shift == 1) {
                            // dd($data);

                            if ($data['status_job'] == 'APPROVED') {
                                $dataJobOrder->update([
                                    'status_job' => 'APPROVED',
                                ]);

                                // Debug log
                                Log::info("Job APPROVED (two_shift=1): {$dataJobOrder->nomor_job_order}, calling sendFinishPrepressNotification");

                                // Kirim notifikasi email ketika job di-APPROVED
                                // $this->sendFinishPrepressNotification($dataJobOrder);

                                // if job code berawalan DEV- , update status di tb_job_order_developments dan tb_handling_developments
                                if (strpos($dataJobOrder->nomor_job_order, 'DEV-') === 0) {
                                    Log::info("Processing DEV job (two_shift): {$dataJobOrder->nomor_job_order}");

                                    // Update status di tb_job_order_developments
                                    $updatedRows = DB::table('tb_job_order_developments')
                                        ->where('job_code', $dataJobOrder->nomor_job_order)
                                        ->update(['status_job' => 'FINISH_PREPRESS']);

                                    Log::info("Updated {$updatedRows} rows in tb_job_order_developments for job: {$dataJobOrder->nomor_job_order}");

                                    // Insert ke tb_handling_developments untuk tracking
                                    DB::table('tb_handling_developments')->insert([
                                        'job_development_id' => DB::table('tb_job_order_developments')
                                            ->where('job_code', $dataJobOrder->nomor_job_order)
                                            ->value('id'),
                                        'action_type' => 'status_change',
                                        'action_description' => 'Job Prepress Approved - Status Updated to FINISH_PREPRESS',
                                        'status_before' => 'IN_PROGRESS_PREPRESS',
                                        'status_after' => 'FINISH_PREPRESS',
                                        'action_data' => json_encode([
                                            'prepress_approved' => true,
                                            'approved_by' => auth()->user()->name,
                                            'approved_at' => now()->format('Y-m-d H:i:s')
                                        ]),
                                        'action_time' => now(),
                                        'performed_by' => auth()->user()->id,
                                        'performed_by_name' => auth()->user()->name,
                                        'notes' => 'Job prepress telah disetujui dan status development diupdate ke FINISH_PREPRESS',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);

                                    Log::info("Updated DEV job status to FINISH_PREPRESS: {$dataJobOrder->nomor_job_order}");
                                }
                            } else if ($data['status_job'] == 'CLOSED') {
                                $dataJobOrder->update([
                                    'status_job' => 'CLOSED'
                                ]);
                            } else {
                                $shift1_setup = $data['waktu_setup_shift_1'] ?? 0;
                                $shift1_downtime = $data['waktu_downtime_shift_1'] ?? 0;

                                $shift2_setup = $data['waktu_setup_value'] ?? 0;
                                $shift2_downtime = $data['waktu_downtime_shift'] ?? 0;

                                $est_job_setup = implode(',', [$shift1_setup, $shift2_setup]);
                                $est_job_downtime = implode(',', [$shift1_downtime, $shift2_downtime]);

                                $dataJobOrder->update([
                                    'status_job' => $data['status_job'],
                                    'catatan_job' => $data['catatan'] ?? null,
                                    'est_job_setup' => $est_job_setup, // Setup yang digabungkan
                                    'est_job_downtime' => $est_job_downtime, // Downtime yang digabungkan
                                    'est_job_realtime' => $est_real_time,
                                    'two_shift' => $two_shift ?? '',
                                ]);
                            }
                        } else {
                            // Jika hanya satu shift, cukup gunakan nilai yang ada
                            $dataJobOrder->update([
                                'status_job' => $data['status_job'],
                                'catatan_job' => $data['catatan'] ?? null,
                                'est_job_setup' => $data['waktu_setup'] ?? 0,
                                'est_job_downtime' => $data['waktu_downtime'] ?? 0,
                                'est_job_realtime' => $est_real_time,
                                'two_shift' => $two_shift ?? '',
                            ]);

                            // Kirim notifikasi email jika status APPROVED (untuk single shift)
                            if ($data['status_job'] == 'APPROVED') {
                                // Debug log
                                Log::info("Job APPROVED (single shift): {$dataJobOrder->nomor_job_order}, calling sendFinishPrepressNotification");

                                // Kirim notifikasi email ketika job di-APPROVED
                                // $this->sendFinishPrepressNotification($dataJobOrder);

                                // if job code berawalan DEV- , update status di tb_job_order_developments dan tb_handling_developments
                                if (strpos($dataJobOrder->nomor_job_order, 'DEV-') === 0) {
                                    Log::info("Processing DEV job (single shift): {$dataJobOrder->nomor_job_order}");

                                    // Update status di tb_job_order_developments
                                    $updatedRows = DB::table('tb_job_order_developments')
                                        ->where('job_code', $dataJobOrder->nomor_job_order)
                                        ->update(['status_job' => 'FINISH_PREPRESS']);

                                    Log::info("Updated {$updatedRows} rows in tb_job_order_developments for job: {$dataJobOrder->nomor_job_order}");

                                    // Insert ke tb_handling_developments untuk tracking
                                    DB::table('tb_handling_developments')->insert([
                                        'job_development_id' => DB::table('tb_job_order_developments')
                                            ->where('job_code', $dataJobOrder->nomor_job_order)
                                            ->value('id'),
                                        'action_type' => 'status_change',
                                        'action_description' => 'Job Prepress Approved - Status Updated to FINISH_PREPRESS',
                                        'status_before' => 'IN_PROGRESS_PREPRESS',
                                        'status_after' => 'FINISH_PREPRESS',
                                        'action_data' => json_encode([
                                            'prepress_approved' => true,
                                            'approved_by' => auth()->user()->name,
                                            'approved_at' => now()->format('Y-m-d H:i:s')
                                        ]),
                                        'action_time' => now(),
                                        'performed_by' => auth()->user()->id,
                                        'performed_by_name' => auth()->user()->name,
                                        'notes' => 'Job prepress telah disetujui dan status development diupdate ke FINISH_PREPRESS',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);

                                    Log::info("Updated DEV job status to FINISH_PREPRESS (single shift): {$dataJobOrder->nomor_job_order}");
                                }
                            }
                        }
                    }
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'Progress submitted successfully.']);
    }

    public function deleteJobOrderPrepressPlan($id)
    {
        $jobOrder = JobPrepress::find($id);
        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order not found.']);
        }
        $jobOrder->forceDelete();
        return response()->json(['success' => true, 'message' => 'Job Order deleted successfully.']);
    }









    public function jobOrderPrepressDetail($id)
    {
        // Logic to display details of a specific job order for prepress
        // return view('main.process.prepress.job-order-prepress-detail', ['id' => $id]);
    }
    public function updateJobOrderPrepress(Request $request, $id)
    {
        // Logic to update a specific job order for prepress
        // Validate and process the request data
        return redirect()->route('prepress.job-order.index')->with('success', 'Job Order updated successfully.');
    }
    public function deleteJobOrderPrepress($id)
    {
        // Logic to delete a specific job order for prepress
        return redirect()->route('prepress.job-order.index')->with('success', 'Job Order deleted successfully.');
    }



    public function submitJobOrderPrepressApproval(Request $request)
    {
        $data = $request->all();
        dd($data);


        return redirect()->route('prepress.job-order.index')->with('success', 'Job Order submitted for approval successfully.');
    }
    public function approveJobOrderPrepress(Request $request)
    {
        // Logic to approve a job order for prepress
        // Validate and process the request data
        return redirect()->route('prepress.job-order.index')->with('success', 'Job Order approved successfully.');
    }
    public function rejectJobOrderPrepress(Request $request)
    {
        try {
            $data = $request->all();

            // dd($data);

            // Validasi input
            $request->validate([
                'id_job_reject' => 'required|integer',
                'alasan_reject' => 'required|string|max:1000'
            ]);

            $jobOrder = JobPrepress::where('id', $data['id_job_reject'])->first();

            if (!$jobOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job Order tidak ditemukan.',
                ], 404);
            }

            // Update status job menjadi OPEN (dikembalikan ke user) dan simpan alasan reject
            $jobOrder->update([
                'status_job' => 'OPEN',
                'reason_reject' => 'REJECTED BY PREPRESS: ' . $data['alasan_reject'],
                // 'rejected_by_prepress' => true,
                'rejected_at' => now(),
                // 'rejection_reason' => $data['alasan_reject'],
                'rejected_by' => Auth::user()->name,
            ]);

            // Log aktivitas
            Log::info("Job Order rejected: {$jobOrder->nomor_job_order} - Reason: {$data['alasan_reject']}");

            // Kirim notifikasi email ke user yang membuat job order
            try {
                $this->sendRejectionNotification($jobOrder, $data['alasan_reject']);
            } catch (\Exception $e) {
                Log::error('Failed to send rejection notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Job Order berhasil di-reject dan dikembalikan ke user untuk revisi.',
                'id' => $jobOrder->id,
                'nomor_job_order' => $jobOrder->nomor_job_order,
                'alasan_reject' => $jobOrder->catatan_job
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting job order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat me-reject job order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectJobOrderPrepressData(Request $request)
    {
        $data = $request->all();
        dd($data);
        $jobOrder = JobPrepress::where('id', $data['id'])->first();
        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order tidak ditemukan.']);
        }
        return response()->json(['success' => true, 'message' => 'Job Order rejected successfully.']);
    }

    public function getTimelineData(Request $request)
    {
        $date = $request->input('date');
        // dd($date);

        // Base query - get all job orders with received_at and est_job_default
        $query = JobPrepress::whereNotNull('received_at')->whereNotNull('est_job_default');

        // If date is provided and not empty, filter by date
        if (!empty($date) && $date !== 'null') {
            $query->whereDate('tanggal_job_order', $date);
        }

        $jobOrders = $query->where('status_job', 'IN PROGRESS')->orderBy('received_at', 'asc')->get();

        // dd($jobOrders);

        $timelineData = [];

        foreach ($jobOrders as $job) {
            // Calculate start time (received_at)
            $startTime = Carbon::parse($job->received_at);

            // Calculate end time (received_at + est_job_default minutes)
            $endTime = $startTime->copy()->addMinutes(intval($job->est_job_default));

            $timelineData[] = [
                'id' => $job->id,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'duration' => $job->est_job_default,
                'kode_design' => $job->kode_design,
                'product' => $job->product,
                'customer' => $job->customer,
                'job_order' => $job->job_order,
                'qty_order_estimation' => $job->qty_order_estimation,
                'status_job' => $job->status_job,
                'received_at' => $job->received_at,
                'est_job_default' => $job->est_job_default,
                'tanggal_job_order' => $job->tanggal_job_order
            ];
        }

        // dd($timelineData);

        return response()->json([
            'success' => true,
            'data' => $timelineData
        ]);
    }

    public function deleteAttachmentJobOrder($id)
    {
        $attachment = AttachmentJobOrder::find($id);
        $attachment->delete();
        return response()->json(['success' => true, 'message' => 'Attachment deleted successfully.']);
    }

    // Workflow Methods
    public function pauseProgressDataPrepress(Request $request)
    {
        $data = $request->all();
        $jobOrder = JobPrepress::find($data['id_job_order']);

        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order tidak ditemukan.']);
        }

        $pauseReason = $data['pause_reason'];
        $newStatus = '';

        if ($pauseReason === 'shift_2') {
            // Shift 2 dengan PIC berbeda
            $newStatus = 'SHIFT_2';

            // Assign ke PIC Shift 2
            $picShift2 = $data['pic_shift_2'];
            AssignJobPrepress::create([
                'id_job_order' => $data['id_job_order'],
                'id_user_pic' => $picShift2,
                'assigned_by' => auth()->user()->id,
                'assigned_at' => now(),
                'status' => 'ASSIGNED'
            ]);

            // Update status job order
            $jobOrder->update([
                'status_job' => 'ASSIGNED',
                'catatan_job' => 'Shift 2 - Dilanjutkan oleh PIC berbeda'
            ]);

            // Catat handling untuk user pertama
            HandlingJobPrepress::create([
                'id_job_order' => $data['id_job_order'],
                'status_handling' => 'FINISH',
                'notify_priority' => 3,
                'id_user_handle' => auth()->user()->id,
                'date_handling' => now(),
                'name_user_handle' => auth()->user()->name,
                'created_by' => auth()->user()->name,
                'changed_by' => auth()->user()->name,
            ]);

            $message = 'Job berhasil di-pause dan dialihkan ke Shift 2.';
        } else {
            // Pause karena job urgent
            $newStatus = 'PENDING';

            $jobOrder->update([
                'status_job' => 'PENDING',
                'catatan_job' => 'Pause - Mengerjakan job urgent'
            ]);

            // Catat handling
            HandlingJobPrepress::create([
                'id_job_order' => $data['id_job_order'],
                'status_handling' => 'PAUSED',
                'notify_priority' => 1,
                'id_user_handle' => auth()->user()->id,
                'date_handling' => now(),
                'name_user_handle' => auth()->user()->name,
                'created_by' => auth()->user()->name,
                'changed_by' => auth()->user()->name,
            ]);

            $message = 'Job berhasil di-pause karena job urgent.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function resumeProgressDataPrepress(Request $request)
    {
        $data = $request->all();
        $jobOrder = JobPrepress::find($data['id_job_order']);

        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order tidak ditemukan.']);
        }

        // Update status kembali ke IN PROGRESS
        $jobOrder->update([
            'status_job' => 'IN PROGRESS',
            'catatan_job' => 'Resume - Pengerjaan dilanjutkan'
        ]);

        // Catat handling resume
        HandlingJobPrepress::create([
            'id_job_order' => $data['id_job_order'],
            'status_handling' => 'IN PROGRESS',
            'notify_priority' => 1,
            'id_user_handle' => auth()->user()->id,
            'date_handling' => now(),
            'name_user_handle' => auth()->user()->name,
            'created_by' => auth()->user()->name,
            'changed_by' => auth()->user()->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Job berhasil di-resume.']);
    }

    public function finishProgressDataPrepress(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        $jobOrder = JobPrepress::find($data['id_job_order']);

        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order tidak ditemukan.']);
        }

        // Handle file uploads
        if ($request->file('file')) {
            $file = $request->file('file');
            foreach ($file as $item) {
                $fileName = time() . '_' . $item->getClientOriginalName();
                $item->move(public_path('uploads'), $fileName);

                AttachmentJobOrder::create([
                    'id_job_order' => $data['id_job_order'],
                    'file_name' => $item->getClientOriginalName(),
                    'file_path' => $fileName,
                    'file_type' => $item->getClientOriginalExtension(),
                    'created_by' => auth()->user()->name,
                ]);
            }
        }

        // Hitung estimasi real time
        $inProgress = HandlingJobPrepress::where('id_job_order', $jobOrder->id)
            ->where('status_handling', 'IN PROGRESS')
            ->first();

        $two_shift = $jobOrder->two_shift ?? null;

        if ($two_shift == 1) {
            $est_job_setup = $data['waktu_setup_shift_2'] ?? 0;
            $est_job_downtime = $data['waktu_downtime_shift_2'] ?? 0;
        } else {
            $est_job_setup = $data['waktu_setup'] ?? 0;
            $est_job_downtime = $data['waktu_downtime'] ?? 0;
        }

        $estRealTime = 0;

        if ($inProgress) {
            $startTime = strtotime($inProgress->date_handling);
            $endTime = time();
            $diffSeconds = $endTime - $startTime;

            $setupTimeMinutes = intval($est_job_setup) ?? 0;
            $downtimeMinutes = intval($est_job_downtime) ?? 0;

            $setupSeconds = $setupTimeMinutes * 60;
            $downtimeSeconds = $downtimeMinutes * 60;

            $estRealTime = $diffSeconds - $setupSeconds - $downtimeSeconds;
        }

        // dd($est_job_setup, $est_job_downtime, $estRealTime);

        // Update job order

        if ($two_shift == 1) {
            $estRealTime = $jobOrder->est_job_realtime + $estRealTime;

            $jobOrder->update([
                'status_job' => 'FINISH',
                // 'catatan_job' => $data['catatan'] ?? '',
                // 'est_job_setup' => $est_job_setup ?? 0,
                // 'est_job_downtime' => $est_job_downtime ?? 0,
                'est_job_realtime' => $estRealTime ?? 0
            ]);
        } else {
            $jobOrder->update([
                'status_job' => 'FINISH',
                'catatan_job' => $data['catatan'] ?? '',
                'est_job_setup' => $est_job_setup ?? 0,
                'est_job_downtime' => $est_job_downtime ?? 0,
                'est_job_realtime' => $estRealTime
            ]);
        }


        // Catat handling finish
        HandlingJobPrepress::create([
            'id_job_order' => $jobOrder->id,
            'status_handling' => 'FINISH',
            'notify_priority' => 3,
            'id_user_handle' => auth()->user()->id,
            'date_handling' => now(),
            'name_user_handle' => auth()->user()->name,
            'created_by' => auth()->user()->name,
            'changed_by' => auth()->user()->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Job berhasil diselesaikan.']);
    }

    public function saveCatatanProgressDataPrepress(Request $request)
    {
        $data = $request->all();
        // Cari id berdasarkan nomor_job_order
        $jobOrder = JobPrepress::where('nomor_job_order', $data['id_job_order'])->first();

        if (!$jobOrder) {
            return response()->json(['success' => false, 'message' => 'Job Order tidak ditemukan.']);
        }

        // Update catatan
        $jobOrder->update([
            'catatan_job' => $data['catatan'] ?? ''
        ]);

        return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan.']);
    }

    public function deleteJobOrderPrepressWO($id)
    {
        $jobOrder = JobPrepress::find($id);
        // delete in assign job prepress
        AssignJobPrepress::where('id_job_order', $id)->delete();
        // delete in handling job prepress
        HandlingJobPrepress::where('id_job_order', $id)->delete();
        // delete in attachment job order
        AttachmentJobOrder::where('id_job_order', $id)->delete();
        // delete in job order prepress
        $jobOrder->delete();
        return response()->json(['success' => true, 'message' => 'Job Order berhasil dihapus.']);
    }

    public function exportJobOrderPrepressPlan(Request $request)
    {
        $selectedDate = $request->input('selected_date');
        // dd($selectedDate);

        if (!$selectedDate) {
            return response()->json(['success' => false, 'message' => 'Tanggal harus dipilih']);
        }

        // Ambil data job order berdasarkan tanggal yang dipilih
        // Coba gunakan planned_at dulu, jika tidak ada data gunakan tanggal_job_order
        $jobOrders = JobPrepress::where(function ($query) use ($selectedDate) {
            $query->whereDate('planned_at', $selectedDate)
                ->orWhereDate('received_at', $selectedDate);
        })
            ->with(['handlingJobPrepress', 'assignJobPrepress'])
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($jobOrders);

        if ($jobOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data plan untuk tanggal ' . $selectedDate]);
        }

        // Buat nama file
        $fileName = 'Plan_Prepress_' . date('Y-m-d', strtotime($selectedDate)) . '.xlsx';

        return Excel::download(new class($jobOrders) implements FromCollection, WithHeadings, WithMapping {
            private $jobOrders;

            public function __construct($jobOrders)
            {
                $this->jobOrders = $jobOrders;
            }

            public function collection()
            {
                return $this->jobOrders;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Tanggal Job Order',
                    'Deadline',
                    'Customer',
                    'Product',
                    'Kode Design',
                    'Job Order',
                    'Qty Order',
                    'Status Job',
                    'Prioritas',
                    'Created By',
                    'Created At',
                    'Assigned To',
                    'Notes'
                ];
            }

            public function map($jobOrder): array
            {
                static $no = 1;

                $assignedTo = $jobOrder->assignJobPrepress ? $jobOrder->assignJobPrepress->name_user_pic : '-';
                $notes = $jobOrder->catatan ?: '-';

                return [
                    $no++,
                    $jobOrder->tanggal_job_order ? date('d/m/Y', strtotime($jobOrder->tanggal_job_order)) : '-',
                    $jobOrder->tanggal_deadline ? date('d/m/Y H:i', strtotime($jobOrder->tanggal_deadline)) : '-',
                    $jobOrder->customer ?: '-',
                    $jobOrder->product ?: '-',
                    $jobOrder->kode_design ?: '-',
                    $jobOrder->job_order ?: '-',
                    $jobOrder->qty_order_estimation ?: '-',
                    $jobOrder->status_job ?: '-',
                    $jobOrder->prioritas_job ?: '-',
                    $jobOrder->created_by ?: '-',
                    $jobOrder->created_at ? date('d/m/Y H:i', strtotime($jobOrder->created_at)) : '-',
                    $assignedTo,
                    $notes
                ];
            }
        }, $fileName);
    }

    /**
     * Get PIC load data for dashboard
     * Menggunakan rumus produktivitas SIPO per hari:
     * Produktivitas = (Waktu realtime untuk Job yang Diselesaikan : 450 menit) × 100%
     * Target = (Master total waktu yang di assigned : 450 menit) × 100%
     * Overtime pengerjaan = Produktivitas - Target
     */
    public function getPicLoadData()
    {
        try {
            $picUsers = User::where('divisi', '3')->get();

            $picLoads = [];
            $totalJobs = 0;
            $highTargetCount = 0;
            $totalTargetPercentage = 0;

            $waktuKerjaTersediaPerHari = 450;

            foreach ($picUsers as $user) {
                $jobCounts = DB::table('tb_assign_job_prepresses')
                    ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                    ->where('tb_assign_job_prepresses.id_user_pic', $user->id)
                    ->selectRaw('tb_job_prepresses.status_job, COUNT(*) as count')
                    ->groupBy('tb_job_prepresses.status_job')
                    ->pluck('count', 'tb_job_prepresses.status_job')
                    ->toArray();

                // dd($jobCounts);

                $totalJobsForUser = array_sum($jobCounts);
                $totalJobs += $totalJobsForUser;

                // Cari total load aktif (job yang belum selesai)
                $loadAktif = DB::table('tb_assign_job_prepresses')
                    ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                    ->where('tb_assign_job_prepresses.id_user_pic', $user->id)
                    ->whereIn('tb_job_prepresses.status_job', ['ASSIGNED', 'IN PROGRESS', 'COMPLETED']) // Job yang masih aktif
                    ->sum(DB::raw('CAST(tb_job_prepresses.est_job_default AS INTEGER)'));

                // Cari total job yang sudah selesai hari ini
                $loadSelesaiHariIni = DB::table('tb_assign_job_prepresses')
                    ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                    ->where('tb_assign_job_prepresses.id_user_pic', $user->id)
                    ->whereIn('tb_job_prepresses.status_job', ['FINISH', 'APPROVED'])
                    ->whereDate('tb_job_prepresses.finished_at', today())
                    ->sum(DB::raw('CAST(tb_job_prepresses.est_job_default AS INTEGER)'));

                // Total load hari ini = load aktif + load yang sudah selesai hari ini
                $totalWaktuAssigned = $loadAktif + $loadSelesaiHariIni;

                // Cari job yang diselesaikan oleh user ini pada hari ini
                $waktuRealtimeSelesai = DB::table('tb_assign_job_prepresses')
                    ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                    ->where('tb_assign_job_prepresses.id_user_pic', $user->id)
                    ->whereIn('tb_job_prepresses.status_job', ['FINISH', 'APPROVED'])
                    ->whereDate('tb_job_prepresses.finished_at', today())
                    ->sum(DB::raw('CAST(tb_job_prepresses.est_job_realtime AS INTEGER)'));

                $jobsSelesai = ($jobCounts['FINISH'] ?? 0) + ($jobCounts['APPROVED'] ?? 0);

                // Rumus produktivitas SIPO per hari
                $produktivitas = round(($waktuRealtimeSelesai / $waktuKerjaTersediaPerHari) * 100);
                $target = round(($totalWaktuAssigned / $waktuKerjaTersediaPerHari) * 100);
                $overtime = $produktivitas - $target;

                $totalTargetPercentage += $target;

                if ($target >= 80) {
                    $highTargetCount++;
                }

                $picLoads[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_jobs' => $totalJobsForUser,
                    'jobs_selesai' => $jobsSelesai,
                    'waktu_realtime_selesai' => $waktuRealtimeSelesai, // Data untuk rumus produktivitas
                    'total_waktu_assigned' => $totalWaktuAssigned, // Total load hari ini (aktif + selesai)
                    'load_aktif' => $loadAktif, // Load yang masih aktif
                    'load_selesai_hari_ini' => $loadSelesaiHariIni, // Load yang selesai hari ini
                    'produktivitas' => $produktivitas,
                    'target' => $target,
                    'overtime' => $overtime,
                    'status_counts' => [
                        'ASSIGNED' => $jobCounts['ASSIGNED'] ?? 0,
                        'IN_PROGRESS' => $jobCounts['IN PROGRESS'] ?? 0,
                        'FINISH' => $jobCounts['FINISH'] ?? 0,
                        'APPROVED' => $jobCounts['APPROVED'] ?? 0,
                        'CLOSED' => $jobCounts['CLOSED'] ?? 0,
                        'COMPLETED' => $jobCounts['COMPLETED'] ?? 0,
                    ]
                ];
                // dd($picLoads);
            }

            // Calculate summary
            $totalPic = count($picLoads);
            $avgTarget = $totalPic > 0 ? round($totalTargetPercentage / $totalPic) : 0;

            $summary = [
                'total_jobs' => $totalJobs,
                'total_pic' => $totalPic,
                'avg_target' => $avgTarget,
                'high_target_pic' => $highTargetCount
            ];
            // dd($picLoads);

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'pic_loads' => $picLoads
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting PIC load data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail jobs for specific PIC
     */
    public function getDetailJobs($id)
    {
        // dd($id);
        try {
            // Get load breakdown for this PIC
            $loadAktif = DB::table('tb_assign_job_prepresses')
                ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                ->where('tb_assign_job_prepresses.id_user_pic', $id)
                ->whereIn('tb_job_prepresses.status_job', ['ASSIGNED', 'IN PROGRESS', 'COMPLETED']) // Job yang masih aktif
                ->sum(DB::raw('CAST(tb_job_prepresses.est_job_default AS INTEGER)'));

            // Cari total job yang sudah selesai hari ini
            $loadSelesaiHariIni = DB::table('tb_assign_job_prepresses')
                ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                ->where('tb_assign_job_prepresses.id_user_pic', $id)
                ->whereIn('tb_job_prepresses.status_job', ['FINISH', 'APPROVED'])
                ->whereDate('tb_job_prepresses.finished_at', today())
                ->sum(DB::raw('CAST(tb_job_prepresses.est_job_default AS INTEGER)'));

            // Total load = semua waktu yang di-assign ke PIC (kecuali CLOSED dan APPROVED)
            $totalLoad = DB::table('tb_assign_job_prepresses')
                ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                ->where('tb_assign_job_prepresses.id_user_pic', $id)
                ->whereNotIn('tb_job_prepresses.status_job', ['CLOSED', 'APPROVED'])
                ->sum(DB::raw('CAST(tb_job_prepresses.est_job_default AS INTEGER)'));

            // dd($totalLoad);

            // Total waktu assigned untuk dashboard (load aktif + selesai hari ini)
            $totalWaktuAssigned = $loadAktif + $loadSelesaiHariIni;

            // Get active jobs (not finished) for this PIC
            $activeJobs = DB::table('tb_assign_job_prepresses')
                ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                ->where('tb_assign_job_prepresses.id_user_pic', $id)
                ->whereNotIn('tb_job_prepresses.status_job', ['APPROVED', 'CLOSED'])
                ->select(
                    'tb_job_prepresses.id',
                    'tb_job_prepresses.nomor_job_order',
                    'tb_job_prepresses.customer',
                    'tb_job_prepresses.product',
                    'tb_job_prepresses.status_job',
                    'tb_job_prepresses.finished_at as tanggal_selesai',
                    'tb_job_prepresses.catatan',
                    'tb_job_prepresses.est_job_realtime as waktu_realtime',
                    'tb_job_prepresses.est_job_default as waktu_default',
                    DB::raw("'active' as job_type")
                )
                ->orderBy('tb_job_prepresses.created_at', 'desc')
                ->get();

            // Get jobs finished today for this PIC
            $finishedJobs = DB::table('tb_assign_job_prepresses')
                ->join('tb_job_prepresses', 'tb_assign_job_prepresses.id_job_order', '=', 'tb_job_prepresses.id')
                ->where('tb_assign_job_prepresses.id_user_pic', $id)
                ->whereIn('tb_job_prepresses.status_job', ['FINISH', 'APPROVED'])
                ->whereDate('tb_job_prepresses.finished_at', today())
                ->select(
                    'tb_job_prepresses.id',
                    'tb_job_prepresses.nomor_job_order',
                    'tb_job_prepresses.customer',
                    'tb_job_prepresses.product',
                    'tb_job_prepresses.status_job',
                    'tb_job_prepresses.finished_at as tanggal_selesai',
                    'tb_job_prepresses.catatan',
                    'tb_job_prepresses.est_job_realtime as waktu_realtime',
                    'tb_job_prepresses.est_job_default as waktu_default',
                    DB::raw("'finished' as job_type")
                )
                ->orderBy('tb_job_prepresses.finished_at', 'desc')
                ->get();

            // Combine both job types
            $jobs = $activeJobs->concat($finishedJobs);

            return response()->json([
                'success' => true,
                'load_breakdown' => [
                    'load_aktif' => $loadAktif,
                    'load_selesai_hari_ini' => $loadSelesaiHariIni,
                    'total_load' => $totalLoad,
                    'total_waktu_assigned' => $totalWaktuAssigned
                ],
                'jobs' => $jobs
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting detail jobs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading detail jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim notifikasi email ketika job di-reject oleh prepress
     */
    private function sendRejectionNotification($jobOrder, $rejectionReason)
    {
        try {
            // Cari user yang membuat job order
            $user = User::where('name', $jobOrder->created_by)->first();

            if (!$user || !$user->email) {
                Log::warning("User not found or no email for job rejection notification: {$jobOrder->created_by}");
                return;
            }

            $data = [
                'job_order' => $jobOrder,
                'rejection_reason' => $rejectionReason,
                'user' => $user,
                'rejected_by' => auth()->user()->name,
                'rejected_at' => now()->format('d-m-Y H:i:s')
            ];

            // Kirim email notifikasi
            Mail::send('emails.job-rejection-notification', $data, function ($message) use ($user, $jobOrder) {
                $message->to($user->email, $user->name)
                    ->subject("Job Order Ditolak - {$jobOrder->nomor_job_order}");
            });

            Log::info("Rejection notification sent to: {$user->email} for job: {$jobOrder->nomor_job_order}");
        } catch (\Exception $e) {
            Log::error("Failed to send rejection notification: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kirim notifikasi email ketika job prepress di-APPROVED
     */
    private function sendFinishPrepressNotification($jobOrder)
    {
        try {
            Log::info("sendFinishPrepressNotification called for job: {$jobOrder->nomor_job_order}");

            $emailService = new DevelopmentEmailNotificationService();

            // Ambil data PIC yang mengerjakan job ini
            $assignedPic = DB::table('tb_assign_job_prepresses')
                ->join('users', 'tb_assign_job_prepresses.id_user_pic', '=', 'users.id')
                ->where('tb_assign_job_prepresses.id_job_order', $jobOrder->id)
                ->select('users.name', 'users.email')
                ->first();

            $jobData = [
                'id' => $jobOrder->id,
                'job_code' => $jobOrder->nomor_job_order,
                'job_name' => $jobOrder->product,
                'customer' => $jobOrder->customer,
                'product' => $jobOrder->product,
                'qty_order_estimation' => $jobOrder->qty,
                'prioritas_job' => $jobOrder->prioritas_job,
                'finished_at' => now()->format('Y-m-d H:i:s'),
                'pic_name' => $assignedPic ? $assignedPic->name : 'Unknown PIC',
                'pic_email' => $assignedPic ? $assignedPic->email : '',
                'catatan' => $jobOrder->catatan_job ?? ''
            ];

            $emailService->sendFinishPrepressNotification($jobData);


            Log::info("Finish prepress notification sent for job: {$jobOrder->nomor_job_order}");
        } catch (\Exception $e) {
            Log::error("Failed to send finish prepress notification for job {$jobOrder->nomor_job_order}: " . $e->getMessage());
        }
    }
}
