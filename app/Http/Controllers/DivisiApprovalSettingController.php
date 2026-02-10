<?php

namespace App\Http\Controllers;

use App\Models\DivisiApprovalSetting;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;

class DivisiApprovalSettingController extends Controller
{
    /**
     * Display all division approval settings with accordion UI
     */
    public function index()
    {
        $divisions = Divisi::with('approvalSetting')
            ->orderBy('divisi')
            ->get();

        return view('hr.approval-settings.division-index', compact('divisions'));
    }

    /**
     * Update or create approval setting for a division
     */
    public function update(Request $request, $divisiId)
    {
        $request->validate([
            'spv_enabled' => 'nullable|boolean',
            'head_enabled' => 'nullable|boolean',
            'manager_enabled' => 'nullable|boolean',
        ]);

        $divisi = Divisi::findOrFail($divisiId);

        // Prepare data - simpan boolean saja
        $data = [
            'divisi_id' => $divisiId,
            'is_active' => true,
            'spv_enabled' => $request->has('spv_enabled') && $request->spv_enabled,
            'head_enabled' => $request->has('head_enabled') && $request->head_enabled,
            'manager_enabled' => $request->has('manager_enabled') && $request->manager_enabled,
        ];

        // Update or create
        DivisiApprovalSetting::updateOrCreate(
            ['divisi_id' => $divisiId],
            $data
        );

        return redirect()
            ->route('hr.approval-settings.divisions.index')
            ->with('success', "Setting approval untuk divisi {$divisi->divisi} berhasil disimpan.");
    }

    /**
     * Get users by division for AJAX dropdown
     */
    public function getUsersByDivision($divisiId)
    {
        $users = User::where('divisi', $divisiId)
            ->orderBy('name')
            ->get(['id', 'name', 'jabatan']);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Preview approval chain for a division
     */
    /**
     * Preview approval chain for a division
     */
    public function previewChain(Request $request, $divisiId)
    {
        $setting = DivisiApprovalSetting::where('divisi_id', $divisiId)->first();

        // Jika belum ada setting, buat dummy untuk preview
        if (!$setting) {
            $setting = new DivisiApprovalSetting();
            $setting->divisi_id = $divisiId;
            $setting->spv_enabled = false;
            $setting->head_enabled = false;
            $setting->manager_enabled = false;
        }

        // Override dengan request parameter untuk preview real-time
        if ($request->has('spv_enabled')) {
            $setting->spv_enabled = filter_var($request->spv_enabled, FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('head_enabled')) {
            $setting->head_enabled = filter_var($request->head_enabled, FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('manager_enabled')) {
            $setting->manager_enabled = filter_var($request->manager_enabled, FILTER_VALIDATE_BOOLEAN);
        }

        $divisi = Divisi::find($divisiId);
        $chain = $setting->chain;
        $levels = $setting->approval_levels;

        return response()->json([
            'success' => true,
            'data' => [
                'divisi' => $divisi,
                'chain' => $chain,
                'levels' => $levels,
                'flow_preview' => $this->buildFlowPreview($chain),
            ]
        ]);
    }

    /**
     * Build human-readable flow preview
     */
    private function buildFlowPreview($chain)
    {
        $flow = [];
        foreach ($chain as $level => $data) {
            $flow[] = [
                'level' => strtoupper($level),
                'name' => $data['level_name'],
                'user' => isset($data['user']) && $data['user'] ? $data['user']->name : 'Belum ditentukan',
            ];
        }
        return collect($flow)->pluck('user')->join(' → ');
    }
}
