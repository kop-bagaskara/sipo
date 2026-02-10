<?php

namespace App\Http\Controllers;

use App\Models\JobDevelopment;
use App\Models\JobDevelopmentProcess;
use App\Models\JobDevelopmentProcessHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserExecutionController extends Controller
{
    /**
     * Show user's assigned processes
     */
    public function myProcesses()
    {
        // Return the view for the page
        return view('main.process.development.user-execution.my-processes');
    }

    public function myProcessesData()
    {
        // Get processes assigned to the authenticated user for AJAX
        $processes = JobDevelopmentProcess::where('assigned_user_id', auth()->id())
            ->with(['jobDevelopment', 'assignedUser', 'department'])
            ->get();

        // If no assigned processes, get all processes for testing (temporary)
        if ($processes->isEmpty()) {
            $processes = JobDevelopmentProcess::with(['jobDevelopment', 'assignedUser', 'department'])
                ->limit(10)
                ->get();
        }

        // Debug logging
        Log::info('UserExecutionController::myProcessesData', [
            'user_id' => auth()->id(),
            'assigned_processes_count' => JobDevelopmentProcess::where('assigned_user_id', auth()->id())->count(),
            'total_processes_count' => JobDevelopmentProcess::count(),
            'returned_processes_count' => $processes->count()
        ]);

        // Return JSON response with proper data structure
        return response()->json([
            'success' => true,
            'data' => $processes,
            'message' => 'Processes loaded successfully'
        ]);
    }

    public function getProcessData($processId)
    {
        $process = JobDevelopmentProcess::with(['jobDevelopment', 'assignedUser', 'department'])
            ->findOrFail($processId);
            
        $job = $process->jobDevelopment;
        
        return response()->json([
            'success' => true,
            'process' => $process,
            'job' => $job
        ]);
    }

    public function updatePurchasingTracking(Request $request, $processId)
    {
        $process = JobDevelopmentProcess::findOrFail($processId);
        
        // Validate that this is a purchasing process for trial khusus
        if ($process->process_type !== 'purchasing' || $process->jobDevelopment->type !== 'trial_khusus') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid process type for purchasing tracking'
            ]);
        }
        
        // Update tracking data
        $trackingData = [
            'item_name' => $request->item_name,
            'supplier_name' => $request->supplier_name,
            'order_status' => $request->order_status,
            'delivery_status' => $request->delivery_status,
            'expected_delivery' => $request->expected_delivery,
            'actual_delivery' => $request->actual_delivery,
            'tracking_notes' => $request->tracking_notes,
            'next_action' => $request->next_action,
            'updated_at' => now()
        ];
        
        $process->update([
            'tracking_data' => $trackingData
        ]);
        
        // Log history
        JobDevelopmentProcessHistory::create([
            'job_development_id' => $process->job_development_id,
            'process_id' => $process->id,
            'user_id' => auth()->id(),
            'action_type' => 'purchasing_tracking_updated',
            'action_result' => 'success',
            'action_notes' => 'Purchasing tracking updated: ' . $request->next_action,
            'action_data' => $trackingData,
            'action_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Purchasing tracking updated successfully'
        ]);
    }

    public function submitQcVerification(Request $request, $processId)
    {
        try {
            $process = JobDevelopmentProcess::with('jobDevelopment')->findOrFail($processId);
            
            // Validate request
            $request->validate([
                'verification_result' => 'required|in:ok,not_ok',
                'quality_score' => 'required|integer|min:1|max:10',
                'defects_found' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'verification_notes' => 'nullable|string',
                'next_action' => 'nullable|string'
            ]);
            
            // Update process with verification data
            $process->update([
                'verification_data' => [
                    'verification_result' => $request->verification_result,
                    'quality_score' => $request->quality_score,
                    'defects_found' => $request->defects_found,
                    'recommendations' => $request->recommendations,
                    'verification_notes' => $request->verification_notes,
                    'next_action' => $request->next_action,
                    'verified_at' => now()
                ],
                'verification_result' => $request->verification_result
            ]);
            
            // Log history
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $process->id,
                'user_id' => auth()->id(),
                'action_type' => 'qc_verification_submitted',
                'action_result' => $request->verification_result === 'ok' ? 'ok' : 'not_ok',
                'action_notes' => $request->verification_notes,
                'action_data' => $request->all(),
                'action_at' => now()
            ]);
            
            // Handle branching logic based on verification result
            $this->handleBranchingLogic($process, $request->verification_result);
            
            return response()->json([
                'success' => true,
                'message' => 'QC verification berhasil disubmit'
            ]);
            
        } catch (\Exception $e) {
            Log::error('QC Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat submit QC verification'
            ], 500);
        }
    }
    
    // New methods for PPIC modal
    public function submitProductionSchedule(Request $request, $processId)
    {
        try {
            $process = JobDevelopmentProcess::with('jobDevelopment')->findOrFail($processId);
            
            // Validate request
            $request->validate([
                'production_date' => 'required|date',
                'production_shift' => 'required|in:shift_1,shift_2,shift_3',
                'production_line' => 'required|string',
                'estimated_quantity' => 'required|integer|min:1',
                'production_notes' => 'nullable|string'
            ]);
            
            // Update process with production schedule data
            $process->update([
                'tracking_data' => [
                    'production_date' => $request->production_date,
                    'production_shift' => $request->production_shift,
                    'production_line' => $request->production_line,
                    'estimated_quantity' => $request->estimated_quantity,
                    'production_notes' => $request->production_notes,
                    'scheduled_at' => now()
                ]
            ]);
            
            // Log history
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $process->id,
                'user_id' => auth()->id(),
                'action_type' => 'scheduled',
                'action_result' => 'success',
                'action_notes' => 'Production schedule set: ' . $request->production_date,
                'action_data' => $request->all(),
                'action_at' => now()
            ]);
            
            // For proof type, this completes the PPIC process
            if ($process->jobDevelopment->type === 'proof') {
                $process->update(['status' => 'completed']);
                
                // Activate next process (RnD verification)
                $this->activateNextProcess($process->jobDevelopment, 'rnd_verification');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Production schedule berhasil diset'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Production Schedule Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat set production schedule'
            ], 500);
        }
    }
    
    public function submitItemRequest(Request $request, $processId)
    {
        try {
            $process = JobDevelopmentProcess::with('jobDevelopment')->findOrFail($processId);
            
            // Validate request
            $request->validate([
                'item_name' => 'required|string',
                'item_specification' => 'required|string',
                'required_quantity' => 'required|integer|min:1',
                'required_date' => 'required|date',
                'priority_level' => 'required|in:low,medium,high,urgent',
                'budget_estimate' => 'nullable|numeric|min:0',
                'request_reason' => 'required|string',
                'additional_notes' => 'nullable|string'
            ]);
            
            // Update process with item request data
            $process->update([
                'tracking_data' => [
                    'item_name' => $request->item_name,
                    'item_specification' => $request->item_specification,
                    'required_quantity' => $request->required_quantity,
                    'required_date' => $request->required_date,
                    'priority_level' => $request->priority_level,
                    'budget_estimate' => $request->budget_estimate,
                    'request_reason' => $request->request_reason,
                    'additional_notes' => $request->additional_notes,
                    'requested_at' => now()
                ]
            ]);
            
            // Log history
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $process->id,
                'user_id' => auth()->id(),
                'action_type' => 'scheduled',
                'action_result' => 'success',
                'action_notes' => 'Item request sent to purchasing: ' . $request->item_name,
                'action_data' => $request->all(),
                'action_at' => now()
            ]);
            
            // For trial_khusus type, activate purchasing process
            if ($process->jobDevelopment->type === 'trial_khusus') {
                $this->activateNextProcess($process->jobDevelopment, 'purchasing');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Item request berhasil dikirim ke purchasing'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Item Request Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat kirim item request'
            ], 500);
        }
    }
    
    // Helper method to activate next process
    private function activateNextProcess($job, $processType)
    {
        $nextProcess = $job->processes()
            ->where('process_type', $processType)
            ->where('status', 'pending')
            ->first();
            
        if ($nextProcess) {
            $nextProcess->update(['status' => 'pending']);
        }
    }

    private function handoverToPpic($process)
    {
        dd($process);
        // Find PPIC process for this job
        $ppicProcess = JobDevelopmentProcess::where('job_development_id', $process->job_development_id)
            ->where('process_type', 'ppic')
            ->first();
            
        if ($ppicProcess) {
            $ppicProcess->update(['status' => 'pending']);
            
            // Log handover
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $ppicProcess->id,
                'user_id' => auth()->id(),
                'action_type' => 'handover_to_ppic',
                'action_result' => 'success',
                'action_notes' => 'Item handed over to PPIC after QC verification OK',
                'action_at' => now()
            ]);
        }
    }

    private function returnToPurchasing($process)
    {
        // Find purchasing process for this job
        $purchasingProcess = JobDevelopmentProcess::where('job_development_id', $process->job_development_id)
            ->where('process_type', 'purchasing')
            ->first();
            
        if ($purchasingProcess) {
            $purchasingProcess->update(['status' => 'pending']);
            
            // Log return
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $purchasingProcess->id,
                'user_id' => auth()->id(),
                'action_type' => 'return_to_purchasing',
                'action_result' => 'success',
                'action_notes' => 'Item returned to purchasing after QC verification NOT OK',
                'action_at' => now()
            ]);
        }
    }

    private function escalateToRnd($process)
    {
        // Find RnD verification process for this job
        $rndProcess = JobDevelopmentProcess::where('job_development_id', $process->job_development_id)
            ->where('process_type', 'rnd_verification')
            ->first();
            
        if ($rndProcess) {
            $rndProcess->update(['status' => 'pending']);
            
            // Log escalation
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $rndProcess->id,
                'user_id' => auth()->id(),
                'action_type' => 'escalate_to_rnd',
                'action_result' => 'success',
                'action_notes' => 'Item escalated to RnD after QC verification conditional',
                'action_at' => now()
            ]);
        }
    }

    /**
     * Show process execution form
     */
    public function executeProcess($processId)
    {
        $process = JobDevelopmentProcess::with(['jobDevelopment', 'department'])
            ->where('id', $processId)
            ->where('assigned_user_id', Auth::id())
            ->firstOrFail();

        if ($process->status === 'completed') {
            return redirect()->route('user-execution.my-processes')
                ->with('error', 'Process ini sudah selesai');
        }

        return view('main.process.development.user-execution.execute-process', compact('process'));
    }

    /**
     * Start process execution
     */
    public function startProcess(Request $request, $processId)
    {
        try {
            $process = JobDevelopmentProcess::where('id', $processId)
                ->where('assigned_user_id', Auth::id())
                ->firstOrFail();

            if ($process->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Process tidak dapat dimulai'
                ], 400);
            }

            // Update process status
            $process->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);

            // Log history
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $process->id,
                'user_id' => Auth::id(),
                'action_type' => 'started',
                'action_result' => 'success',
                'action_notes' => 'Process dimulai',
                'action_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Process berhasil dimulai'
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulai process'
            ], 500);
        }
    }

    /**
     * Complete process with branching logic
     */
    public function completeProcess(Request $request, $processId)
    {
        try {
            $process = JobDevelopmentProcess::where('id', $processId)
                ->where('assigned_user_id', Auth::id())
                ->firstOrFail();

            if ($process->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Process tidak dapat diselesaikan'
                ], 400);
            }

            $request->validate([
                'completion_notes' => 'required|string',
                'verification_result' => 'required|in:ok,not_ok',
                'additional_data' => 'nullable|array'
            ]);

            // Update process status
            $process->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->completion_notes,
                'verification_result' => $request->verification_result
            ]);

            // Log history
            JobDevelopmentProcessHistory::create([
                'job_development_id' => $process->job_development_id,
                'process_id' => $process->id,
                'user_id' => Auth::id(),
                'action_type' => $this->getActionType($process->process_type),
                'action_result' => $request->verification_result,
                'action_notes' => $request->completion_notes,
                'action_data' => $request->additional_data,
                'action_at' => now()
            ]);

            // Handle branching logic
            $this->handleBranchingLogic($process, $request->verification_result);

            return response()->json([
                'success' => true,
                'message' => 'Process berhasil diselesaikan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan process'
            ], 500);
        }
    }

    /**
     * Get action type based on process type
     */
    private function getActionType($processType)
    {
        switch ($processType) {
            case 'ppic':
                return 'scheduled';
            case 'purchasing':
                return 'purchased';
            case 'qc':
                return 'qc_checked';
            case 'rnd_verification':
                return 'rnd_verified';
            default:
                return 'completed';
        }
    }

    /**
     * Handle branching logic based on process completion
     */
    private function handleBranchingLogic($process, $verificationResult)
    {
        $job = $process->jobDevelopment;
        $branchConditions = $process->branch_conditions;

        if ($job->type === 'proof') {
            // Proof (Normal) flow
            if ($process->process_type === 'ppic' && $verificationResult === 'ok') {
                // PPIC completed, move to RnD verification
                $this->activateNextProcess($job, 'rnd_verification');
            } elseif ($process->process_type === 'rnd_verification' && $verificationResult === 'ok') {
                // RnD verification completed, job is done
                $job->update(['status' => 'completed']);
            }
        } else {
            // Trial Item Khusus flow
            if ($process->process_type === 'purchasing' && $verificationResult === 'ok') {
                // Purchasing completed, move to QC
                $this->activateNextProcess($job, 'qc');
            } elseif ($process->process_type === 'qc') {
                if ($verificationResult === 'ok') {
                    // QC OK, move to RnD verification
                    $this->activateNextProcess($job, 'rnd_verification');
                } else {
                    // QC NOT OK, return to RnD for reprocessing
                    $this->returnToRnD($job, $process);
                }
            } elseif ($process->process_type === 'rnd_verification') {
                if ($verificationResult === 'ok') {
                    // RnD verification completed, job is done
                    $job->update(['status' => 'completed']);
                } else {
                    // RnD verification failed, return to RnD for reprocessing
                    $this->returnToRnD($job, $process);
                }
            }
        }
    }

    /**
     * Return job to RnD for reprocessing
     */
    private function returnToRnD($job, $failedProcess)
    {
        // Reset all processes to pending
        $job->processes()->update(['status' => 'pending']);
        
        // Log the return to RnD
        JobDevelopmentProcessHistory::create([
            'job_development_id' => $job->id,
            'process_id' => $failedProcess->id,
            'user_id' => Auth::id(),
            'action_type' => 'returned_to_rnd',
            'action_result' => 'pending',
            'action_notes' => 'Job dikembalikan ke RnD untuk reprocessing',
            'action_at' => now()
        ]);
    }
    
    /**
     * Get process history
     */
    public function getProcessHistory($processId)
    {
        try {
            $process = JobDevelopmentProcess::findOrFail($processId);
            
            $history = JobDevelopmentProcessHistory::with('user')
                ->where('process_id', $processId)
                ->orderBy('action_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'history' => $history
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get Process History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat load history'
            ], 500);
        }
    }
}
