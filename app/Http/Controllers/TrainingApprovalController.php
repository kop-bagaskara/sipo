<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingApprovalController extends Controller
{
    /**
     * Display pending approvals
     */
    public function index(Request $request)
    {
        $query = TrainingParticipant::with(['training.creator', 'employee'])
            ->where('registration_status', 'registered');

        // Filter by training
        if ($request->has('training_id') && $request->training_id !== '') {
            $query->where('training_id', $request->training_id);
        }

        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id !== '') {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id !== '') {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('divisi', $request->department_id);
            });
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('training', function($subQ) use ($search) {
                    $subQ->where('training_name', 'like', "%{$search}%")
                         ->orWhere('training_code', 'like', "%{$search}%");
                })->orWhereHas('employee', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $trainings = TrainingMaster::published()->active()->get();
        $employees = User::all();
        $departments = \App\Models\Divisi::all();

        return view('hr.training.approval.index', compact('participants', 'trainings', 'employees', 'departments'));
    }

    /**
     * Show approval details
     */
    public function show($id)
    {
        $participant = TrainingParticipant::with([
            'training.creator',
            'employee.divisiUser',
            'employee.jabatanUser',
            'employee.levelUser'
        ])->findOrFail($id);

        // Get training statistics
        $trainingStats = [
            'total_participants' => $participant->training->participants()->count(),
            'approved_participants' => $participant->training->approvedParticipants()->count(),
            'completed_participants' => $participant->training->completedParticipants()->count(),
            'max_participants' => $participant->training->max_participants
        ];

        return view('hr.training.approval.show', compact('participant', 'trainingStats'));
    }

    /**
     * Approve registration
     */
    public function approve(Request $request, $id)
    {
        $participant = TrainingParticipant::findOrFail($id);

        // Check if can be approved
        if ($participant->registration_status !== 'registered') {
            return redirect()->back()
                ->with('error', 'Pendaftaran tidak dapat disetujui.');
        }

        // Check if training is full
        if ($participant->training->isFull()) {
            return redirect()->back()
                ->with('error', 'Training sudah penuh.');
        }

        $validator = Validator::make($request->all(), [
            'approval_notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $participant->update([
                'registration_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'notes' => $request->approval_notes
            ]);

            DB::commit();

            return redirect()->route('training.approval.index')
                ->with('success', 'Pendaftaran berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject registration
     */
    public function reject(Request $request, $id)
    {
        $participant = TrainingParticipant::findOrFail($id);

        // Check if can be rejected
        if ($participant->registration_status !== 'registered') {
            return redirect()->back()
                ->with('error', 'Pendaftaran tidak dapat ditolak.');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $participant->update([
                'registration_status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
                'rejection_reason' => $request->rejection_reason
            ]);

            DB::commit();

            return redirect()->route('training.approval.index')
                ->with('success', 'Pendaftaran berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:tb_training_participants,id',
            'approval_notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $participantIds = $request->participant_ids;
        $approvedCount = 0;
        $rejectedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($participantIds as $participantId) {
                $participant = TrainingParticipant::find($participantId);

                if ($participant && $participant->registration_status === 'registered') {
                    // Check if training is full
                    if (!$participant->training->isFull()) {
                        $participant->update([
                            'registration_status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => Auth::id(),
                            'notes' => $request->approval_notes
                        ]);
                        $approvedCount++;
                    } else {
                        $participant->update([
                            'registration_status' => 'rejected',
                            'rejected_at' => now(),
                            'rejected_by' => Auth::id(),
                            'rejection_reason' => 'Training sudah penuh'
                        ]);
                        $rejectedCount++;
                    }
                }
            }

            DB::commit();

            $message = "Berhasil memproses {$approvedCount} pendaftaran.";
            if ($rejectedCount > 0) {
                $message .= " {$rejectedCount} pendaftaran ditolak karena training penuh.";
            }

            return redirect()->route('training.approval.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get approval statistics
     */
    public function statistics()
    {
        $stats = [
            'pending_approvals' => TrainingParticipant::where('registration_status', 'registered')->count(),
            'approved_today' => TrainingParticipant::where('registration_status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'rejected_today' => TrainingParticipant::where('registration_status', 'rejected')
                ->whereDate('rejected_at', today())->count(),
            'total_approved' => TrainingParticipant::where('registration_status', 'approved')->count(),
            'total_rejected' => TrainingParticipant::where('registration_status', 'rejected')->count()
        ];

        // Get recent approvals
        $recentApprovals = TrainingParticipant::with(['training', 'employee'])
            ->whereIn('registration_status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get approval trends (last 30 days)
        $approvalTrends = TrainingParticipant::selectRaw('DATE(approved_at) as date, COUNT(*) as count')
            ->where('registration_status', 'approved')
            ->where('approved_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('hr.training.approval.statistics', compact('stats', 'recentApprovals', 'approvalTrends'));
    }
}
