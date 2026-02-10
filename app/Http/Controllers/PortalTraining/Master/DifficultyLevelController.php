<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingDifficultyLevel;
use Yajra\DataTables\Facades\DataTables;

class DifficultyLevelController extends Controller
{
    /**
     * Display a listing of difficulty levels
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('portal-training.master.difficulty-levels.index');
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $levels = TrainingDifficultyLevel::orderBy('display_order', 'asc')->get();

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('action', function($level) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$level->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$level->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('level_order', function($level) {
                return $level->display_order;
            })
            ->make(true);
    }

    /**
     * Show single difficulty level
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $level = TrainingDifficultyLevel::findOrFail($id);

        return response()->json([
            'id' => $level->id,
            'level_name' => $level->level_name,
            'level_code' => $level->level_code,
            'score_multiplier' => $level->score_multiplier,
            'level_order' => $level->display_order,
        ]);
    }

    /**
     * Store a new difficulty level
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'level_name' => 'required|string|max:255',
            'level_code' => 'required|string|max:50',
            'score_multiplier' => 'required|numeric|min:0|max:10',
            'level_order' => 'required|integer|min:1',
        ]);

        // Manual unique check for level_code
        $exists = TrainingDifficultyLevel::where('level_code', $request->level_code)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kode level sudah digunakan.'
            ], 422);
        }

        $level = TrainingDifficultyLevel::create([
            'level_name' => $request->level_name,
            'level_code' => $request->level_code,
            'score_multiplier' => $request->score_multiplier,
            'display_order' => $request->level_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tingkat kesulitan berhasil ditambahkan.',
            'data' => $level
        ]);
    }

    /**
     * Update difficulty level
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'level_name' => 'required|string|max:255',
            'level_code' => 'required|string|max:50',
            'score_multiplier' => 'required|numeric|min:0|max:10',
            'level_order' => 'required|integer|min:1',
        ]);

        // Manual unique check for level_code (excluding current record)
        $exists = TrainingDifficultyLevel::where('level_code', $request->level_code)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kode level sudah digunakan.'
            ], 422);
        }

        $level = TrainingDifficultyLevel::findOrFail($id);
        $level->update([
            'level_name' => $request->level_name,
            'level_code' => $request->level_code,
            'score_multiplier' => $request->score_multiplier,
            'display_order' => $request->level_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tingkat kesulitan berhasil diupdate.',
            'data' => $level
        ]);
    }

    /**
     * Delete difficulty level
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $level = TrainingDifficultyLevel::findOrFail($id);

        // Cek apakah ada questions yang menggunakan level ini
        if ($level->questions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tingkat kesulitan tidak dapat dihapus karena masih digunakan oleh soal.'
            ], 400);
        }

        $level->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tingkat kesulitan berhasil dihapus.'
        ]);
    }
}
