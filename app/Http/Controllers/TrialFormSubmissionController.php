<?php

namespace App\Http\Controllers;

use App\Models\TrialProcessStep;
use App\Models\TrialFormSubmission;
use App\Models\TrialWorkflowHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrialFormSubmissionController extends Controller
{
    /**
     * Show form for user to fill
     */
    public function showForm(TrialProcessStep $processStep)
    {
        if ($processStep->assigned_user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke form ini');
        }

        $formSubmission = $processStep->formSubmissions()
            ->where('user_id', Auth::id())
            ->first();

        if (!$formSubmission) {
            $formSubmission = TrialFormSubmission::create([
                'trial_process_step_id' => $processStep->id,
                'user_id' => Auth::id(),
                'form_data' => [],
                'status' => 'draft'
            ]);
        }

        return view('main.process.samplequality.form', compact('processStep', 'formSubmission'));
    }

    /**
     * Store form data
     */
    public function store(Request $request, TrialProcessStep $processStep)
    {
        if ($processStep->assigned_user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke form ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'form_data' => 'required|array',
            'notes' => 'nullable|string',
            'conclusion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $formSubmission = $processStep->formSubmissions()
                ->where('user_id', Auth::id())
                ->first();

            if (!$formSubmission) {
                $formSubmission = TrialFormSubmission::create([
                    'trial_process_step_id' => $processStep->id,
                    'user_id' => Auth::id(),
                    'form_data' => $request->form_data,
                    'notes' => $request->notes,
                    'conclusion' => $request->conclusion,
                    'status' => 'draft'
                ]);
            } else {
                $formSubmission->update([
                    'form_data' => $request->form_data,
                    'notes' => $request->notes,
                    'conclusion' => $request->conclusion
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form data berhasil disimpan',
                'data' => $formSubmission
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
     * Submit form
     */
    public function submit(Request $request, TrialProcessStep $processStep)
    {
        if ($processStep->assigned_user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke form ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'form_data' => 'required|array',
            'notes' => 'nullable|string',
            'conclusion' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $formSubmission = $processStep->formSubmissions()
                ->where('user_id', Auth::id())
                ->first();

            if (!$formSubmission) {
                $formSubmission = TrialFormSubmission::create([
                    'trial_process_step_id' => $processStep->id,
                    'user_id' => Auth::id(),
                    'form_data' => $request->form_data,
                    'notes' => $request->notes,
                    'conclusion' => $request->conclusion,
                    'status' => 'submitted'
                ]);
            } else {
                $formSubmission->update([
                    'form_data' => $request->form_data,
                    'notes' => $request->notes,
                    'conclusion' => $request->conclusion,
                    'status' => 'submitted'
                ]);
            }

            // Update process step status
            $processStep->update(['status' => 'completed']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form berhasil di-submit'
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
     * Verify form submission
     */
    public function verify(Request $request, TrialFormSubmission $formSubmission)
    {
        if (!Auth::user()->hasRole('quality_assurance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk verify form'
            ], 403);
        }

        if (!$formSubmission->canBeVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Status form tidak valid untuk diverifikasi'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $formSubmission->verify(Auth::id());
            
            // Update process step status
            $formSubmission->processStep->verifyProcess();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form berhasil diverifikasi'
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
     * Get form data for display
     */
    public function show(TrialFormSubmission $formSubmission)
    {
        // Check access - user can see their own submission or QA can see all
        if ($formSubmission->user_id !== Auth::id() && !Auth::user()->hasRole('quality_assurance')) {
            abort(403, 'Anda tidak memiliki akses ke form ini');
        }

        return view('main.process.samplequality.form-view', compact('formSubmission'));
    }
}
