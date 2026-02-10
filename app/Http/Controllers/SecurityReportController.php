<?php

namespace App\Http\Controllers;

use App\Models\SecurityVehicleChecklist;
use App\Models\SecurityGoodsMovement;
use App\Models\SecurityDailyActivityLog;
use App\Models\SecurityActivityEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityReportController extends Controller
{
    // ===== VEHICLE CHECKLIST METHODS =====

    /**
     * Display vehicle checklist list
     */
    public function vehicleChecklistIndex(Request $request)
    {
        $query = SecurityVehicleChecklist::query();

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // Default tampilkan data hari ini
            $query->whereDate('tanggal', today());
        }

        // Filter berdasarkan shift
        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        // Filter berdasarkan driver
        if ($request->filled('driver')) {
            $query->byDriver($request->driver);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $checklists = $query->orderBy('tanggal', 'desc')
                           ->orderBy('no_urut', 'asc')
                           ->paginate(20);

        // Data untuk filter dropdown
        $drivers = SecurityVehicleChecklist::select('nama_driver')
                                         ->distinct()
                                         ->orderBy('nama_driver')
                                         ->pluck('nama_driver');

        return view('main.security.vehicle-checklist.index', compact('checklists', 'drivers'));
    }

    /**
     * Show form for creating new vehicle checklist
     */
    public function vehicleChecklistCreate()
    {
        // Generate nomor urut untuk hari ini
        $noUrut = SecurityVehicleChecklist::generateNoUrut(today());

        return view('main.security.vehicle-checklist.create', compact('noUrut'));
    }

    /**
     * Store new vehicle checklist
     */
    public function vehicleChecklistStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tanggal' => 'required|date',
            'nama_driver' => 'required|string|max:255',
            'model_kendaraan' => 'required|string|max:255',
            'no_polisi' => 'required|string|max:255',
            'jam_out' => 'nullable',
            'jam_in' => 'nullable',
            'km_awal' => 'nullable|integer|min:0',
            'km_akhir' => 'nullable|integer|min:0',
            'bbm_awal' => 'nullable|numeric|min:0',
            'bbm_akhir' => 'nullable|numeric|min:0',
            'tujuan' => 'required|string',
            'lokasi' => 'required',
            'checklist_pada' => 'required|in:1,2',
            'foto_dashboard' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor urut
            $noUrut = SecurityVehicleChecklist::generateNoUrut($request->tanggal);

            // Handle foto uploads
            $fotoPaths = [];

            // Upload foto dashboard (wajib)
            if ($request->hasFile('foto_dashboard')) {
                $fotoDashboard = $request->file('foto_dashboard');
                $fotoDashboardName = 'foto_dashboard_' . $noUrut . '_' . time() . '.' . $fotoDashboard->getClientOriginalExtension();

                $uploadPath = 'public/vehicle-checklist';
                $fullPath = storage_path('app/' . $uploadPath);

                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                $fotoDashboard->move($fullPath, $fotoDashboardName);
                $fotoPaths['foto_dashboard'] = 'vehicle-checklist/' . $fotoDashboardName;
            }

            // Determine checklist_pada value
            $checklistPada = $request->checklist_pada == '1' ? 'awal_masuk' : 'akhir_keluar';

            $checklist = SecurityVehicleChecklist::create([
                'no_urut' => $noUrut,
                'tanggal' => $request->tanggal,
                'nama_driver' => $request->nama_driver,
                'model_kendaraan' => $request->model_kendaraan,
                'jam_out' => $request->jam_out,
                'jam_in' => $request->jam_in,
                'km_awal' => $request->km_awal,
                'km_akhir' => $request->km_akhir,
                'bbm_awal' => $request->bbm_awal,
                'bbm_akhir' => $request->bbm_akhir,
                'tujuan' => $request->tujuan,
                'no_polisi' => $request->no_polisi,
                'petugas_security' => Auth::user()->name,
                'shift' => $request->shift ?? 'pagi',
                'status' => 'keluar',
                'keterangan' => $request->keterangan,
                'checklist_pada' => $checklistPada,
                'lokasi' => $request->lokasi,
                'foto_dashboard' => $fotoPaths['foto_dashboard'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('security.vehicle-checklist.index')
                           ->with('success', 'Data checklist kendaraan berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing vehicle checklist
     */
    public function vehicleChecklistEdit($id)
    {
        $checklist = SecurityVehicleChecklist::findOrFail($id);
        return view('main.security.vehicle-checklist.edit', compact('checklist'));
    }

    /**
     * Update vehicle checklist
     */
    public function vehicleChecklistUpdate(Request $request, $id)
    {
        $checklist = SecurityVehicleChecklist::findOrFail($id);

        // Validation rules
        // Validasi dinamis berdasarkan checklist_pada
        // $validationRules = [
        //     'tanggal' => 'required|date',
        //     'nama_driver' => 'required|string|max:255',
        //     'model_kendaraan' => 'required|string|max:255',
        //     'checklist_pada' => 'required|in:1,2',
        //     'tujuan' => 'required|string',
        //     'no_polisi' => 'nullable|string|max:255',
        //     'lokasi' => 'required|in:1,2,3',
        //     'foto_kondisi' => 'nullable|image|max:5120',
        //     'foto_dashboard' => 'nullable|image|max:5120',
        //     'foto_driver' => 'nullable|image|max:5120',
        //     'foto_lainnya' => 'nullable|image|max:5120',
        //     'remove_foto_kondisi' => 'nullable|in:0,1',
        //     'remove_foto_dashboard' => 'nullable|in:0,1',
        //     'remove_foto_driver' => 'nullable|in:0,1',
        //     'remove_foto_lainnya' => 'nullable|in:0,1'
        // ];

        // Tambahkan validasi berdasarkan checklist_pada
        // if ($request->checklist_pada == '1') { // AWAL MASUK
        //     $validationRules['jam_in'] = 'required';
        //     $validationRules['km_akhir'] = 'required|integer|min:0';
        //     $validationRules['bbm_akhir'] = 'required|numeric|min:0';
        // } else { // AKHIR KELUAR
        //     $validationRules['jam_out'] = 'required';
        //     $validationRules['km_awal'] = 'required|integer|min:0';
        //     $validationRules['bbm_awal'] = 'required|numeric|min:0';
        // }

        // $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Determine checklist_pada value (konversi dari 1/2 ke string)
            $checklistPada = $request->checklist_pada == '1' ? 'awal_masuk' : 'akhir_keluar';

            // dd($checklistPada);

            // Prepare update data
            $updateData = [
                'tanggal' => $request->tanggal,
                'nama_driver' => $request->nama_driver,
                'model_kendaraan' => $request->model_kendaraan,
                'checklist_pada' => $checklistPada,
                'jam_out' => $request->jam_out,
                'jam_in' => $request->jam_in,
                'km_awal' => $request->km_awal,
                'km_akhir' => $request->km_akhir,
                'bbm_awal' => $request->bbm_awal,
                'bbm_akhir' => $request->bbm_akhir,
                'tujuan' => $request->tujuan,
                'no_polisi' => $request->no_polisi,
                'shift' => $request->shift,
                'keterangan' => $request->keterangan,
                'status' => $request->jam_in ? 'selesai' : 'keluar',
                'lokasi' => $request->lokasi
            ];

            // Handle photo removal
            if ($request->remove_foto_kondisi == '1') {
                $updateData['foto_kondisi'] = null;
                // Delete file from storage if exists
                if ($checklist->foto_kondisi && file_exists(storage_path('app/public/' . $checklist->foto_kondisi))) {
                    unlink(storage_path('app/public/' . $checklist->foto_kondisi));
                }
            }
            if ($request->remove_foto_dashboard == '1') {
                $updateData['foto_dashboard'] = null;
                if ($checklist->foto_dashboard && file_exists(storage_path('app/public/' . $checklist->foto_dashboard))) {
                    unlink(storage_path('app/public/' . $checklist->foto_dashboard));
                }
            }
            if ($request->remove_foto_driver == '1') {
                $updateData['foto_driver'] = null;
                if ($checklist->foto_driver && file_exists(storage_path('app/public/' . $checklist->foto_driver))) {
                    unlink(storage_path('app/public/' . $checklist->foto_driver));
                }
            }
            if ($request->remove_foto_lainnya == '1') {
                $updateData['foto_lainnya'] = null;
                if ($checklist->foto_lainnya && file_exists(storage_path('app/public/' . $checklist->foto_lainnya))) {
                    unlink(storage_path('app/public/' . $checklist->foto_lainnya));
                }
            }

            // Handle photo uploads
            $uploadPath = 'public/vehicle-checklist';
            $fullPath = storage_path('app/' . $uploadPath);

            // Ensure directory exists
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            // Upload foto kondisi
            if ($request->hasFile('foto_kondisi')) {
                $fotoKondisi = $request->file('foto_kondisi');
                $fotoKondisiName = 'foto_kondisi_' . $checklist->no_urut . '_' . time() . '.' . $fotoKondisi->getClientOriginalExtension();

                try {
                    $fotoKondisi->move($fullPath, $fotoKondisiName);
                    $updateData['foto_kondisi'] = 'vehicle-checklist/' . $fotoKondisiName;
                } catch (\Exception $e) {
                    Log::error('Failed to save foto kondisi: ' . $e->getMessage());
                    throw new \Exception('Gagal menyimpan foto kondisi: ' . $e->getMessage());
                }
            }

            // Upload foto dashboard
            if ($request->hasFile('foto_dashboard')) {
                $fotoDashboard = $request->file('foto_dashboard');
                $fotoDashboardName = 'foto_dashboard_' . $checklist->no_urut . '_' . time() . '.' . $fotoDashboard->getClientOriginalExtension();

                try {
                    $fotoDashboard->move($fullPath, $fotoDashboardName);
                    $updateData['foto_dashboard'] = 'vehicle-checklist/' . $fotoDashboardName;
                } catch (\Exception $e) {
                    Log::error('Failed to save foto dashboard: ' . $e->getMessage());
                    throw new \Exception('Gagal menyimpan foto dashboard: ' . $e->getMessage());
                }
            }

            // Upload foto driver
            if ($request->hasFile('foto_driver')) {
                $fotoDriver = $request->file('foto_driver');
                $fotoDriverName = 'foto_driver_' . $checklist->no_urut . '_' . time() . '.' . $fotoDriver->getClientOriginalExtension();

                try {
                    $fotoDriver->move($fullPath, $fotoDriverName);
                    $updateData['foto_driver'] = 'vehicle-checklist/' . $fotoDriverName;
                } catch (\Exception $e) {
                    Log::error('Failed to save foto driver: ' . $e->getMessage());
                    throw new \Exception('Gagal menyimpan foto driver: ' . $e->getMessage());
                }
            }

            // Upload foto lainnya
            if ($request->hasFile('foto_lainnya')) {
                $fotoLainnya = $request->file('foto_lainnya');
                $fotoLainnyaName = 'foto_lainnya_' . $checklist->no_urut . '_' . time() . '.' . $fotoLainnya->getClientOriginalExtension();

                try {
                    $fotoLainnya->move($fullPath, $fotoLainnyaName);
                    $updateData['foto_lainnya'] = 'vehicle-checklist/' . $fotoLainnyaName;
                } catch (\Exception $e) {
                    Log::error('Failed to save foto lainnya: ' . $e->getMessage());
                    throw new \Exception('Gagal menyimpan foto lainnya: ' . $e->getMessage());
                }
            }

            $checklist->update($updateData);

            DB::commit();

            return redirect()->route('security.vehicle-checklist.index')
                           ->with('success', 'Data checklist kendaraan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Update vehicle return (jam masuk)
     */
    public function vehicleChecklistReturn(Request $request, $id)
    {
        $checklist = SecurityVehicleChecklist::findOrFail($id);

        $request->validate([
            'jam_in' => 'required',
            'km_akhir' => 'required|integer|min:' . $checklist->km_awal,
            'bbm_akhir' => 'required|numeric|min:0'
        ]);

        try {
            $checklist->updateKembali(
                $request->jam_in,
                $request->km_akhir,
                $request->bbm_akhir,
                $request->keterangan_masuk
            );

            return redirect()->route('security.vehicle-checklist.index')
                           ->with('success', 'Data kembali kendaraan berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Delete vehicle checklist
     */
    public function vehicleChecklistDestroy($id)
    {
        try {
            $checklist = SecurityVehicleChecklist::findOrFail($id);
            $checklist->delete();

            return redirect()->route('security.vehicle-checklist.index')
                           ->with('success', 'Data checklist kendaraan berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Show vehicle checklist detail
     */
    public function vehicleChecklistShow($id)
    {
        $checklist = SecurityVehicleChecklist::findOrFail($id);
        return view('main.security.vehicle-checklist.show', compact('checklist'));
    }

    /**
     * Export vehicle checklist to Excel/PDF
     */
    public function vehicleChecklistExport(Request $request)
    {
        $query = SecurityVehicleChecklist::query();

        // Apply same filters as index
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        if ($request->filled('driver')) {
            $query->byDriver($request->driver);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $checklists = $query->orderBy('tanggal', 'desc')
                           ->orderBy('no_urut', 'asc')
                           ->get();

        // Return view for PDF export
        return view('main.security.vehicle-checklist.export', compact('checklists'));
    }

    /**
     * Dashboard summary for vehicle checklist
     */
    public function vehicleChecklistDashboard()
    {
        $today = today();

        $summary = [
            'total_hari_ini' => SecurityVehicleChecklist::byDate($today)->count(),
            'keluar_hari_ini' => SecurityVehicleChecklist::byDate($today)->byStatus('keluar')->count(),
            'masuk_hari_ini' => SecurityVehicleChecklist::byDate($today)->byStatus('selesai')->count(),
            'total_bulan_ini' => SecurityVehicleChecklist::whereMonth('tanggal', $today->month)
                                                       ->whereYear('tanggal', $today->year)
                                                       ->count()
        ];

        // Data untuk chart (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartData[] = [
                'tanggal' => $date->format('Y-m-d'),
                'label' => $date->format('d/m'),
                'total' => SecurityVehicleChecklist::byDate($date)->count()
            ];
        }

        return view('main.security.vehicle-checklist.dashboard', compact('summary', 'chartData'));
    }

    // ===== GOODS MOVEMENT METHODS =====

    /**
     * Display goods movement list
     */
    public function goodsMovementIndex(Request $request)
    {
        $query = SecurityGoodsMovement::query();

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // Default tampilkan data hari ini
            $query->whereDate('tanggal', today());
        }

        // Filter berdasarkan shift
        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        // Filter berdasarkan jenis movement
        if ($request->filled('jenis_movement')) {
            $query->byMovement($request->jenis_movement);
        }

        // Filter berdasarkan status
        if ($request->filled('status_filter')) {
            switch ($request->status_filter) {
                case 'belum_keluar':
                    $query->where('status_laporan', 'IN')->whereNull('jam_keluar');
                    break;
                case 'sudah_keluar':
                    $query->where('status_laporan', 'IN')->whereNotNull('jam_keluar');
                    break;
                case 'lengkap':
                    $query->where('status_laporan', 'OUT');
                    break;
            }
        }

        // Filter berdasarkan nama pengunjung
        if ($request->filled('nama_pengunjung')) {
            $query->byVisitor($request->nama_pengunjung);
        }

        // Filter berdasarkan perusahaan
        if ($request->filled('perusahaan_asal')) {
            $query->where('perusahaan_asal', 'like', '%' . $request->perusahaan_asal . '%');
        }

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi_filter')) {
            $query->where('lokasi', $request->lokasi_filter);
        }

        // Filter berdasarkan jenis barang
        if ($request->filled('jenis_barang')) {
            $query->byGoods($request->jenis_barang);
        }

        $movements = $query->orderBy('tanggal', 'desc')
                          ->orderBy('no_urut', 'asc')
                          ->paginate(20);

        // Data untuk filter dropdown
        $visitors = SecurityGoodsMovement::select('nama_pengunjung')
                                       ->distinct()
                                       ->orderBy('nama_pengunjung')
                                       ->pluck('nama_pengunjung');

        $goodsTypes = SecurityGoodsMovement::select('jenis_barang')
                                         ->distinct()
                                         ->orderBy('jenis_barang')
                                         ->pluck('jenis_barang');

        return view('main.security.goods-movement.index', compact('movements', 'visitors', 'goodsTypes'));
    }

    /**
     * Show form for creating new goods movement
     */
    public function goodsMovementCreate()
    {
        // Generate nomor urut untuk hari ini
        $noUrut = SecurityGoodsMovement::generateNoUrut(today());

        return view('main.security.goods-movement.create', compact('noUrut'));
    }

    /**
     * Store new goods movement
     */
    public function goodsMovementStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tanggal' => 'required|date',
            'nama_pengunjung' => 'required|string|max:255',
            'jam_masuk' => 'required|string',
            'jam_keluar' => 'nullable|string',

            // 'status_laporan' => 'required|in:IN,OUT',
            'barang' => 'nullable|array',
            'barang.*.jenis_barang' => 'nullable|string',
            'barang.*.jumlah' => 'nullable|integer|min:0',
            'barang.*.satuan' => 'nullable|string',
            'barang.*.berat' => 'nullable|numeric|min:0',
            'barang_keluar' => 'nullable|array',
            'barang_keluar.*.jenis_barang' => 'nullable|string',
            'barang_keluar.*.jumlah' => 'nullable|integer|min:0',
            'barang_keluar.*.satuan' => 'nullable|string',
            'barang_keluar.*.berat' => 'nullable|numeric|min:0',
            'jenis_kendaraan' => 'required|string',
            'no_polisi' => 'required|string',
            // 'nama_driver' => 'required|string',
            'no_surat_jalan' => 'required|string',
            'lokasi' => 'required|in:1,2,3'
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor urut
            $noUrut = SecurityGoodsMovement::generateNoUrut($request->tanggal);

            // Process barang items (optional)
            $barangItems = [];
            if ($request->has('barang') && is_array($request->barang)) {
                foreach ($request->barang as $index => $barang) {
                    // Only process if at least jenis_barang is filled
                    if (!empty($barang['jenis_barang'])) {
                        $barangItems[] = [
                            'jenis_barang' => $barang['jenis_barang'],
                            'deskripsi_barang' => $barang['deskripsi_barang'] ?? null,
                            'jumlah' => $barang['jumlah'] ? (int) $barang['jumlah'] : null,
                            'satuan' => $barang['satuan'] ?? null,
                            'berat' => $barang['berat'] ? (float) $barang['berat'] : null
                        ];
                    }
                }
            }

            // Process barang keluar items (opsional)
            $barangKeluarItems = [];
            if ($request->has('barang_keluar') && is_array($request->barang_keluar)) {
                foreach ($request->barang_keluar as $index => $barangKeluar) {
                    // Only process if at least jenis_barang is filled
                    if (!empty($barangKeluar['jenis_barang'])) {
                        $barangKeluarItems[] = [
                            'jenis_barang' => $barangKeluar['jenis_barang'],
                            'deskripsi_barang' => $barangKeluar['deskripsi_barang'] ?? null,
                            'jumlah' => $barangKeluar['jumlah'] ? (int) $barangKeluar['jumlah'] : null,
                            'satuan' => $barangKeluar['satuan'] ?? null,
                            'berat' => $barangKeluar['berat'] ? (float) $barangKeluar['berat'] : null
                        ];
                    }
                }
            }

            $movement = SecurityGoodsMovement::create([
                'no_urut' => $noUrut,
                'tanggal' => $request->tanggal,
                'nama_pengunjung' => $request->nama_pengunjung,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'perusahaan_asal' => $request->perusahaan_asal,
                'jam_masuk' => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'status_laporan' => $request->status_laporan,
                'jenis_movement' => '', // Default to keluar since form is for outgoing goods
                'barang_items' => $barangItems, // Store as JSON
                'barang_keluar_items' => $barangKeluarItems, // Store barang keluar as JSON
                // Keep old fields for backward compatibility (use first item if exists)
                'jenis_barang' => !empty($barangItems) ? $barangItems[0]['jenis_barang'] : null,
                'deskripsi_barang' => !empty($barangItems) ? $barangItems[0]['deskripsi_barang'] : null,
                'jumlah' => !empty($barangItems) ? $barangItems[0]['jumlah'] : null,
                'satuan' => !empty($barangItems) ? $barangItems[0]['satuan'] : null,
                'berat' => !empty($barangItems) ? $barangItems[0]['berat'] : null,
                'tujuan' => $request->tujuan,
                'asal' => $request->asal,
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'no_polisi' => $request->no_polisi,
                'nama_driver' => $request->nama_driver,
                'no_surat_jalan' => $request->no_surat_jalan,
                'no_invoice' => $request->no_invoice,
                'dokumen_pendukung' => $request->dokumen_pendukung,
                'petugas_security' => Auth::user()->name,
                'shift' => $request->shift ?? 'pagi',
                'keterangan' => $request->keterangan,
                'lokasi' => $request->lokasi
            ]);

            DB::commit();

            return redirect()->route('security.goods-movement.index')
                           ->with('success', 'Data keluar/masuk barang berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show goods movement detail
     */
    public function goodsMovementShow($id)
    {
        $movement = SecurityGoodsMovement::findOrFail($id);
        return view('main.security.goods-movement.show', compact('movement'));
    }

    /**
     * Show form for editing goods movement
     */
    public function goodsMovementEdit($id)
    {
        $movement = SecurityGoodsMovement::findOrFail($id);
        $mode = request()->get('mode'); // Ambil parameter mode dari URL

        if ($mode === 'update-jam-keluar') {
            // Mode khusus untuk update jam keluar saja
            return view('main.security.goods-movement.edit', compact('movement', 'mode'));
        }

        return view('main.security.goods-movement.edit', compact('movement'));
    }

    /**
     * Update goods movement
     */
    public function goodsMovementUpdate(Request $request, $id)
    {
        $movement = SecurityGoodsMovement::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'nama_pengunjung' => 'required|string|max:255',
            'jam_masuk' => 'required|string',
            'jam_keluar' => 'nullable|string',
            'status_laporan' => 'required|in:IN,OUT',
            // 'barang' => 'nullable|array',
            // 'barang.*.jenis_barang' => 'nullable|string',
            // 'barang.*.jumlah' => 'nullable|integer|min:0',
            // 'barang.*.satuan' => 'nullable|string',
            // 'barang.*.berat' => 'nullable|numeric|min:0',
            'barang_keluar' => 'nullable|array',
            'barang_keluar.*.jenis_barang' => 'nullable|string',
            'barang_keluar.*.jumlah' => 'nullable|integer|min:0',
            'barang_keluar.*.satuan' => 'nullable|string',
            'barang_keluar.*.berat' => 'nullable|numeric|min:0',
            // 'jenis_kendaraan' => 'required|string',
            // 'no_polisi' => 'required|string',
            // 'nama_driver' => 'required|string',
            // 'no_surat_jalan' => 'required|string',
            // 'lokasi' => 'required|in:1,2,3'
        ]);

        // dd($request->all());

        try {
            DB::beginTransaction();

            // Process barang items (opsional)
            $barangItems = [];
            if ($request->has('barang') && is_array($request->barang)) {
                foreach ($request->barang as $index => $barang) {
                    // Only process if at least jenis_barang is filled
                    if (!empty($barang['jenis_barang'])) {
                        $barangItems[] = [
                            'jenis_barang' => $barang['jenis_barang'],
                            'deskripsi_barang' => $barang['deskripsi_barang'] ?? null,
                            'jumlah' => $barang['jumlah'] ? (int) $barang['jumlah'] : null,
                            'satuan' => $barang['satuan'] ?? null,
                            'berat' => $barang['berat'] ? (float) $barang['berat'] : null
                        ];
                    }
                }
            }

            // Process barang keluar items (opsional)
            $barangKeluarItems = [];
            if ($request->has('barang_keluar') && is_array($request->barang_keluar)) {
                foreach ($request->barang_keluar as $index => $barangKeluar) {
                    // Only process if at least jenis_barang is filled
                    if (!empty($barangKeluar['jenis_barang'])) {
                        $barangKeluarItems[] = [
                            'jenis_barang' => $barangKeluar['jenis_barang'],
                            'deskripsi_barang' => $barangKeluar['deskripsi_barang'] ?? null,
                            'jumlah' => $barangKeluar['jumlah'] ? (int) $barangKeluar['jumlah'] : null,
                            'satuan' => $barangKeluar['satuan'] ?? null,
                            'berat' => $barangKeluar['berat'] ? (float) $barangKeluar['berat'] : null
                        ];
                    }
                }
            }

            // dd($barangKeluarItems);

            $movement->update([
                'tanggal' => $request->tanggal,
                'nama_pengunjung' => $request->nama_pengunjung,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'perusahaan_asal' => $request->perusahaan_asal,
                'jam_masuk' => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'status_laporan' => $request->status_laporan,
                'jenis_movement' => '', // Default to keluar since form is for outgoing goods
                'barang_items' => $barangItems, // Store as JSON
                'barang_keluar_items' => $barangKeluarItems, // Store barang keluar as JSON
                // Keep old fields for backward compatibility (use first item if exists)
                'jenis_barang' => !empty($barangItems) ? $barangItems[0]['jenis_barang'] : null,
                'deskripsi_barang' => !empty($barangItems) ? $barangItems[0]['deskripsi_barang'] : null,
                'jumlah' => !empty($barangItems) ? $barangItems[0]['jumlah'] : null,
                'satuan' => !empty($barangItems) ? $barangItems[0]['satuan'] : null,
                'berat' => !empty($barangItems) ? $barangItems[0]['berat'] : null,
                'tujuan' => $request->tujuan,
                'asal' => $request->asal,
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'no_polisi' => $request->no_polisi,
                'nama_driver' => $request->nama_driver,
                'no_surat_jalan' => $request->no_surat_jalan,
                'no_invoice' => $request->no_invoice,
                'dokumen_pendukung' => $request->dokumen_pendukung,
                'shift' => 0,
                'keterangan' => $request->keterangan,
                'catatan_security' => $request->catatan_security,
                'lokasi' => $request->lokasi
            ]);

            DB::commit();

            return redirect()->route('security.goods-movement.index')
                           ->with('success', 'Data keluar/masuk barang berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }


    /**
     * Delete goods movement
     */
    public function goodsMovementDestroy($id)
    {
        try {
            $movement = SecurityGoodsMovement::findOrFail($id);
            $movement->delete();

            return redirect()->route('security.goods-movement.index')
                           ->with('success', 'Data keluar/masuk barang berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export goods movement to PDF
     */
    public function goodsMovementExport(Request $request)
    {
        $query = SecurityGoodsMovement::query();

        // Apply same filters as index
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        if ($request->filled('jenis_movement')) {
            $query->byMovement($request->jenis_movement);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('nama_pengunjung')) {
            $query->byVisitor($request->nama_pengunjung);
        }

        if ($request->filled('perusahaan_asal')) {
            $query->where('perusahaan_asal', 'like', '%' . $request->perusahaan_asal . '%');
        }

        if ($request->filled('lokasi_filter')) {
            $query->where('lokasi', $request->lokasi_filter);
        }

        if ($request->filled('jenis_barang')) {
            $query->byGoods($request->jenis_barang);
        }

        $movements = $query->orderBy('tanggal', 'desc')
                          ->orderBy('no_urut', 'asc')
                          ->get();

        return view('main.security.goods-movement.export', compact('movements'));
    }

    /**
     * Export goods movement to Excel
     */
    public function goodsMovementExportExcel(Request $request)
    {
        $query = SecurityGoodsMovement::query();

        // Apply same filters as index
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        if ($request->filled('jenis_movement')) {
            $query->byMovement($request->jenis_movement);
        }

        if ($request->filled('status_filter')) {
            switch ($request->status_filter) {
                case 'belum_keluar':
                    $query->where('status_laporan', 'IN')->whereNull('jam_keluar');
                    break;
                case 'sudah_keluar':
                    $query->where('status_laporan', 'IN')->whereNotNull('jam_keluar');
                    break;
                case 'lengkap':
                    $query->where('status_laporan', 'OUT');
                    break;
            }
        }

        if ($request->filled('nama_pengunjung')) {
            $query->byVisitor($request->nama_pengunjung);
        }

        if ($request->filled('perusahaan_asal')) {
            $query->where('perusahaan_asal', 'like', '%' . $request->perusahaan_asal . '%');
        }

        if ($request->filled('lokasi_filter')) {
            $query->where('lokasi', $request->lokasi_filter);
        }

        if ($request->filled('jenis_barang')) {
            $query->byGoods($request->jenis_barang);
        }

        $movements = $query->orderBy('tanggal', 'desc')
                          ->orderBy('no_urut', 'asc')
                          ->get();

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title (Excel sheet title max 31 chars, no invalid chars like : \ / ? * [ ])
        $sheetTitle = 'Laporan Keluar Masuk Barang';
        // Truncate if exceeds 31 characters (Excel limit)
        if (strlen($sheetTitle) > 31) {
            $sheetTitle = substr($sheetTitle, 0, 31);
        }
        $sheet->setTitle($sheetTitle);

        // Header info
        $sheet->setCellValue('A1', 'LAPORAN KELUAR/MASUK BARANG');
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'PT. KRISANTHIUM OFFSET');
        $sheet->mergeCells('A2:Q2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $periode = 'Periode: ' . ($request->tanggal_dari ? \Carbon\Carbon::parse($request->tanggal_dari)->format('d/m/Y') : 'Semua') .
                   ' - ' . ($request->tanggal_sampai ? \Carbon\Carbon::parse($request->tanggal_sampai)->format('d/m/Y') : 'Semua');
        $sheet->setCellValue('A3', $periode);
        $sheet->mergeCells('A3:Q3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Dicetak pada: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A4:Q4');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Column headers
        $headers = [
            'A' => 'No',
            'B' => 'Tanggal',
            'C' => 'Lokasi',
            'D' => 'Nama Pengunjung',
            'E' => 'Perusahaan',
            'F' => 'Status Laporan',
            'G' => 'Jenis Barang',
            'H' => 'Deskripsi Barang',
            'I' => 'Jumlah',
            'J' => 'Satuan',
            'K' => 'Berat (kg)',
            'L' => 'Jam Masuk',
            'M' => 'Jam Keluar',
            'N' => 'Jenis Kendaraan',
            'O' => 'No. Polisi',
            'P' => 'No. Surat Jalan',
            'Q' => 'No. Invoice',
        ];

        $row = 6;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getStyle($col . $row)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // Data rows
        $row = 7;
        $no = 1;
        foreach ($movements as $movement) {
            // Get lokasi label
            $lokasiLabel = '-';
            if ($movement->lokasi == 1) {
                $lokasiLabel = 'Lokasi 19 (KRISANTHIUM)';
            } elseif ($movement->lokasi == 2) {
                $lokasiLabel = 'Lokasi 23 (KRISANTHIUM)';
            } elseif ($movement->lokasi == 3) {
                $lokasiLabel = 'Lokasi 15 (BERBEK)';
            }

            // Get status laporan
            $statusLaporan = $movement->status_laporan ?? '-';
            if ($movement->status_laporan == 'IN' && !$movement->jam_keluar) {
                $statusLaporan = 'IN - Belum Keluar';
            } elseif ($movement->status_laporan == 'IN' && $movement->jam_keluar) {
                $statusLaporan = 'IN - Sudah Keluar';
            } elseif ($movement->status_laporan == 'OUT') {
                $statusLaporan = 'OUT - Lengkap';
            }

            $sheet->setCellValue('A' . $row, $movement->no_urut ?? $no);
            $sheet->setCellValue('B' . $row, $movement->tanggal ? \Carbon\Carbon::parse($movement->tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $row, $lokasiLabel);
            $sheet->setCellValue('D' . $row, $movement->nama_pengunjung ?? '-');
            $sheet->setCellValue('E' . $row, $movement->perusahaan_asal ?? '-');
            $sheet->setCellValue('F' . $row, $statusLaporan);
            $sheet->setCellValue('G' . $row, $movement->jenis_barang ?? '-');
            $sheet->setCellValue('H' . $row, $movement->deskripsi_barang ?? '-');
            $sheet->setCellValue('I' . $row, $movement->jumlah ? number_format($movement->jumlah, 0, ',', '.') : '-');
            $sheet->setCellValue('J' . $row, $movement->satuan ?? '-');
            $sheet->setCellValue('K' . $row, $movement->berat ? number_format($movement->berat, 2, ',', '.') : '-');
            $sheet->setCellValue('L' . $row, $movement->jam_masuk ? \Carbon\Carbon::parse($movement->jam_masuk)->format('H:i') : '-');
            $sheet->setCellValue('M' . $row, $movement->jam_keluar ? \Carbon\Carbon::parse($movement->jam_keluar)->format('H:i') : '-');
            $sheet->setCellValue('N' . $row, $movement->jenis_kendaraan ?? '-');
            $sheet->setCellValue('O' . $row, $movement->no_polisi ?? '-');
            $sheet->setCellValue('P' . $row, $movement->no_surat_jalan ?? '-');
            $sheet->setCellValue('Q' . $row, $movement->no_invoice ?? '-');

            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column width for better readability
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);

        // Set border for header
        $headerRange = 'A6:Q6';
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set border for data
        if ($row > 7) {
            $dataRange = 'A7:Q' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Wrap text for description columns
        $sheet->getStyle('H7:H' . ($row - 1))->getAlignment()->setWrapText(true);

        // Generate filename
        $filename = 'laporan_keluar_masuk_barang_' . date('Ymd_His') . '.xlsx';

        // Write and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Dashboard summary for goods movement
     */
    public function goodsMovementDashboard()
    {
        $today = today();

        $summary = SecurityGoodsMovement::getSummaryStats($today);

        // Summary bulan ini
        $summary['total_bulan_ini'] = SecurityGoodsMovement::whereMonth('tanggal', $today->month)
                                                         ->whereYear('tanggal', $today->year)
                                                         ->count();

        // Data untuk chart (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $stats = SecurityGoodsMovement::getSummaryStats($date);
            $chartData[] = [
                'tanggal' => $date->format('Y-m-d'),
                'label' => $date->format('d/m'),
                'masuk' => $stats['masuk'],
                'keluar' => $stats['keluar'],
                'total' => $stats['total']
            ];
        }

        return view('main.security.goods-movement.dashboard', compact('summary', 'chartData'));
    }

    // ==================== DAILY ACTIVITY LOG ====================

    public function dailyActivityIndex(Request $request)
    {
        $query = SecurityDailyActivityLog::with('activityEntries');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // Default tampilkan data hari ini
            // $query->whereDate('tanggal', today());
        }

        // Filter berdasarkan shift
        if ($request->filled('shift')) {
            $query->byShift($request->shift);
        }

        // Filter berdasarkan personil
        if ($request->filled('personil')) {
            $query->byPersonil($request->personil);
        }

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $logs = $query->orderBy('tanggal', 'desc')
                     ->orderBy('jam_mulai', 'asc')
                     ->paginate(100)
                     ->appends($request->query());

        // Data untuk filter dropdown
        $shifts = ['I' => 'I (Pagi)', 'II' => 'II (Sore)', 'III' => 'III (Malam)'];
        $personils = SecurityDailyActivityLog::select('personil_jaga')
                                           ->distinct()
                                           ->orderBy('personil_jaga')
                                           ->pluck('personil_jaga');
        $lokasis = SecurityDailyActivityLog::select('lokasi')
                                         ->distinct()
                                         ->orderBy('lokasi')
                                         ->pluck('lokasi');

        return view('main.security.daily-activity.index', compact('logs', 'shifts', 'personils', 'lokasis'));
    }

    public function dailyActivityCreate()
    {
        return view('main.security.daily-activity.create');
    }

    public function dailyActivityStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:I,II,III',
            // 'lokasi' => 'required|in:19,23,15',
            // 'jam_mulai' => 'required',
            // 'jam_selesai' => 'required',
            // 'personil_jaga' => 'required|string|max:255',
            // 'kondisi_awal' => 'nullable|string',
            // 'kondisi_akhir' => 'nullable|string',
            // 'menyerahkan_by' => 'nullable|string|max:100',
            // 'diterima_by' => 'nullable|string|max:100',
            // 'diketahui_by' => 'nullable|string|max:100',
            'activities' => 'required|array|min:1',
            'activities.*.time_in' => 'nullable',
            'activities.*.time_out' => 'nullable',
            'activities.*.keterangan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Generate hari dari tanggal
            $tanggal = Carbon::parse($request->tanggal);
            $hari = $tanggal->format('l'); // Monday, Tuesday, etc.
            // dd($hari);

            try {
                $log = SecurityDailyActivityLog::create([
                    // 'no_laporan' => SecurityDailyActivityLog::generateNoLaporan($request->tanggal),
                    'tanggal' => $request->tanggal,
                    'hari' => $hari,
                    'shift' => $request->shift,
                    'lokasi' => $request->lokasi,
                    'personil_jaga' => $request->personil_jaga ?? '',
                    // 'kondisi_awal' => $request->kondisi_awal,
                    // 'kondisi_akhir' => $request->kondisi_akhir,
                    'menyerahkan_by' => $request->menyerahkan_by ?? '',
                    'diterima_by' => $request->diterima_by ?? '',
                    'diketahui_by' => $request->diketahui_by ?? '',
                    'petugas_security' => Auth::user()->name
                ]);
            } catch (\Throwable $th) {
                dd($th);
                //throw $th;
            }


            // dd($log);


            // Simpan activity entries
            foreach ($request->activities as $index => $activity) {
                if (!empty($activity['keterangan'])) {
                    SecurityActivityEntry::create([
                        'daily_log_id' => $log->id,
                        'urutan' => $index + 1,
                        'time_in' => $activity['time_in'],
                        'time_out' => $activity['time_out'],
                        'keterangan' => $activity['keterangan']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('security.daily-activity.index')
                           ->with('success', 'Laporan aktivitas harian berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function dailyActivityShow($id)
    {
        $log = SecurityDailyActivityLog::with('activityEntries')->findOrFail($id);
        return view('main.security.daily-activity.show', compact('log'));
    }

    public function dailyActivityEdit($id)
    {
        $log = SecurityDailyActivityLog::with('activityEntries')->findOrFail($id);
        return view('main.security.daily-activity.edit', compact('log'));
    }

    public function dailyActivityUpdate(Request $request, $id)
    {
        // dd($request->all());
        $log = SecurityDailyActivityLog::findOrFail($id);

        // dd($log);


        try {
            DB::beginTransaction();

            // Update daily log
            $tanggal = Carbon::parse($request->tanggal);
            $hari = $tanggal->format('l');

            $log->update([
                'tanggal' => $request->tanggal,
                'hari' => $hari,
                'shift' => $request->shift,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'personil_jaga' => $request->personil_jaga,
                'kondisi_awal' => $request->kondisi_awal,
                'kondisi_akhir' => $request->kondisi_akhir,
                'menyerahkan_by' => $request->menyerahkan_by,
                'diterima_by' => $request->diterima_by,
                'diketahui_by' => $request->diketahui_by
            ]);

            // Hapus activity entries lama
            $log->activityEntries()->delete();

            // Simpan activity entries baru
            foreach ($request->activities as $index => $activity) {
                if (!empty($activity['keterangan'])) {
                    SecurityActivityEntry::create([
                        'daily_log_id' => $log->id,
                        'urutan' => $index + 1,
                        'time_in' => $activity['time_in'],
                        'time_out' => $activity['time_out'],
                        'keterangan' => $activity['keterangan']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('security.daily-activity.index')
                           ->with('success', 'Laporan aktivitas harian berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function dailyActivityDestroy($id)
    {
        try {
            $log = SecurityDailyActivityLog::findOrFail($id);

            // Check if user is authorized to delete this log
            $currentUser = auth()->user();
            $isAuthorized = false;

            // Check created_by field first (if exists), then fallback to petugas_security
            if (isset($log->created_by) && $log->created_by) {
                $isAuthorized = $log->created_by === $currentUser->id ||
                               $log->created_by === $currentUser->name ||
                               $log->created_by === $currentUser->username;
            } else {
                // Fallback to petugas_security field
                $isAuthorized = $log->petugas_security === $currentUser->name ||
                               $log->petugas_security === $currentUser->username;
            }

            if (!$isAuthorized) {
                // Return JSON response for AJAX requests
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menghapus laporan ini. Hanya pembuat laporan yang dapat menghapus.'
                    ], 403);
                }

                return redirect()->back()
                               ->with('error', 'Anda tidak memiliki izin untuk menghapus laporan ini. Hanya pembuat laporan yang dapat menghapus.');
            }

            $log->delete();

            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan aktivitas harian berhasil dihapus'
                ]);
            }

            return redirect()->route('security.daily-activity.index')
                           ->with('success', 'Laporan aktivitas harian berhasil dihapus');

        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export daily activity to PDF
     */
    public function dailyActivityExport(Request $request, $id = null)
    {
        try {
            if ($id) {
                // Export single report
                $log = SecurityDailyActivityLog::with('activityEntries')->findOrFail($id);
                $logs = collect([$log]);
            } else {
                // Export filtered reports
                $query = SecurityDailyActivityLog::with('activityEntries');

                // Apply same filters as index
                if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                    $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
                } elseif ($request->filled('tanggal')) {
                    $query->whereDate('tanggal', $request->tanggal);
                }

                if ($request->filled('shift')) {
                    $query->where('shift', $request->shift);
                }

                if ($request->filled('personil')) {
                    $query->where('personil_jaga', $request->personil);
                }

                if ($request->filled('lokasi')) {
                    $query->where('lokasi', $request->lokasi);
                }

                $logs = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam_mulai', 'asc')
                             ->get();
            }

            // Return view for PDF export
            return view('main.security.daily-activity.export', compact('logs'));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Get security dashboard data for AJAX
     */
    public function dashboardData()
    {
        try {
            $today = now()->format('Y-m-d');

            // Get today's statistics
            $todayVehicleChecklists = SecurityVehicleChecklist::whereDate('created_at', $today)->count();
            $todayGoodsMovements = SecurityGoodsMovement::whereDate('created_at', $today)->count();
            $todayDailyActivities = SecurityDailyActivityLog::whereDate('created_at', $today)->count();

            // Get recent activities
            $recentVehicleChecklists = SecurityVehicleChecklist::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $recentGoodsMovements = SecurityGoodsMovement::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today_vehicle_checklists' => $todayVehicleChecklists,
                    'today_goods_movements' => $todayGoodsMovements,
                    'today_daily_activities' => $todayDailyActivities,
                ],
                'recent_vehicle_checklists' => $recentVehicleChecklists,
                'recent_goods_movements' => $recentGoodsMovements,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle checklist dashboard data for AJAX
     */
    public function vehicleChecklistDashboardData()
    {
        try {
            $today = now()->format('Y-m-d');

            // Get today's statistics
            $todayCount = SecurityVehicleChecklist::whereDate('created_at', $today)->count();
            $pendingCount = SecurityVehicleChecklist::where('status', 'keluar')->count();
            $completedCount = SecurityVehicleChecklist::where('status', 'kembali')->count();

            // Get recent activities
            $recentActivities = SecurityVehicleChecklist::orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today_count' => $todayCount,
                    'pending_count' => $pendingCount,
                    'completed_count' => $completedCount,
                ],
                'recent_activities' => $recentActivities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get goods movement dashboard data for AJAX
     */
    public function goodsMovementDashboardData()
    {
        try {
            $today = now()->format('Y-m-d');

            // Get today's statistics
            $todayCount = SecurityGoodsMovement::whereDate('created_at', $today)->count();
            $inCount = SecurityGoodsMovement::where('jenis_movement', 'masuk')->count();
            $outCount = SecurityGoodsMovement::where('jenis_movement', 'keluar')->count();

            // Get recent activities
            $recentActivities = SecurityGoodsMovement::orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today_count' => $todayCount,
                    'in_count' => $inCount,
                    'out_count' => $outCount,
                ],
                'recent_activities' => $recentActivities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}
