<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingMaterial;
use App\Models\TrainingMaterialCategory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = TrainingMaterialCategory::orderBy('display_order', 'asc')->get();
        return view('portal-training.master.materials.index', compact('categories'));
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $materials = TrainingMaterial::with('category')->orderBy('display_order', 'asc');

        return DataTables::of($materials)
            ->addIndexColumn()
            ->addColumn('title', function($material) {
                return $material->material_title;
            })
            ->addColumn('category_name', function($material) {
                return $material->category ? $material->category->category_name : '-';
            })
            ->addColumn('duration_formatted', function($material) {
                return $material->formatted_duration ?? '-';
            })
            ->addColumn('order', function($material) {
                return $material->display_order;
            })
            ->addColumn('status_badge', function($material) {
                if ($material->is_active) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Tidak Aktif</span>';
                }
            })
            ->addColumn('action', function($material) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-primary btn-view" data-id="'.$material->id.'" title="Lihat">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$material->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$material->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store a new material
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer',
            'video_path' => 'required|string|max:500',
            'duration' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $material = TrainingMaterial::create([
            'material_title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'video_path' => $request->video_path,
            'video_duration_seconds' => $request->duration,
            'display_order' => $request->order,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Materi training berhasil ditambahkan.',
            'data' => $material
        ]);
    }

    /**
     * Show single material
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $material = TrainingMaterial::with('category')->findOrFail($id);

        return response()->json([
            'id' => $material->id,
            'title' => $material->material_title,
            'description' => $material->description,
            'category_id' => $material->category_id,
            'category_name' => $material->category ? $material->category->category_name : '-',
            'video_path' => $material->video_path,
            'duration' => $material->video_duration_seconds,
            'order' => $material->display_order,
            'is_active' => $material->is_active,
        ]);
    }

    /**
     * Update material
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer',
            'video_path' => 'required|string|max:500',
            'duration' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $material = TrainingMaterial::findOrFail($id);
        $material->update([
            'material_title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'video_path' => $request->video_path,
            'video_duration_seconds' => $request->duration,
            'display_order' => $request->order,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Materi training berhasil diupdate.',
            'data' => $material
        ]);
    }

    /**
     * Delete material
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $material = TrainingMaterial::findOrFail($id);

        // Cek apakah material digunakan di assignment
        if ($material->assignments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak dapat dihapus karena sudah digunakan dalam assignment.'
            ], 400);
        }

        // Cek apakah ada exam untuk material ini
        if ($material->exams()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak dapat dihapus karena sudah ada ujian yang dibuat.'
            ], 400);
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi training berhasil dihapus.'
        ]);
    }

    /**
     * Upload video file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi,wmv|max:102400', // Max 100MB
        ]);

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('training/videos', 'public');
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'message' => 'Video berhasil diupload.',
                'path' => $path,
                'url' => $url
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupload video.'
        ], 400);
    }
}
