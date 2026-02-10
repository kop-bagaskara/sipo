<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Machine;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman index master setting
     */
    public function index()
    {
        if (request()->ajax()) {
            $settings = Setting::orderBy('master')->get();

            return datatables()->of($settings)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group" role="group">';
                    $actionBtn .= '<button type="button" class="btn btn-warning btn-sm" onclick="editSetting(' . $row->id . ')" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $actionBtn .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteSetting(' . $row->id . ')" title="Hapus"><i class="mdi mdi-delete"></i></button>';
                    $actionBtn .= '</div>';
                    return $actionBtn;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.settings.index');
    }

    /**
     * Tampilkan form untuk membuat setting baru
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Simpan setting baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'master' => 'required|string|max:255|unique:settings,master',
            'value' => 'required|string|max:255'
        ]);

        try {
            Setting::create([
                'master' => $request->master,
                'value' => $request->value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan detail setting
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        return view('admin.settings.show', compact('setting'));
    }

    /**
     * Tampilkan form edit setting
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        return response()->json($setting);
    }

    /**
     * Update setting
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'master' => 'required|string|max:255|unique:settings,master,' . $id,
            'value' => 'required|string|max:255'
        ]);

        try {
            $setting = Setting::findOrFail($id);
            $setting->update([
                'master' => $request->master,
                'value' => $request->value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus setting
     */
    public function destroy($id)
    {
        try {
            $setting = Setting::findOrFail($id);
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active machines setting
     */
    public function getActiveMachines()
    {
        $activeMachineCodes = Setting::getActiveMachines();

        // Get machine details
        $machines = Machine::whereIn('Code', $activeMachineCodes)
            ->get(['Code', 'Description', 'Department'])
            ->toArray();

        return response()->json([
            'success' => true,
            'active_machines' => $machines,
            'active_codes' => $activeMachineCodes
        ]);
    }

    /**
     * Set active machines setting
     */
    public function setActiveMachines(Request $request)
    {
        $request->validate([
            'machine_codes' => 'required|array',
            'machine_codes.*' => 'string'
        ]);

        $machineCodes = $request->input('machine_codes');

        // Verify machines exist
        $existingMachines = Machine::whereIn('Code', $machineCodes)->pluck('Code')->toArray();
        $invalidCodes = array_diff($machineCodes, $existingMachines);

        if (!empty($invalidCodes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid machine codes: ' . implode(', ', $invalidCodes)
            ], 400);
        }

        Setting::setActiveMachines($machineCodes);

        return response()->json([
            'success' => true,
            'message' => 'Active machines setting updated successfully'
        ]);
    }

    /**
     * Get all machines for selection
     */
    public function getAllMachines()
    {
        $machines = Machine::where('Description', 'not like', '%JANGAN DIPAKAI%')
            ->orderBy('Department')
            ->orderBy('Description')
            ->get(['Code', 'Description', 'Department'])
            ->toArray();

        return response()->json([
            'success' => true,
            'machines' => $machines
        ]);
    }

    /**
     * Toggle machine active status
     */
    public function toggleMachineActive(Request $request)
    {
        $request->validate([
            'machine_code' => 'required|string'
        ]);

        $machineCode = $request->input('machine_code');
        $activeMachineCodes = Setting::getActiveMachines();

        if (in_array($machineCode, $activeMachineCodes)) {
            // Remove from active
            $activeMachineCodes = array_diff($activeMachineCodes, [$machineCode]);
        } else {
            // Add to active
            $activeMachineCodes[] = $machineCode;
        }

        Setting::setActiveMachines($activeMachineCodes);

        return response()->json([
            'success' => true,
            'message' => 'Machine status updated successfully',
            'is_active' => in_array($machineCode, $activeMachineCodes)
        ]);
    }
}
