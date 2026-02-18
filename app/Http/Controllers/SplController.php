<?php

namespace App\Http\Controllers;

use App\Models\SplRequest;
use App\Models\SplEmployee;
use App\Models\PaytestEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SplController extends Controller
{
    /**
     * Display a listing of SPL requests
     */
    public function index()
    {
        $user = Auth::user();

        // Supervisor hanya bisa lihat SPL mereka sendiri
        $query = SplRequest::where('supervisor_id', $user->id)
            ->orderBy('created_at', 'desc');

        // HRD bisa lihat semua SPL yang sudah signed
        if ($user->isHR()) {
            $query = SplRequest::whereIn('status', [
                SplRequest::STATUS_SIGNED,
                SplRequest::STATUS_APPROVED_HRD,
                SplRequest::STATUS_REJECTED
            ])->orderBy('created_at', 'desc');
        }

        $splRequests = $query->paginate(20);

        return view('hr.spl.index', compact('splRequests'));
    }

    /**
     * Show the form for creating a new SPL
     */
    public function create()
    {
        $user = Auth::user();

        // Hanya supervisor yang bisa membuat SPL
        if (!$user->canApprove() && (int)($user->jabatan ?? 0) !== 5) {
            abort(403, 'Hanya Supervisor yang dapat membuat SPL.');
        }

        $employeeData = DB::connection('mysql7')->table('masteremployee')
        ->where('Nama', 'like', '%' . $user->name . '%')
        ->where('Begda', '<=', now())
        ->where(function($q) {
            $q->whereNull('Endda')
              ->orWhere('Endda', '>=', now());
        })
        ->select('Nama', 'Kode Divisi', 'Begda', 'Endda')
        ->orderBy('Nama')
        ->get();

        // dd($employeeData);

        // Get employees from masteremployee (payroll)
        $payrollEmployees = PaytestEmployee::active()
            ->where('Kode Divisi', $employeeData[0]->{'Kode Divisi'})
            ->orderBy('Nama')
            ->get(['Nip', 'Nama', 'Kode Divisi', 'Kode Bagian']);

        // Get employees from users table
        $users = User::where('divisi', $user->divisi)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'divisi']);

        return view('hr.spl.create', compact('payrollEmployees', 'users'));
    }

    /**
     * Store a newly created SPL
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'request_date' => 'required|date',
            'shift' => 'required|string|max:255',
            // Jam dipindah ke tiap karyawan
            'mesin' => 'nullable|string|max:255',
            'keperluan' => 'required|string',
            'employees' => 'required|array|min:1',
            'employees.*.employee_id' => 'nullable|exists:users,id',
            'employees.*.nip' => 'nullable|string|max:50',
            'employees.*.employee_name' => 'required|string|max:255',
            'employees.*.is_manual' => 'nullable',
            'employees.*.start_time' => 'required|date_format:H:i',
            'employees.*.end_time' => 'required|date_format:H:i',
        ]);

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Jam header tidak digunakan lagi; gunakan jam per karyawan
            $startTime = null;
            $endTime = null;

            // Create SPL request
            $splRequest = SplRequest::create([
                'spl_number' => SplRequest::generateSplNumber(),
                'request_date' => $validated['request_date'],
                'shift' => $validated['shift'],
                'start_time' => $startTime, // diset null; ringkasan bisa dihitung dari data karyawan
                'end_time' => $endTime,
                'mesin' => $validated['mesin'] ?? null,
                'keperluan' => $validated['keperluan'],
                'supervisor_id' => $user->id,
                'divisi_id' => $user->divisi,
                'status' => SplRequest::STATUS_PENDING, // Langsung pending untuk masuk approval flow
                'submitted_at' => now(),
                'current_approval_order' => 0, // Akan di-update saat approval
            ]);

            // Tidak lagi menyimpan jam header ke session

            // Create SPL employees
            foreach ($validated['employees'] as $employeeData) {
                // Convert is_manual to boolean
                $isManual = false;
                if (isset($employeeData['is_manual'])) {
                    $isManualValue = $employeeData['is_manual'];
                    if (is_string($isManualValue)) {
                        $isManual = in_array(strtolower($isManualValue), ['true', '1'], true);
                    } else {
                        $isManual = (bool) $isManualValue;
                    }
                }

                // Hitung start/end per karyawan sebagai timestamp dari request_date + HH:MM
                $empStart = null;
                $empEnd = null;
                if (!empty($employeeData['start_time'])) {
                    $empStart = \Carbon\Carbon::parse($validated['request_date'] . ' ' . $employeeData['start_time']);
                }
                if (!empty($employeeData['end_time'])) {
                    $empEnd = \Carbon\Carbon::parse($validated['request_date'] . ' ' . $employeeData['end_time']);
                    if ($empStart && $empEnd->lt($empStart)) {
                        $empEnd->addDay();
                    }
                }

                SplEmployee::create([
                    'spl_request_id' => $splRequest->id,
                    'employee_id' => !empty($employeeData['employee_id']) ? $employeeData['employee_id'] : null,
                    'nip' => !empty($employeeData['nip']) ? $employeeData['nip'] : null,
                    'employee_name' => trim($employeeData['employee_name']),
                    'is_manual' => $isManual,
                    'is_signed' => false,
                    'start_time' => $empStart,
                    'end_time' => $empEnd,
                ]);
            }

            DB::connection('pgsql2')->commit();

            return redirect()->route('hr.spl.index')
                ->with('success', 'SPL berhasil dibuat dan masuk ke alur approval.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            \Log::error('SPL Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified SPL
     */
    public function show($id)
    {
        $user = Auth::user();
        // dd($user);
        $splRequest = SplRequest::with(['employees', 'supervisor', 'head', 'manager', 'hrd'])->findOrFail($id);
        // dd($splRequest);

        // Check authorization
        if (!$user->isHR() && $splRequest->supervisor_id !== $user->id) {
            // Cek apakah user bisa approve (untuk approver)
            $approvalService = new \App\Services\SplApprovalService();
            try {
                $chain = $approvalService->getApprovalChain($splRequest);
                $canApprove = false;
                foreach ($chain as $level => $approverData) {
                    $users = $approverData['users'] ?? collect();
                    if ($users->contains('id', $user->id)) {
                        $canApprove = true;
                        break;
                    }
                }
                if (!$canApprove) {
                    abort(403, 'Anda tidak memiliki akses untuk melihat SPL ini.');
                }
            } catch (\Exception $e) {
                abort(403, 'Anda tidak memiliki akses untuk melihat SPL ini.');
            }
        }

        // Get approval flow untuk ditampilkan
        $approvalService = new \App\Services\SplApprovalService();
        $approvalFlow = null;
        try {
            $approvalFlow = $approvalService->getApprovalChain($splRequest);
        } catch (\Exception $e) {
            \Log::error('Error getting approval flow: ' . $e->getMessage());
        }

        // Cek apakah user bisa approve SPL ini
        $canApprove = false;
        $canDisapprove = false;
        $isPending = false;
        $currentApprovalLevel = null;

        if ($approvalFlow) {
            foreach ($approvalFlow as $level => $approverData) {
                $users = $approverData['users'] ?? collect();
                $roleKeyInChain = $approverData['role_key'] ?? null;

                if ($users->contains('id', $user->id)) {
                    $isPending = false;

                    if ($roleKeyInChain === 'spv_division') {
                        // SPV yang membuat sudah otomatis approve
                        continue;
                    } elseif ($roleKeyInChain === 'head_division') {
                        // head_division bisa di-approve oleh HEAD atau MANAGER
                        // Cek berdasarkan jabatan user
                        if ($user->jabatan == 3) {
                            // MANAGER cek manager_approved_at
                            $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                        } else {
                            // HEAD cek head_approved_at
                            $isPending = is_null($splRequest->head_approved_at) && is_null($splRequest->head_rejected_at);
                        }
                    } elseif ($roleKeyInChain === 'manager') {
                        $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                    } elseif ($roleKeyInChain === 'hr') {
                        $isPending = is_null($splRequest->hrd_approved_at) && is_null($splRequest->hrd_rejected_at);
                    }

                    // Cek apakah level sebelumnya sudah di-approve
                    $previousLevelsApproved = true;
                    foreach ($approvalFlow as $prevLevel => $prevApproverData) {
                        if ($prevLevel === $level) {
                            break;
                        }

                        $prevRoleKey = $prevApproverData['role_key'] ?? null;
                        $prevApproved = false;

                        if ($prevRoleKey === 'spv_division') {
                            $prevApproved = true; // SPV yang membuat sudah approve
                        } elseif ($prevRoleKey === 'head_division') {
                            // head_division bisa di-approve oleh HEAD atau MANAGER
                            // Cek apakah HEAD atau MANAGER sudah approve
                            $prevApproved = !is_null($splRequest->head_approved_at) || !is_null($splRequest->manager_approved_at);
                        } elseif ($prevRoleKey === 'manager') {
                            $prevApproved = !is_null($splRequest->manager_approved_at);
                        } elseif ($prevRoleKey === 'hr') {
                            $prevApproved = !is_null($splRequest->hrd_approved_at);
                        }

                        if (!$prevApproved) {
                            $previousLevelsApproved = false;
                            break;
                        }
                    }

                    if ($isPending && $previousLevelsApproved) {
                        $canApprove = true;
                        $canDisapprove = true;
                        $currentApprovalLevel = $roleKeyInChain;
                        break;
                    }
                }
            }
        }

        // Ambil start_time dan end_time dari database (jika ada), fallback ke session untuk backward compatibility
        $startTime = null;
        $endTime = null;

        if ($splRequest->start_time) {
            $startTime = $splRequest->start_time->format('H:i');
            // dd($startTime);
        } else {
            // dd('2');
            // dd($splRequest);
            $startTime = session('spl_start_time_' . $splRequest->id);
        }

        if ($splRequest->end_time) {
            $endTime = $splRequest->end_time->format('H:i');
        } else {
            $endTime = session('spl_end_time_' . $splRequest->id);
        }

        return view('hr.spl.show', compact('splRequest', 'approvalFlow', 'canApprove', 'canDisapprove', 'isPending', 'currentApprovalLevel', 'startTime', 'endTime'));
    }

    /**
     * Edit SPL (hanya supervisor pembuat, saat pending dan belum ada approval HEAD/MANAGER/HR)
     */
    public function edit($id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::with('employees')->findOrFail($id);
        if ($splRequest->supervisor_id !== $user->id && !$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit SPL ini.');
        }
        $hasAnyApproval = $splRequest->head_approved_at || $splRequest->manager_approved_at || $splRequest->hrd_approved_at;
        if ($splRequest->status === SplRequest::STATUS_REJECTED || $hasAnyApproval) {
            abort(403, 'SPL tidak dapat diedit karena sudah ada proses approval atau ditolak.');
        }
        return view('hr.spl.edit', compact('splRequest'));
    }

    /**
     * Update SPL
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::with('employees')->findOrFail($id);
        if ($splRequest->supervisor_id !== $user->id && !$user->isHR()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit SPL ini.');
        }
        $hasAnyApproval = $splRequest->head_approved_at || $splRequest->manager_approved_at || $splRequest->hrd_approved_at;
        if ($splRequest->status === SplRequest::STATUS_REJECTED || $hasAnyApproval) {
            return back()->with('error', 'SPL tidak dapat diedit karena sudah ada proses approval atau ditolak.');
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'shift' => 'required|string|max:255',
            'mesin' => 'nullable|string|max:255',
            'keperluan' => 'required|string',
            'employees' => 'required|array|min:1',
            'employees.*.employee_id' => 'nullable|exists:users,id',
            'employees.*.nip' => 'nullable|string|max:50',
            'employees.*.employee_name' => 'required|string|max:255',
            'employees.*.is_manual' => 'nullable',
            'employees.*.start_time' => 'required|date_format:H:i',
            'employees.*.end_time' => 'required|date_format:H:i',
        ]);

        DB::connection('pgsql2')->beginTransaction();
        try {
            $splRequest->update([
                'request_date' => $validated['request_date'],
                'shift' => $validated['shift'],
                'mesin' => $validated['mesin'] ?? null,
                'keperluan' => $validated['keperluan'],
            ]);

            // Replace employees (sederhana, aman untuk status pending)
            SplEmployee::where('spl_request_id', $splRequest->id)->delete();
            foreach ($validated['employees'] as $employeeData) {
                $isManual = false;
                if (isset($employeeData['is_manual'])) {
                    $isManualValue = $employeeData['is_manual'];
                    if (is_string($isManualValue)) {
                        $isManual = in_array(strtolower($isManualValue), ['true', '1'], true);
                    } else {
                        $isManual = (bool) $isManualValue;
                    }
                }
                $empStart = \Carbon\Carbon::parse($validated['request_date'] . ' ' . $employeeData['start_time']);
                $empEnd = \Carbon\Carbon::parse($validated['request_date'] . ' ' . $employeeData['end_time']);
                if ($empEnd->lt($empStart)) {
                    $empEnd->addDay();
                }
                SplEmployee::create([
                    'spl_request_id' => $splRequest->id,
                    'employee_id' => !empty($employeeData['employee_id']) ? $employeeData['employee_id'] : null,
                    'nip' => !empty($employeeData['nip']) ? $employeeData['nip'] : null,
                    'employee_name' => trim($employeeData['employee_name']),
                    'is_manual' => $isManual,
                    'is_signed' => false,
                    'start_time' => $empStart,
                    'end_time' => $empEnd,
                ]);
            }
            DB::connection('pgsql2')->commit();
            return redirect()->route('hr.spl.show', $splRequest->id)->with('success', 'SPL berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            \Log::error('SPL Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui SPL: ' . $e->getMessage());
        }
    }

    /**
     * Get pending SPL requests for current user
     */
    public function pending()
    {
        $user = Auth::user();
        $approvalService = new \App\Services\SplApprovalService();
        $pendingRequests = $approvalService::getPendingRequestsForUser($user);

        return view('hr.spl.pending', compact('pendingRequests'));
    }

    /**
     * Print SPL for signature
     */
    public function print(Request $request, $id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::with('employees')->findOrFail($id);

        // Check authorization
        if (!$user->isHR() && $splRequest->supervisor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak SPL ini.');
        }

        // Update status to submitted if still draft
        if ($splRequest->status === SplRequest::STATUS_DRAFT) {
            $splRequest->update([
                'status' => SplRequest::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);
        }

        // Get start_time and end_time from request or session
        $startTime = $request->input('start_time') ?? session('spl_start_time_' . $splRequest->id);
        $endTime = $request->input('end_time') ?? session('spl_end_time_' . $splRequest->id);

        $pdf = Pdf::loadView('hr.spl.print', compact('splRequest', 'startTime', 'endTime'));

        return $pdf->download('SPL-' . $splRequest->spl_number . '.pdf');
    }

    /**
     * Mark SPL as signed (after all employees signed)
     */
    public function markAsSigned($id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::with('employees')->findOrFail($id);

        // Check authorization
        if (!$user->isHR() && $splRequest->supervisor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status SPL ini.');
        }

        // Check if all employees have signed
        if (!$splRequest->allEmployeesSigned()) {
            return back()->with('error', 'Belum semua karyawan menandatangani SPL.');
        }

        $splRequest->update([
            'status' => SplRequest::STATUS_SIGNED,
            'signed_at' => now(),
        ]);

        return back()->with('success', 'SPL telah ditandatangani oleh semua karyawan.');
    }

    /**
     * Process approval untuk SPL (menggunakan approval workflow)
     */
    public function processApproval(Request $request, $id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::with(['supervisor', 'head', 'manager', 'hrd'])->findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Cek apakah user bisa approve request ini
        $approvalService = new \App\Services\SplApprovalService();
        $chain = $approvalService->getApprovalChain($splRequest);

        $canApprove = false;
        $currentRoleKey = null;
        $currentOrder = 0;

        foreach ($chain as $level => $approverData) {
            $users = $approverData['users'] ?? collect();
            $roleKey = $approverData['role_key'] ?? null;
            $approvalOrder = $approverData['approval_order'] ?? 0;

            // Skip SPV karena yang membuat sudah otomatis approve
            if ($roleKey === 'spv_division') {
                continue;
            }

            // Cek apakah user ini ada di list approvers untuk level ini
            $userInLevel = $users->contains('id', $user->id);

            if ($userInLevel) {
                // Cek apakah level ini masih pending
                $isPending = false;
                if ($roleKey === 'head_division') {
                    if ($user->jabatan == 3) {
                        $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                    } else {
                        $isPending = is_null($splRequest->head_approved_at) && is_null($splRequest->head_rejected_at);
                    }
                    // $isPending = is_null($splRequest->head_approved_at) && is_null($splRequest->head_rejected_at);
                } elseif ($roleKey === 'manager') {
                    $isPending = is_null($splRequest->manager_approved_at) && is_null($splRequest->manager_rejected_at);
                } elseif ($roleKey === 'hr') {
                    $isPending = is_null($splRequest->hrd_approved_at) && is_null($splRequest->hrd_rejected_at);
                }

                // Cek apakah level sebelumnya sudah di-approve
                $previousLevelsApproved = true;
                foreach ($chain as $prevLevel => $prevApproverData) {
                    if ($prevLevel === $level) {
                        break;
                    }

                    $prevRoleKey = $prevApproverData['role_key'] ?? null;
                    $prevApproved = false;

                    if ($prevRoleKey === 'spv_division') {
                        $prevApproved = true; // SPV yang membuat sudah approve
                    } elseif ($prevRoleKey === 'head_division') {
                        // head_division bisa di-approve oleh HEAD atau MANAGER
                        // Cek apakah HEAD atau MANAGER sudah approve
                        $prevApproved = !is_null($splRequest->head_approved_at) || !is_null($splRequest->manager_approved_at);
                    } elseif ($prevRoleKey === 'manager') {
                        $prevApproved = !is_null($splRequest->manager_approved_at);
                    } elseif ($prevRoleKey === 'hr') {
                        $prevApproved = !is_null($splRequest->hrd_approved_at);
                    }

                    if (!$prevApproved) {
                        $previousLevelsApproved = false;
                        break;
                    }
                }

                if ($isPending && $previousLevelsApproved) {
                    $canApprove = true;
                    $currentRoleKey = $roleKey;
                    $currentOrder = $approvalOrder;
                    break;
                }
            }
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak dapat menyetujui SPL ini.');
        }

        DB::connection('pgsql2')->beginTransaction();

        try {
            $updateData = [];

            if ($validated['action'] === 'approve') {
                if ($currentRoleKey === 'head_division') {
                    if ($user->jabatan == 3) {
                        // MANAGER yang approve melalui head_division
                        $updateData['manager_id'] = $user->id;
                        $updateData['manager_approved_at'] = now();
                        if (!empty($validated['notes'] ?? null)) {
                            $updateData['manager_notes'] = $validated['notes'];
                        }
                    } else {
                        // HEAD yang approve
                        $updateData['head_id'] = $user->id;
                        $updateData['head_approved_at'] = now();
                        if (!empty($validated['notes'] ?? null)) {
                            $updateData['head_notes'] = $validated['notes'];
                        }
                    }
                } elseif ($currentRoleKey === 'manager') {
                    $updateData['manager_id'] = $user->id;
                    $updateData['manager_approved_at'] = now();
                    if (!empty($validated['notes'] ?? null)) {
                        $updateData['manager_notes'] = $validated['notes'];
                    }
                } elseif ($currentRoleKey === 'hr') {
                    $updateData['hrd_id'] = $user->id;
                    $updateData['hrd_approved_at'] = now();
                    if (!empty($validated['notes'] ?? null)) {
                        $updateData['hrd_notes'] = $validated['notes'];
                    }
                }
            } else {
                // Reject
                // Notes wajib untuk reject
                if (empty($validated['notes'] ?? null)) {
                    DB::connection('pgsql2')->rollBack();
                    return back()->withErrors(['notes' => 'Catatan wajib diisi saat menolak SPL.'])->withInput();
                }

                if ($currentRoleKey === 'head_division') {
                    if ($user->jabatan == 3) {
                        // MANAGER yang reject melalui head_division
                        $updateData['manager_id'] = $user->id;
                        $updateData['manager_rejected_at'] = now();
                        $updateData['manager_notes'] = $validated['notes'];
                    } else {
                        // HEAD yang reject
                        $updateData['head_id'] = $user->id;
                        $updateData['head_rejected_at'] = now();
                        $updateData['head_notes'] = $validated['notes'];
                    }
                } elseif ($currentRoleKey === 'manager') {
                    $updateData['manager_id'] = $user->id;
                    $updateData['manager_rejected_at'] = now();
                    $updateData['manager_notes'] = $validated['notes'];
                } elseif ($currentRoleKey === 'hr') {
                    $updateData['hrd_id'] = $user->id;
                    $updateData['hrd_rejected_at'] = now();
                    $updateData['hrd_notes'] = $validated['notes'];
                }
            }

            $splRequest->update($updateData);

            // Update status
            $approvalService->updateRequestStatus($splRequest);

            DB::connection('pgsql2')->commit();

            $actionText = $validated['action'] === 'approve' ? 'disetujui' : 'ditolak';
            return back()->with('success', "SPL berhasil {$actionText}.");
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollBack();
            \Log::error('SPL Approval Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * HRD approve SPL (legacy method, deprecated - gunakan processApproval)
     * @deprecated Gunakan processApproval untuk workflow approval baru
     */
    public function approve(Request $request, $id)
    {
        return $this->processApproval($request, $id);
    }

    /**
     * HRD reject SPL (legacy method, deprecated - gunakan processApproval)
     * @deprecated Gunakan processApproval untuk workflow approval baru
     */
    public function reject(Request $request, $id)
    {
        return $this->processApproval($request, $id);
    }

    /**
     * Upload signed document (foto SPL yang sudah ditandatangani)
     */
    public function uploadSignedDocument(Request $request, $id)
    {
        $user = Auth::user();
        $splRequest = SplRequest::findOrFail($id);

        // Only supervisor who created the SPL can upload signed document
        if ($splRequest->supervisor_id !== $user->id) {
            abort(403, 'Hanya supervisor yang membuat SPL yang dapat mengupload dokumen.');
        }

        $validated = $request->validate([
            'signed_document' => 'required|image|mimes:jpeg,jpg,png|max:5120', // Max 5MB
        ]);

        try {
            // Delete old document if exists
            if ($splRequest->signed_document_path) {
                $oldPath = storage_path('app/public/' . $splRequest->signed_document_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new document
            $file = $request->file('signed_document');
            $fileName = 'spl_' . $splRequest->spl_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('spl/signed_documents', $fileName, 'public');

            $splRequest->update([
                'signed_document_path' => $path,
            ]);

            return back()->with('success', 'Dokumen SPL yang sudah ditandatangani berhasil diupload.');
        } catch (\Exception $e) {
            \Log::error('Error uploading signed document: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }
}
