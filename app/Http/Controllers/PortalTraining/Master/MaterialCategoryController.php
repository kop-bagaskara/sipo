<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingMaterialCategory;
use Yajra\DataTables\Facades\DataTables;

class MaterialCategoryController extends Controller
{
    /**
     * Display a listing of material categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('portal-training.master.categories.index');
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $categories = TrainingMaterialCategory::orderBy('display_order', 'asc')->get();

        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('name', function($category) {
                return $category->category_name;
            })
            ->addColumn('order', function($category) {
                return $category->display_order;
            })
            ->addColumn('action', function($category) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$category->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$category->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->make(true);
    }

    /**
     * Show single category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = TrainingMaterialCategory::findOrFail($id);

        return response()->json([
            'id' => $category->id,
            'name' => $category->category_name,
            'description' => $category->description,
            'order' => $category->display_order,
        ]);
    }

    /**
     * Store a new category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        $category = TrainingMaterialCategory::create([
            'category_name' => $request->name,
            'description' => $request->description,
            'display_order' => $request->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori materi berhasil ditambahkan.',
            'data' => $category
        ]);
    }

    /**
     * Update category
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        $category = TrainingMaterialCategory::findOrFail($id);
        $category->update([
            'category_name' => $request->name,
            'description' => $request->description,
            'display_order' => $request->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori materi berhasil diupdate.',
            'data' => $category
        ]);
    }

    /**
     * Delete category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = TrainingMaterialCategory::findOrFail($id);

        // Cek apakah ada materials yang menggunakan kategori ini
        if ($category->materials()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh materi.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori materi berhasil dihapus.'
        ]);
    }
}
