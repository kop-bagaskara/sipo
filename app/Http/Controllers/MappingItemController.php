<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MappingItem;
use Yajra\DataTables\DataTables;

class MappingItemController extends Controller
{
    // Endpoint untuk DataTables
    public function data(Request $request)
    {
        try {
            $query = MappingItem::query();
            // dd($query);

            return DataTables::of($query)
                ->addIndexColumn()
                ->setRowId('id') // Menambahkan rowId untuk menghindari error DT_RowId
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method test untuk memverifikasi DataTables
    public function testData()
    {
        try {
            $items = MappingItem::take(5)->get();
            return response()->json([
                'success' => true,
                'data' => $items,
                'count' => $items->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Endpoint untuk menampilkan form create (opsional)
    public function create()
    {
        // return view('main.master.mapping-item-create');
    }

    // Endpoint untuk menyimpan data baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required',
            'nama_barang' => 'required',
            'jumlah' => 'required|integer',
            'panjang' => 'required|integer',
            'lebar' => 'required|numeric',
            'gramasi' => 'required|numeric',
            'kg_per_pcs' => 'required|numeric',
            'pcs_dc' => 'required|integer',
            'speed' => 'required|integer',
            'target' => 'required|integer',
            'tipe_jo' => 'required',
            'optimal' => 'required',
            'information' => 'nullable',
            'jumlah_t1' => 'required|integer',
            'jumlah_t2' => 'required|integer',
            'jumlah_t3' => 'required|integer',
            'jumlah_t4' => 'required|integer',
            'jumlah_t5' => 'required|integer',
            'jumlah_t6' => 'required|integer',
            'jumlah_t7' => 'required|integer',
            't1' => 'required',
            't2' => 'required',
            't3' => 'required',
            't4' => 'required',
            't5' => 'required',
            't6' => 'required',
            't7' => 'required',
            'coating1' => 'required',
            'coating2' => 'nullable',
            'dimensi1' => 'required',
            'dimensi2' => 'required',
            'dimensi3' => 'required',
        ]);
        $item = MappingItem::create($data);
        return response()->json(['success' => true, 'data' => $item]);
    }

    // Endpoint untuk edit data
    public function edit($id)
    {
        $item = MappingItem::findOrFail($id);
        return response()->json($item);
    }

    // Endpoint untuk update data
    public function update(Request $request, $id)
    {
        $item = MappingItem::findOrFail($id);
        $data = $request->validate([
            'kode' => 'required',
            'nama_barang' => 'required',
            'jumlah' => 'required|integer',
            'panjang' => 'required|integer',
            'lebar' => 'required|numeric',
            'gramasi' => 'required|numeric',
            'kg_per_pcs' => 'required|numeric',
            'pcs_dc' => 'required|integer',
            'speed' => 'required|integer',
            'target' => 'required|integer',
            'tipe_jo' => 'required',
            'optimal' => 'required',
            'information' => 'nullable',
            'jumlah_t1' => 'required|integer',
            'jumlah_t2' => 'required|integer',
            'jumlah_t3' => 'required|integer',
            'jumlah_t4' => 'required|integer',
            'jumlah_t5' => 'required|integer',
            'jumlah_t6' => 'required|integer',
            'jumlah_t7' => 'required|integer',
            't1' => 'required',
            't2' => 'required',
            't3' => 'required',
            't4' => 'required',
            't5' => 'required',
            't6' => 'required',
            't7' => 'required',
            'coating1' => 'required',
            'coating2' => 'nullable',
            'dimensi1' => 'required',
            'dimensi2' => 'required',
            'dimensi3' => 'required',
        ]);
        $item->update($data);
        return response()->json(['success' => true, 'data' => $item]);
    }

    // Endpoint untuk hapus data
    public function destroy($id)
    {
        $item = MappingItem::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
