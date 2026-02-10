<?php

namespace App\Http\Controllers;

use App\Models\TrialSample;
use App\Models\TrialProcessStep;
use App\Models\TrialWorkflowHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrialSampleController extends Controller
{
    /**
     * Display a listing of trial samples
     */
    public function index()
    {
        $user = Auth::user();

        // Filter berdasarkan role user
        $query = TrialSample::with(['creator', 'purchasingUser', 'qaUser', 'processSteps']);

        if ($user->hasRole('purchasing')) {
            $query->whereIn('status', ['submitted', 'purchasing_review']);
        } elseif ($user->hasRole('quality_assurance')) {
            $query->whereIn('status', ['purchasing_approved', 'qa_processing', 'qa_completed']);
        } else {
            // User biasa - lihat yang dia buat atau yang dia ditugaskan
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('processSteps', function($subQ) use ($user) {
                      $subQ->where('assigned_user_id', $user->id);
                  });
            });
        }

        $trialSamples = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('main.process.samplequality.index', compact('trialSamples'));
    }

    /**
     * Show the form for creating a new trial sample
     */
    public function create()
    {
        return view('main.process.samplequality.input-pengajuan');
    }

    /**
     * Store a newly created trial sample
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tujuan_trial' => 'required|in:trial_material,trial_produk,trial_proses',
            'material_bahan' => 'required|string',
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string',
            'kode_supplier' => 'required|string',
            'nama_supplier' => 'required|string',
            'jumlah_bahan' => 'required|numeric|min:0',
            'satuan' => 'required|string',
            'tanggal_terima' => 'required|date',
            'deskripsi' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $trialSample = TrialSample::create([
                'nomor_pengajuan' => TrialSample::generateNomorPengajuan(),
                'tujuan_trial' => $request->tujuan_trial,
                'material_bahan' => $request->material_bahan,
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'kode_supplier' => $request->kode_supplier,
                'nama_supplier' => $request->nama_supplier,
                'jumlah_bahan' => $request->jumlah_bahan,
                'satuan' => $request->satuan,
                'tanggal_terima' => $request->tanggal_terima,
                'deskripsi' => $request->deskripsi,
                'status' => 'draft',
                'created_by' => Auth::id()
            ]);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'created',
                'Pengajuan trial bahan baku dibuat'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan trial berhasil dibuat',
                'data' => $trialSample
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
     * Display the specified trial sample
     */
    public function show(TrialSample $trialSample)
    {
        $trialSample->load(['creator', 'purchasingUser', 'qaUser', 'processSteps.assignedUser', 'workflowHistory.user']);

        return view('main.process.samplequality.show', compact('trialSample'));
    }

    /**
     * Submit trial sample to purchasing
     */
    public function submitToPurchasing(TrialSample $trialSample)
    {
        if ($trialSample->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk submit pengajuan ini'
            ], 403);
        }

        if ($trialSample->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan tidak valid untuk di-submit'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $trialSample->update(['status' => 'submitted']);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'submitted',
                'Pengajuan disubmit ke purchasing'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disubmit ke purchasing'
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
     * Purchasing approve trial sample
     */
    public function purchasingApprove(Request $request, TrialSample $trialSample)
    {
        if (!Auth::user()->hasRole('purchasing')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk approve pengajuan'
            ], 403);
        }

        if (!$trialSample->canBeApprovedByPurchasing()) {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan tidak valid untuk di-approve'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $trialSample->update([
                'status' => 'purchasing_approved',
                'purchasing_user_id' => Auth::id(),
                'purchasing_reviewed_at' => now(),
                'purchasing_notes' => $request->notes
            ]);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'purchasing_approved',
                'Pengajuan disetujui purchasing',
                ['notes' => $request->notes]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui'
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
     * Purchasing reject trial sample
     */
    public function purchasingReject(Request $request, TrialSample $trialSample)
    {
        if (!Auth::user()->hasRole('purchasing')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk reject pengajuan'
            ], 403);
        }

        if (!$trialSample->canBeApprovedByPurchasing()) {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan tidak valid untuk di-reject'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $trialSample->update([
                'status' => 'purchasing_rejected',
                'purchasing_user_id' => Auth::id(),
                'purchasing_reviewed_at' => now(),
                'purchasing_notes' => $request->notes
            ]);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'purchasing_rejected',
                'Pengajuan ditolak purchasing',
                ['notes' => $request->notes]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil ditolak'
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
     * QA start processing trial sample
     */
    public function qaStartProcessing(TrialSample $trialSample)
    {
        if (!Auth::user()->hasRole('quality_assurance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk memulai proses QA'
            ], 403);
        }

        if ($trialSample->status !== 'purchasing_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan tidak valid untuk diproses QA'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $trialSample->update([
                'status' => 'qa_processing',
                'qa_user_id' => Auth::id()
            ]);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'qa_processing',
                'QA mulai memproses pengajuan'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proses QA berhasil dimulai'
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
     * Close trial sample
     */
    public function close(TrialSample $trialSample)
    {
        if ($trialSample->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk close pengajuan ini'
            ], 403);
        }

        if (!$trialSample->canBeClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan tidak valid untuk di-close'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $trialSample->update([
                'status' => 'closed',
                'closed_by' => Auth::id(),
                'closed_at' => now()
            ]);

            // Log workflow history
            TrialWorkflowHistory::logAction(
                $trialSample->id,
                Auth::id(),
                'closed',
                'Pengajuan di-close'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil di-close'
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
