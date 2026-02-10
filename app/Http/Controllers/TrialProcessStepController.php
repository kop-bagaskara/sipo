<?php

namespace App\Http\Controllers;

use App\Models\TrialSample;
use App\Models\TrialProcessStep;
use App\Models\TrialWorkflowHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrialProcessStepController extends Controller
{
    /**
     * Store process steps for a trial sample
     */
    public function store(Request $request, TrialSample $trialSample)
    {
        if (!Auth::user()->hasRole('quality_assurance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membuat process steps'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'process_steps' => 'required|array|min:1',
            'process_steps.*.proses' => 'required|string',
            'process_steps.*.department_terkait' => 'required|string',
            'process_steps.*.rencana_trial' => 'required|date',
            'process_steps.*.mesin' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Hapus process steps yang sudah ada
            $trialSample->processSteps()->delete();

            // Buat process steps baru
            foreach ($request->process_steps as $index => $stepData) {
                TrialProcessStep::create([
                    'trial_sample_id' => $trialSample->id,
                    'urutan' => $index + 1,
                    'proses' => $stepData['proses'],
                    'department_terkait' => $stepData['department_terkait'],
                    'rencana_trial' => $stepData['rencana_trial'],
                    'mesin' => $stepData['mesin'],
                    'status' => 'pending'
                ]);
            }

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'step_assigned',
                'Process steps dibuat untuk trial'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Process steps berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign user to a process step
     */
    public function assignUser(Request $request, TrialProcessStep $processStep)
    {
        if (!Auth::user()->hasRole('quality_assurance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk assign user'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $processStep->assignUser($request->user_id);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $processStep->trial_sample_id,
                Auth::id(),
                'step_assigned',
                "User di-assign ke step: {$processStep->proses}",
                ['step_id' => $processStep->id, 'assigned_user_id' => $request->user_id]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil di-assign ke process step'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start process step
     */
    public function startProcess(TrialProcessStep $processStep)
    {
        if ($processStep->assigned_user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk memulai process step ini'
            ], 403);
        }

        if ($processStep->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Status process step tidak valid untuk dimulai'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $processStep->startProcess();

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $processStep->trial_sample_id,
                Auth::id(),
                'step_started',
                "Process step dimulai: {$processStep->proses}",
                ['step_id' => $processStep->id]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Process step berhasil dimulai'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete process step
     */
    public function completeProcess(Request $request, TrialProcessStep $processStep)
    {
        if ($processStep->assigned_user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menyelesaikan process step ini'
            ], 403);
        }

        if (!$processStep->canBeCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Status process step tidak valid untuk diselesaikan'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $processStep->completeProcess();

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $processStep->trial_sample_id,
                Auth::id(),
                'step_completed',
                "Process step selesai: {$processStep->proses}",
                ['step_id' => $processStep->id]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Process step berhasil diselesaikan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify process step
     */
    public function verifyProcess(Request $request, TrialProcessStep $processStep)
    {
        if (!Auth::user()->hasRole('quality_assurance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk verify process step'
            ], 403);
        }

        if (!$processStep->canBeVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Status process step tidak valid untuk diverifikasi'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $processStep->verifyProcess();

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $processStep->trial_sample_id,
                Auth::id(),
                'step_verified',
                "Process step diverifikasi: {$processStep->proses}",
                ['step_id' => $processStep->id]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Process step berhasil diverifikasi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
