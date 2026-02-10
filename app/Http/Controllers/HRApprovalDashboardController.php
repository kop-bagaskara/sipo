<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRequest;
use App\Models\OvertimeEntry;
use App\Models\VehicleAssetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRApprovalDashboardController extends Controller
{
    /**
     * Display the approval dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get pending requests for different modules
        $pendingEmployeeRequests = collect();
        $pendingOvertimeEntries = collect();
        $pendingVehicleRequests = collect();
        $pendingAssetRequests = collect();
        
        if ($user->canApprove()) {
            // Manager/Head can see their division's requests
            $pendingEmployeeRequests = EmployeeRequest::where('supervisor_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingOvertimeEntries = OvertimeEntry::forDivisi($user->divisi)
                ->pendingSpv()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingVehicleRequests = VehicleAssetRequest::forDivisi($user->divisi)
                ->where('request_type', 'vehicle')
                ->pendingManager()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingAssetRequests = VehicleAssetRequest::forDivisi($user->divisi)
                ->where('request_type', 'asset')
                ->pendingManager()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        if ($user->isHR()) {
            // HR can see all pending HR approvals
            $pendingEmployeeRequests = EmployeeRequest::where('status', 'supervisor_approved')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingOvertimeEntries = OvertimeEntry::pendingHrga()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingVehicleRequests = VehicleAssetRequest::where('request_type', 'vehicle')
                ->pendingHrga()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $pendingAssetRequests = VehicleAssetRequest::where('request_type', 'asset')
                ->pendingHrga()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        // Get statistics
        $stats = [
            'employee_requests' => [
                'pending' => $user->canApprove() 
                    ? EmployeeRequest::where('supervisor_id', $user->id)->where('status', 'pending')->count()
                    : EmployeeRequest::where('status', 'supervisor_approved')->count(),
                'total' => $user->canApprove()
                    ? EmployeeRequest::where('supervisor_id', $user->id)->count()
                    : EmployeeRequest::count()
            ],
            'overtime_entries' => [
                'pending' => $user->canApprove()
                    ? OvertimeEntry::forDivisi($user->divisi)->pendingSpv()->count()
                    : OvertimeEntry::pendingHrga()->count(),
                'total' => $user->canApprove()
                    ? OvertimeEntry::forDivisi($user->divisi)->count()
                    : OvertimeEntry::count()
            ],
            'vehicle_requests' => [
                'pending' => $user->canApprove()
                    ? VehicleAssetRequest::forDivisi($user->divisi)->where('request_type', 'vehicle')->pendingManager()->count()
                    : VehicleAssetRequest::where('request_type', 'vehicle')->pendingHrga()->count(),
                'total' => $user->canApprove()
                    ? VehicleAssetRequest::forDivisi($user->divisi)->where('request_type', 'vehicle')->count()
                    : VehicleAssetRequest::where('request_type', 'vehicle')->count()
            ],
            'asset_requests' => [
                'pending' => $user->canApprove()
                    ? VehicleAssetRequest::forDivisi($user->divisi)->where('request_type', 'asset')->pendingManager()->count()
                    : VehicleAssetRequest::where('request_type', 'asset')->pendingHrga()->count(),
                'total' => $user->canApprove()
                    ? VehicleAssetRequest::forDivisi($user->divisi)->where('request_type', 'asset')->count()
                    : VehicleAssetRequest::where('request_type', 'asset')->count()
            ]
        ];
        
        return view('hr.approval.dashboard', compact(
            'pendingEmployeeRequests',
            'pendingOvertimeEntries', 
            'pendingVehicleRequests',
            'pendingAssetRequests',
            'stats'
        ));
    }
}
