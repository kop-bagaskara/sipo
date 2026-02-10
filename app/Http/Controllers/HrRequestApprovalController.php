<?php

namespace App\Http\Controllers;

use App\Models\HrRequest;
use App\Models\RequestApproval;
use App\Models\DivisiApprovalSetting;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrRequestApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Display pending approvals for the logged-in user
     */
    public function index()
    {
        $user = Auth::user();
        $pendingApprovals = $this->approvalService->getPendingApprovalsForUser($user->id);

        return view('hr.approvals.pending', compact('pendingApprovals'));
    }

    /**
     * Show approval detail modal
     */
    public function show($id)
    {
        $user = Auth::user();
        $approval = RequestApproval::with(['request', 'request.user', 'approver'])
            ->findOrFail($id);

        // Check if user is authorized to view this approval
        if ($approval->approver_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat approval ini.');
        }

        // Get all approvals for this request
        $allApprovals = $this->approvalService->getRequestApprovals($approval->request);

        return view('hr.approvals.show', compact('approval', 'allApprovals'));
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $approval = RequestApproval::findOrFail($id);

        // Check if user is authorized
        if ($approval->approver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menyetujui pengajuan ini.'
            ], 403);
        }

        // Check if already processed
        if ($approval->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan sudah diproses.'
            ], 400);
        }

        try {
            $this->approvalService->processApproval(
                $approval->request,
                $approval->level,
                'approved',
                $user->id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $approval = RequestApproval::findOrFail($id);

        // Check if user is authorized
        if ($approval->approver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menolak pengajuan ini.'
            ], 403);
        }

        // Check if already processed
        if ($approval->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan sudah diproses.'
            ], 400);
        }

        // Validate notes is required for rejection
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        try {
            $this->approvalService->processApproval(
                $approval->request,
                $approval->level,
                'rejected',
                $user->id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil ditolak.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval history for a request
     */
    public function history($requestId)
    {
        $request = HrRequest::findOrFail($requestId);
        $approvals = $this->approvalService->getRequestApprovals($request);

        return response()->json([
            'success' => true,
            'data' => $approvals
        ]);
    }

    /**
     * Get approval statistics for logged-in user
     */
    public function stats()
    {
        $user = Auth::user();

        $pendingCount = RequestApproval::where('approver_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedThisMonth = RequestApproval::where('approver_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $rejectedThisMonth = RequestApproval::where('approver_id', $user->id)
            ->where('status', 'rejected')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'pending' => $pendingCount,
                'approved_this_month' => $approvedThisMonth,
                'rejected_this_month' => $rejectedThisMonth,
            ]
        ]);
    }

    /**
     * Display approval settings for a division
     */
    public function settings($divisiId)
    {
        $setting = DivisiApprovalSetting::with(['divisi', 'spv', 'head', 'manager'])
            ->where('divisi_id', $divisiId)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting approval tidak ditemukan untuk divisi ini.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'divisi' => $setting->divisi,
                'spv' => $setting->spv,
                'head' => $setting->head,
                'manager' => $setting->manager,
                'chain' => $setting->chain,
                'levels' => $setting->approval_levels,
            ]
        ]);
    }
}
