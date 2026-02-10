<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaanPrepress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class JenisPekerjaanPrepressController extends Controller
{
    public function index()
    {
        return view('main.master.jenis-pekerjaan-prepress');
    }

    public function data()
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::get();

        return DataTables::of($jenisPekerjaan)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-warning btn-sm edit-data" data-id="' . $row->id . '">
                            <i class="mdi mdi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-data ml-1" data-id="' . $row->id . '">
                            <i class="mdi mdi-delete"></i> Delete
                        </button>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50',
            'nama_jenis' => 'required|string|max:100',
            'waktu_estimasi' => 'nullable|integer|min:1',
            'job_rate' => 'nullable|numeric|min:0',
            'point_job' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::user()->name;

        if ($request->filled('id')) {
            // Update
            $jenisPekerjaan = JenisPekerjaanPrepress::find($request->id);
            if (!$jenisPekerjaan) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            $data['updated_by'] = Auth::user()->name;
            $jenisPekerjaan->update($data);

            return response()->json(['success' => 'Data berhasil diperbarui']);
        } else {
            // Create
            // Cek kode unik
            if (JenisPekerjaanPrepress::where('kode', $request->kode)->exists()) {
                return response()->json(['success' => false, 'message' => 'Kode sudah digunakan']);
            }

            JenisPekerjaanPrepress::create($data);
            return response()->json(['success' => 'Data berhasil ditambahkan']);
        }
    }

    public function detail($id)
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::find($id);

        if (!$jenisPekerjaan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return response()->json(['success' => true, 'data' => $jenisPekerjaan]);
    }

    public function delete($id)
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::find($id);
        // dd($jenisPekerjaan);

        if (!$jenisPekerjaan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Cek apakah ada job order yang menggunakan jenis pekerjaan ini
        // if ($jenisPekerjaan->jobPrepresses()->count() > 0) {
        //     return response()->json(['success' => false, 'message' => 'Tidak dapat dihapus karena masih digunakan di job order']);
        // }

        $jenisPekerjaan->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    // API untuk mendapatkan data jenis pekerjaan (untuk dropdown di form job order)
    public function getActiveJenisPekerjaan()
    {
        $jenisPekerjaan = JenisPekerjaanPrepress::active()
            ->select('id', 'kode', 'nama_jenis', 'waktu_estimasi', 'job_rate', 'point_job')
            ->orderBy('nama_jenis', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $jenisPekerjaan]);
    }
}
