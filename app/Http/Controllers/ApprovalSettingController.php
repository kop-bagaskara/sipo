<?php

namespace App\Http\Controllers;

use App\Models\ApprovalSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApprovalSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ApprovalSetting::with('user');

        // Filter by request type if provided
        if ($request->has('request_type') && $request->request_type) {
            $query->where('request_type', $request->request_type);
        }

        $approvalSettings = $query->orderBy('request_type')
            ->orderBy('approval_order')
            ->get();

        $users = User::with('jabatanUser')->orderBy('name')->get();

        return view('hr.approval-settings.index', compact('approvalSettings', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_type' => 'required|string|max:50',
            'approval_level' => 'required|string|max:50',
            'approval_order' => 'required|integer|min:1',
            'approver_type' => 'required|in:user,role',
            'role_key' => 'required_if:approver_type,role|nullable|string|max:50',
            'allowed_jabatan' => 'nullable|array',
            'allowed_jabatan.*' => 'integer',
            'user_id' => 'required_if:approver_type,user|nullable|exists:users,id',
            'user_name' => 'nullable|string|max:255',
            'user_position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate approval order for same request type
        $existingOrder = ApprovalSetting::where('request_type', $request->request_type)
            ->where('approval_order', $request->approval_order)
            ->where('is_active', true)
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Urutan approval sudah ada untuk jenis pengajuan ini',
                'errors' => ['approval_order' => ['Urutan approval sudah ada']]
            ], 422);
        }

        // Normalize user_name/user_position when approver_type=user
        $payload = $request->only([
            'request_type',
            'approval_level',
            'approval_order',
            'approver_type',
            'role_key',
            'allowed_jabatan',
            'user_id',
            'user_name',
            'user_position',
            'is_active',
            'description'
        ]);

        if ($request->approver_type === 'user' && $request->user_id) {
            $user = User::find($request->user_id);
            $payload['user_name'] = $user ? $user->name : null;
            $payload['user_position'] = $user ? ($user->position ?? ($request->user_position ?? null)) : ($request->user_position ?? null);
            $payload['allowed_jabatan'] = null; // User-based approver doesn't need allowed_jabatan
        } else {
            // role-based approver: clear user fields and set allowed_jabatan
            $payload['user_id'] = null;
            $payload['user_name'] = null;
            $payload['user_position'] = null;
            // Store allowed_jabatan as array (will be cast to JSON by model)
            $payload['allowed_jabatan'] = $request->input('allowed_jabatan') ?? [];
        }

        $approvalSetting = ApprovalSetting::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Setting approval berhasil ditambahkan',
            'data' => $approvalSetting
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $approvalSetting = ApprovalSetting::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $approvalSetting
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $approvalSetting = ApprovalSetting::with('user')->findOrFail($id);
        $users = User::with('jabatanUser')->orderBy('name')->get();

        return view('hr.approval-settings.edit', compact('approvalSetting', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('Update ApprovalSetting - Request all:', $request->all());
        Log::info('Update ApprovalSetting - Request input:', $request->input());
        Log::info('Update ApprovalSetting - Request POST:', $request->post());

        $approvalSetting = ApprovalSetting::findOrFail($id);

        // dd($approvalSetting);

        $validator = Validator::make($request->all(), [
            'request_type' => 'required|string|max:50',
            'approval_level' => 'required|string|max:50',
            'approval_order' => 'required|integer|min:1',
            'approver_type' => 'required|in:user,role',
            'role_key' => 'required_if:approver_type,role|nullable|string|max:50',
            'allowed_jabatan' => 'nullable|array',
            'allowed_jabatan.*' => 'integer',
            'user_id' => 'required_if:approver_type,user|nullable|exists:users,id',
            'user_name' => 'nullable|string|max:255',
            'user_position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate approval order for same request type (excluding current record)
        $existingOrder = ApprovalSetting::where('request_type', $request->request_type)
            ->where('approval_order', $request->approval_order)
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Urutan approval sudah ada untuk jenis pengajuan ini',
                'errors' => ['approval_order' => ['Urutan approval sudah ada']]
            ], 422);
        }

        $payload = $request->only([
            'request_type',
            'approval_level',
            'approval_order',
            'approver_type',
            'role_key',
            'allowed_jabatan',
            'user_id',
            'user_name',
            'user_position',
            'is_active',
            'description'
        ]);

        if ($request->approver_type === 'user' && $request->user_id) {
            $user = User::find($request->user_id);
            $payload['user_name'] = $user ? $user->name : null;
            $payload['user_position'] = $user ? ($user->position ?? ($request->user_position ?? null)) : ($request->user_position ?? null);
            $payload['allowed_jabatan'] = null; // User-based approver doesn't need allowed_jabatan
        } else {
            $payload['user_id'] = null;
            $payload['user_name'] = null;
            $payload['user_position'] = null;
            // Store allowed_jabatan as array (will be cast to JSON by model)
            $payload['allowed_jabatan'] = $request->input('allowed_jabatan') ?? [];
        }

        // dd($payload);

        $approvalSetting->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Setting approval berhasil diperbarui',
            'data' => $approvalSetting
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $approvalSetting = ApprovalSetting::findOrFail($id);
        $approvalSetting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting approval berhasil dihapus'
        ]);
    }

    /**
     * Get approval flow for specific request type
     */
    public function getApprovalFlow($requestType)
    {
        $approvalFlow = ApprovalSetting::getApprovalFlow($requestType);

        return response()->json([
            'success' => true,
            'data' => $approvalFlow
        ]);
    }

    /**
     * Get next approver for specific request type and current approval order
     */
    public function getNextApprover(Request $request)
    {
        $requestType = $request->input('request_type');
        $currentApprovalOrder = $request->input('current_approval_order', 0);

        $nextApprover = ApprovalSetting::getNextApprover($requestType, $currentApprovalOrder);

        return response()->json([
            'success' => true,
            'data' => $nextApprover
        ]);
    }

    /**
     * Return eligible approvers for a given setting context.
     * Inputs: approver_type, role_key, user_id (optional), division_id (optional)
     */
    public function approvers(Request $request)
    {
        $approverType = $request->input('approver_type');
        $roleKey = $request->input('role_key');
        $userId = $request->input('user_id');
        $divisionId = $request->input('division_id');

        $users = collect();

        if ($approverType === 'user' && $userId) {
            $users = User::with('jabatanUser')->where('id', $userId)->get();
        } elseif ($approverType === 'role') {
            if ($roleKey === 'hr') {
                $users = User::with('jabatanUser')->where(function ($q) {
                    $q->where('divisi', 7);
                })->orderBy('name')->get();
            } elseif ($roleKey === 'head_division') {
                $query = User::with('jabatanUser')->where(function ($q) {
                    // Head/Manager/SPV per aturan canApprove
                    $q->whereIn('jabatan', [3, 4, 5]);
                });
                if ($divisionId) {
                    $query->where('divisi', (int) $divisionId);
                }
                $users = $query->orderBy('name')->get();
            } elseif ($roleKey === 'spv_division') {
                $query = User::with('jabatanUser')->where('jabatan', 5);
                if ($divisionId) {
                    $query->where('divisi', (int) $divisionId);
                }
                $users = $query->orderBy('name')->get();
            }
        }

        $data = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'divisi' => optional($u->divisiUser)->divisi,
                'jabatan' => optional($u->jabatanUser)->jabatan,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
}
