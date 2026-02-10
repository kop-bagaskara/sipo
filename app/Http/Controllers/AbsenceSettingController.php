<?php

namespace App\Http\Controllers;

use App\Models\MasterAbsenceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class AbsenceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterAbsenceSetting::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '<a href="' . route('hr.absence-settings.edit', $row->id) . '" class="btn btn-sm btn-info"><i class="mdi mdi-pencil"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })
                ->editColumn('is_active', function($row) {
                    if ($row->is_active) {
                        return '<span class="badge bg-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-danger">Tidak Aktif</span>';
                    }
                })
                ->editColumn('attachment_required', function($row) {
                    if ($row->attachment_required) {
                        return '<span class="badge bg-primary">Wajib</span>';
                    } else {
                        return '<span class="badge bg-secondary">Tidak</span>';
                    }
                })
                ->editColumn('min_deadline_days', function($row) {
                    if ($row->min_deadline_days === null) {
                        return '<span class="text-muted">Unlimited</span>';
                    } elseif ($row->min_deadline_days < 0) {
                        return '<span class="text-info">H' . ($row->min_deadline_days) . '</span>';
                    } elseif ($row->min_deadline_days == 0) {
                        return '<span class="text-success">Hari Ini</span>';
                    } else {
                        return '<span class="text-warning">H+' . $row->min_deadline_days . '</span>';
                    }
                })
                ->editColumn('max_deadline_days', function($row) {
                    if ($row->max_deadline_days === null) {
                        return '<span class="text-muted">Unlimited</span>';
                    } elseif ($row->max_deadline_days < 0) {
                        return '<span class="text-info">H' . ($row->max_deadline_days) . '</span>';
                    } else {
                        return '<span class="text-warning">H+' . $row->max_deadline_days . '</span>';
                    }
                })
                ->rawColumns(['action', 'is_active', 'attachment_required', 'min_deadline_days', 'max_deadline_days'])
                ->make(true);
        }

        return view('hr.absence-settings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr.absence-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== STORE REQUEST ===');
        \Log::info('master_sub_absence:', [$request->master_sub_absence]);
        \Log::info('All request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'absence_type' => 'required',
            'min_deadline_days' => 'nullable|integer',
            'max_deadline_days' => 'nullable|integer',
            'attachment_required' => 'boolean',
            'deadline_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'master_sub_absence' => 'nullable|array',
            'master_sub_absence.*.name' => 'required|string|max:255',
            'master_sub_absence.*.duration_days' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        MasterAbsenceSetting::create([
            'absence_type' => $request->absence_type,
            'min_deadline_days' => $request->min_deadline_days ?? 0,
            'max_deadline_days' => $request->max_deadline_days,
            'attachment_required' => $request->boolean('attachment_required', false),
            'deadline_text' => $request->deadline_text,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'master_sub_absence' => $request->master_sub_absence,
        ]);

        \Log::info('SAVED master_sub_absence:', [$request->master_sub_absence]);

        return response()->json([
            'success' => true,
            'message' => 'Setting absence berhasil ditambahkan'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $setting = MasterAbsenceSetting::findOrFail($id);

        // Jika request AJAX, return JSON
        if (request()->ajax()) {
            return response()->json($setting->toArray());
        }

        return view('hr.absence-settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        \Log::info('=== UPDATE REQUEST ===');
        \Log::info('PHP max_input_vars: ' . ini_get('max_input_vars'));
        \Log::info('Request count: ' . count($request->all()));
        \Log::info('master_sub_absence received:', [$request->master_sub_absence]);

        // dd($request->all());
        $setting = MasterAbsenceSetting::findOrFail($id);

        $validator = Validator::make($request->all(), [
            // 'absence_type' => 'required|string|max:50|unique:tb_master_absence_settings,absence_type,' . $id,
            'min_deadline_days' => 'nullable|integer',
            'max_deadline_days' => 'nullable|integer',
            'attachment_required' => 'boolean',
            'deadline_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            // 'master_sub_absence' => 'nullable|array',
            // 'master_sub_absence.*.name' => 'required|string|max:255',
            // 'master_sub_absence.*.duration_days' => 'required|integer|min:1',
        ]);

        // dd($validator);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // dd('1');

        $setting->update([
            'absence_type' => $request->absence_type,
            'min_deadline_days' => $request->min_deadline_days ?? 0,
            'max_deadline_days' => $request->max_deadline_days,
            'attachment_required' => $request->boolean('attachment_required', false),
            'deadline_text' => $request->deadline_text,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'master_sub_absence' => $request->master_sub_absence,
        ]);

        \Log::info('SAVED master_sub_absence:', [$setting->master_sub_absence]);

        return response()->json([
            'success' => true,
            'message' => 'Setting absence berhasil diupdate'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = MasterAbsenceSetting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting absence berhasil dihapus'
        ]);
    }
}
