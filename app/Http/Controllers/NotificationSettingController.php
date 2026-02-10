<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationSettingController extends Controller
{
    /**
     * Tampilkan halaman index pengaturan notifikasi
     */
    public function index()
    {
        $settings = NotificationSetting::orderBy('notification_type')
            ->orderBy('target_type')
            ->get();
            
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.notification-settings.index', compact('settings', 'users'));
    }

    /**
     * Tampilkan form untuk membuat pengaturan baru
     */
    public function create()
    {
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.notification-settings.create', compact('users'));
    }

    /**
     * Simpan pengaturan notifikasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'notification_type' => 'required|string',
            'target_type' => 'required|in:divisi,jabatan,specific_user',
            'target_value' => 'required|string',
            'send_email' => 'boolean',
            'send_website' => 'boolean',
            'description' => 'nullable|string'
        ]);

        try {
            NotificationSetting::create([
                'notification_type' => $request->notification_type,
                'target_type' => $request->target_type,
                'target_value' => $request->target_value,
                'send_email' => $request->has('send_email'),
                'send_website' => $request->has('send_website'),
                'description' => $request->description,
                'is_active' => true
            ]);

            return redirect()->route('notification-settings.index')
                ->with('success', 'Pengaturan notifikasi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail pengaturan notifikasi
     */
    public function show($id)
    {
        $setting = NotificationSetting::findOrFail($id);
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.notification-settings.show', compact('setting', 'users'));
    }

    /**
     * Tampilkan form edit pengaturan
     */
    public function edit($id)
    {
        $setting = NotificationSetting::findOrFail($id);
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.notification-settings.edit', compact('setting', 'users'));
    }

    /**
     * Update pengaturan notifikasi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'notification_type' => 'required|string',
            'target_type' => 'required|in:divisi,jabatan,specific_user',
            'target_value' => 'required|string',
            'send_email' => 'boolean',
            'send_website' => 'boolean',
            'description' => 'nullable|string'
        ]);

        try {
            $setting = NotificationSetting::findOrFail($id);
            $setting->update([
                'notification_type' => $request->notification_type,
                'target_type' => $request->target_type,
                'target_value' => $request->target_value,
                'send_email' => $request->has('send_email'),
                'send_website' => $request->has('send_website'),
                'description' => $request->description
            ]);

            return redirect()->route('notification-settings.index')
                ->with('success', 'Pengaturan notifikasi berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif/tidak aktif
     */
    public function toggleStatus($id)
    {
        try {
            $setting = NotificationSetting::findOrFail($id);
            $setting->update(['is_active' => !$setting->is_active]);
            
            $status = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "Pengaturan berhasil {$status}",
                'is_active' => $setting->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus pengaturan notifikasi
     */
    public function destroy($id)
    {
        try {
            $setting = NotificationSetting::findOrFail($id);
            $setting->delete();
            
            return redirect()->route('notification-settings.index')
                ->with('success', 'Pengaturan notifikasi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan data untuk dropdown target value berdasarkan target type
     */
    public function getTargetValues(Request $request)
    {
        $targetType = $request->input('target_type');
        
        switch ($targetType) {
            case 'divisi':
                $values = User::with('divisiUser')->pluck('divisiUser.divisi')->filter()->values();
                break;
                
            case 'jabatan':
                $values = User::distinct()->pluck('jabatan')->filter()->values();
                break;
                
            case 'specific_user':
                $values = User::select('id', 'name', 'divisi', 'jabatan')
                    ->get()
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'text' => "{$user->name} ({$user->divisi} - {$user->jabatan})"
                        ];
                    });
                break;
                
            default:
                $values = collect();
        }
        
        return response()->json($values);
    }
}
