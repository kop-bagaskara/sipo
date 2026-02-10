<?php

namespace App\Http\Controllers;

use App\Models\EmployeeData;
use App\Imports\EmployeeDataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class EmployeeDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hr.employee-data.index');
    }

    /**
     * Get employee detail by ID (for modal)
     */
    public function getDetail($id)
    {
        try {
            $employee = EmployeeData::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get data for DataTables
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = EmployeeData::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->setRowId('id')
                ->addColumn('foto', function ($row) {
                    if ($row->foto_path) {
                        return '<img src="' . asset('storage/' . $row->foto_path) . '" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
                    }
                    return '<span class="badge badge-secondary">Tidak ada foto</span>';
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . route('hr.employee-data.edit', $row->id) . '" class="btn btn-sm btn-warning me-1" title="Edit">
                        <i class="mdi mdi-pencil"></i>
                    </a>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger" onclick="deleteEmployee(' . $row->id . ')" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                    </button>';
                    return '<div class="d-flex">' . $editBtn . $deleteBtn . '</div>';
                })
                ->editColumn('tgl_masuk', function ($row) {
                    return $row->tgl_masuk ? Carbon::parse($row->tgl_masuk)->format('d/m/Y') : '-';
                })
                ->editColumn('tgl_lahir', function ($row) {
                    return $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d/m/Y') : '-';
                })
                ->editColumn('tanggal_awal', function ($row) {
                    return $row->tanggal_awal ? Carbon::parse($row->tanggal_awal)->format('d/m/Y') : '-';
                })
                ->editColumn('tanggal_berakhir', function ($row) {
                    return $row->tanggal_berakhir ? Carbon::parse($row->tanggal_berakhir)->format('d/m/Y') : '-';
                })
                ->rawColumns(['foto', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr.employee-data.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:tb_employee_data,nip',
            'nama_karyawan' => 'required|string|max:255',
            'lp' => 'nullable|string|max:10',
            'lvl' => 'nullable|string|max:50',
            'dept' => 'nullable|string|max:100',
            'bagian' => 'nullable|string|max:100',
            'tgl_masuk' => 'nullable|date',
            'status_update' => 'nullable|string|max:50',
            'tanggal_awal' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'masa_kerja' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'usia' => 'nullable|integer',
            'alamat_ktp' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat_domisili' => 'nullable|string',
            'nomor_kontak_darurat' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('employee-photos', 'public');
            $validated['foto_path'] = $fotoPath;
        }

        // Calculate usia if tgl_lahir is provided
        if (isset($validated['tgl_lahir']) && !isset($validated['usia'])) {
            $validated['usia'] = Carbon::parse($validated['tgl_lahir'])->age;
        }

        // Calculate masa_kerja if tgl_masuk is provided
        if (isset($validated['tgl_masuk']) && !isset($validated['masa_kerja'])) {
            $masaKerja = Carbon::parse($validated['tgl_masuk'])->diffInYears(Carbon::now());
            $validated['masa_kerja'] = $masaKerja . ' tahun';
        }

        // Remove 'foto' from validated array as we use 'foto_path'
        unset($validated['foto']);

        $employee = EmployeeData::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil ditambahkan.',
                'data' => $employee
            ]);
        }

        return redirect()->route('hr.employee-data.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = EmployeeData::findOrFail($id);
        return view('hr.employee-data.form', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = EmployeeData::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:tb_employee_data,nip,' . $id,
            'nama_karyawan' => 'required|string|max:255',
            'lp' => 'nullable|string|max:10',
            'lvl' => 'nullable|string|max:50',
            'dept' => 'nullable|string|max:100',
            'bagian' => 'nullable|string|max:100',
            'tgl_masuk' => 'nullable|date',
            'status_update' => 'nullable|string|max:50',
            'tanggal_awal' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'masa_kerja' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'usia' => 'nullable|integer',
            'alamat_ktp' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat_domisili' => 'nullable|string',
            'nomor_kontak_darurat' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($employee->foto_path) {
                Storage::disk('public')->delete($employee->foto_path);
            }
            $foto = $request->file('foto');
            $fotoPath = $foto->store('employee-photos', 'public');
            $validated['foto_path'] = $fotoPath;
        }

        // Calculate usia if tgl_lahir is provided
        if (isset($validated['tgl_lahir']) && !isset($validated['usia'])) {
            $validated['usia'] = Carbon::parse($validated['tgl_lahir'])->age;
        }

        // Calculate masa_kerja if tgl_masuk is provided
        if (isset($validated['tgl_masuk']) && !isset($validated['masa_kerja'])) {
            $masaKerja = Carbon::parse($validated['tgl_masuk'])->diffInYears(Carbon::now());
            $validated['masa_kerja'] = $masaKerja . ' tahun';
        }

        // Remove 'foto' from validated array as we use 'foto_path'
        unset($validated['foto']);

        $employee->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui.',
                'data' => $employee
            ]);
        }

        return redirect()->route('hr.employee-data.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = EmployeeData::findOrFail($id);

        // Delete foto if exists
        if ($employee->foto_path) {
            Storage::disk('public')->delete($employee->foto_path);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil dihapus.'
        ]);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('hr.employee-data.import');
    }

    /**
     * Import employee data from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            $import = new EmployeeDataImport();

            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $skipCount = $import->getSkipCount();
            $errors = $import->getErrors();

            Log::info('Import completed', [
                'success_count' => $successCount,
                'skip_count' => $skipCount,
                'error_count' => count($errors)
            ]);

            $message = "Import berhasil! Data yang diimport: {$successCount}";
            if ($skipCount > 0) {
                $message .= ", Data yang dilewati: {$skipCount}";
            }
            if (count($errors) > 0) {
                $message .= ", Error: " . count($errors);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'success_count' => $successCount,
                        'skip_count' => $skipCount,
                        'errors' => $errors
                    ]
                ]);
            }

            return redirect()->route('hr.employee-data.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            Log::error('Import validation error', [
                'errors' => $errors,
                'exception' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi saat import',
                    'errors' => $errors
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan validasi saat import')
                ->with('import_errors', $errors)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Import Employee Data Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/employee-data-template.xlsx');

        if (!file_exists($templatePath)) {
            // Create template if doesn't exist
            $this->createTemplate($templatePath);
        }

        return response()->download($templatePath, 'template-import-data-karyawan.xlsx');
    }

    /**
     * Create Excel template
     */
    protected function createTemplate($path)
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $headers = [
            'NO',
            'NIP',
            'Nama Karyawan',
            'L/P',
            'LVL',
            'DEPT',
            'BAGIAN',
            'TGL MASUK',
            'STATUS UPDATE',
            'TANGGAL AWAL KONTRAK',
            'TANGGAL BERAKHIR KONTRAK',
            'MASA KERJA',
            'TEMPAT LAHIR',
            'TGL LAHIR',
            'USIA',
            'ALAMAT KTP',
            'Email',
            'No HP',
            'ALAMAT DOMISILI',
            'NOMOR KONTAK DARURAT',
            'AGAMA',
            'PENDIDIKAN TERAKHIR',
            'JURUSAN'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->fromArray([$headers], null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:W1')->applyFromArray($headerStyle);

        // Set column widths
        foreach (range('A', 'W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add example row
        $exampleRow = [
            '1',
            'EMP001',
            'Contoh Nama Karyawan',
            'L',
            'Staff',
            'HRD',
            'Recruitment',
            '2020-01-15',
            'Aktif',
            '2020-01-15',
            '',
            '',
            'Jakarta',
            '1990-05-20',
            '',
            'Jl. Contoh No. 123',
            'email@example.com',
            '081234567890',
            'Jl. Domisili No. 456',
            '081234567891',
            'Islam',
            'S1',
            'Teknik Informatika'
        ];
        $sheet->fromArray([$exampleRow], null, 'A2');

        // Add note
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->setCellValue('A5', '1. NIP dan Nama Karyawan wajib diisi');
        $sheet->setCellValue('A6', '2. Format tanggal: YYYY-MM-DD atau DD/MM/YYYY');
        $sheet->setCellValue('A7', '3. USIA dan MASA KERJA akan dihitung otomatis jika TGL LAHIR dan TGL MASUK diisi');
        $sheet->setCellValue('A8', '4. Jika NIP sudah ada, data akan diupdate');
        $sheet->setCellValue('A9', '5. Hapus baris contoh ini sebelum import');

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);
    }
}

