<?php

namespace App\Http\Controllers;

use App\Models\MenuNavigationSetting;
use App\Models\Divisi;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuNavigationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = MenuNavigationSetting::ordered()->get();
        $divisis = Divisi::orderBy('divisi')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();

        return view('master.menu-navigation-settings.index', compact('settings', 'divisis', 'jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisis = Divisi::orderBy('divisi')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();

        return view('master.menu-navigation-settings.create', compact('divisis', 'jabatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_key' => 'required|string|unique:tb_menu_navigation_settings,menu_key|max:255',
            'menu_name' => 'required|string|max:255',
            'menu_icon' => 'nullable|string|max:255',
            'menu_route' => 'nullable|string|max:255',
            'allowed_divisi' => 'nullable|array',
            'allowed_divisi.*' => 'integer|exists:tb_divisis,id',
            'allowed_jabatan' => 'nullable|array',
            'allowed_jabatan.*' => 'integer|exists:tb_jabatans,id',
            'excluded_divisi' => 'nullable|array',
            'excluded_divisi.*' => 'integer|exists:tb_divisis,id',
            'excluded_jabatan' => 'nullable|array',
            'excluded_jabatan.*' => 'integer|exists:tb_jabatans,id',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        MenuNavigationSetting::create($validated);

        return redirect()->route('master.menu-navigation-settings.index')
            ->with('success', 'Menu navigation setting berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuNavigationSetting $menuNavigationSetting)
    {
        $divisis = Divisi::orderBy('divisi')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();

        return view('master.menu-navigation-settings.show', compact('menuNavigationSetting', 'divisis', 'jabatans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuNavigationSetting $menuNavigationSetting)
    {
        $divisis = Divisi::orderBy('divisi')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();

        return view('master.menu-navigation-settings.edit', compact('menuNavigationSetting', 'divisis', 'jabatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuNavigationSetting $menuNavigationSetting)
    {
        $validated = $request->validate([
            'menu_key' => 'required|string|max:255|unique:tb_menu_navigation_settings,menu_key,' . $menuNavigationSetting->id,
            'menu_name' => 'required|string|max:255',
            'menu_icon' => 'nullable|string|max:255',
            'menu_route' => 'nullable|string|max:255',
            'allowed_divisi' => 'nullable|array',
            'allowed_divisi.*' => 'integer|exists:tb_divisis,id',
            'allowed_jabatan' => 'nullable|array',
            'allowed_jabatan.*' => 'integer|exists:tb_jabatans,id',
            'excluded_divisi' => 'nullable|array',
            'excluded_divisi.*' => 'integer|exists:tb_divisis,id',
            'excluded_jabatan' => 'nullable|array',
            'excluded_jabatan.*' => 'integer|exists:tb_jabatans,id',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $menuNavigationSetting->update($validated);

        return redirect()->route('master.menu-navigation-settings.index')
            ->with('success', 'Menu navigation setting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuNavigationSetting $menuNavigationSetting)
    {
        $menuNavigationSetting->delete();

        return redirect()->route('master.menu-navigation-settings.index')
            ->with('success', 'Menu navigation setting berhasil dihapus.');
    }
}

