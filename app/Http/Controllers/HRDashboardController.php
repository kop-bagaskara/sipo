<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HRDashboardController extends Controller
{
    /**
     * Display HR dashboard - redirect to requests page
     */
    public function index()
    {
        // Redirect to the main requests page which now serves as the dashboard
        return redirect()->route('hr.requests.index');
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($user)
    {
        $query = EmployeeRequest::query();

        if ($user->isHR()) {
            // HR can see all requests
            $query->whereIn('status', [
                EmployeeRequest::STATUS_PENDING,
                EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ($user->hasSupervisor()) {
            // Supervisor can see requests from subordinates
            $query->where('supervisor_id', $user->id);
        } else {
            // Employee can see own requests
            $query->where('employee_id', $user->id);
        }

        $totalRequests = $query->count();
        $pendingRequests = $query->where('status', EmployeeRequest::STATUS_PENDING)->count();
        $approvedRequests = $query->where('status', EmployeeRequest::STATUS_HR_APPROVED)->count();
        $rejectedRequests = $query->whereIn('status', [
            EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
            EmployeeRequest::STATUS_HR_REJECTED
        ])->count();

        // Get requests by type
        $requestsByType = $query->select('request_type', DB::raw('count(*) as total'))
                              ->groupBy('request_type')
                              ->pluck('total', 'request_type')
                              ->toArray();

        // Get requests by status
        $requestsByStatus = $query->select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status')
                                ->toArray();

        return [
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'approved_requests' => $approvedRequests,
            'rejected_requests' => $rejectedRequests,
            'requests_by_type' => $requestsByType,
            'requests_by_status' => $requestsByStatus
        ];
    }

    /**
     * Get recent requests
     */
    private function getRecentRequests($user)
    {
        $query = EmployeeRequest::query();

        if ($user->isHR()) {
            // HR can see all requests
            $query->whereIn('status', [
                EmployeeRequest::STATUS_PENDING,
                EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ($user->hasSupervisor()) {
            $query->where('supervisor_id', $user->id);
        } else {
            $query->where('employee_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')->limit(10)->get();
    }

    /**
     * Get pending approvals
     */
    private function getPendingApprovals($user)
    {
        if ($user->isHR()) {
            return EmployeeRequest::where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED)
                                 ->orderBy('created_at', 'asc')
                                 ->limit(5)
                                 ->get();
        } elseif ($user->hasSupervisor()) {
            return EmployeeRequest::where('supervisor_id', $user->id)
                                 ->where('status', EmployeeRequest::STATUS_PENDING)
                                 ->orderBy('created_at', 'asc')
                                 ->limit(5)
                                 ->get();
        }

        return collect();
    }




    /**
     * Get approval statistics
     */
    public function getApprovalStats()
    {
        $user = Auth::user();

        if (!$user->is_hr && !$user->supervisor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [];

        if ($user->isHR()) {
            $stats = [
                'pending_hr_approval' => EmployeeRequest::where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED)->count(),
                'approved_today' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_APPROVED)
                                                  ->whereDate('hr_approved_at', today())
                                                  ->count(),
                'rejected_today' => EmployeeRequest::where('status', EmployeeRequest::STATUS_HR_REJECTED)
                                                  ->whereDate('hr_rejected_at', today())
                                                  ->count()
            ];
        } elseif ($user->hasSupervisor()) {
            $stats = [
                'pending_supervisor_approval' => EmployeeRequest::where('supervisor_id', $user->id)
                                                               ->where('status', EmployeeRequest::STATUS_PENDING)
                                                               ->count(),
                'approved_today' => EmployeeRequest::where('supervisor_id', $user->id)
                                                  ->where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED)
                                                  ->whereDate('supervisor_approved_at', today())
                                                  ->count(),
                'rejected_today' => EmployeeRequest::where('supervisor_id', $user->id)
                                                  ->where('status', EmployeeRequest::STATUS_SUPERVISOR_REJECTED)
                                                  ->whereDate('supervisor_rejected_at', today())
                                                  ->count()
            ];
        }

        return response()->json($stats);
    }

    /**
     * Get request trends (for charts)
     */
    public function getRequestTrends(Request $request)
    {
        $user = Auth::user();
        $days = $request->get('days', 30);

        $query = EmployeeRequest::query();

        if ($user->isHR()) {
            $query->whereIn('status', [
                EmployeeRequest::STATUS_PENDING,
                EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                EmployeeRequest::STATUS_HR_APPROVED,
                EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } elseif ($user->hasSupervisor()) {
            $query->where('supervisor_id', $user->id);
        } else {
            $query->where('employee_id', $user->id);
        }

        $trends = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "hr_approved" OR status = "supervisor_approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "hr_rejected" OR status = "supervisor_rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trends);
    }
}
