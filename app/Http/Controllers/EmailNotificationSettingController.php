<?php

namespace App\Http\Controllers;

use App\Models\EmailNotificationSetting;
use App\Models\User;
use Illuminate\Http\Request;

class EmailNotificationSettingController extends Controller
{
    /**
     * Tampilkan halaman index master setting email
     */
    public function index()
    {
        $settings = EmailNotificationSetting::with('users')->get();
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.email-notification-settings.index', compact('settings', 'users'));
    }

    /**
     * Tampilkan form untuk membuat setting baru
     */
    public function create()
    {
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.email-notification-settings.create', compact('users'));
    }

    /**
     * Simpan setting email baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'notification_name' => 'required|string|max:255',
            'notification_type' => 'required|string|unique:email_notification_settings,notification_type',
            'description' => 'nullable|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $setting = EmailNotificationSetting::create([
                'notification_name' => $request->notification_name,
                'notification_type' => $request->notification_type,
                'description' => $request->description,
                'is_active' => true
            ]);

            // Attach users dengan pivot data
            $userData = [];
            foreach ($request->user_ids as $userId) {
                $userData[$userId] = ['is_active' => true];
            }
            $setting->users()->attach($userData);

            return redirect()->route('email-notification-settings.index')
                ->with('success', 'Setting email berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan setting: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail setting
     */
    public function show($id)
    {
        $setting = EmailNotificationSetting::with('users')->findOrFail($id);
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.email-notification-settings.show', compact('setting', 'users'));
    }

    /**
     * Tampilkan form edit
     */
    public function edit($id)
    {
        $setting = EmailNotificationSetting::with('users')->findOrFail($id);
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        
        return view('admin.email-notification-settings.edit', compact('setting', 'users'));
    }

    /**
     * Update setting
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'notification_name' => 'required|string|max:255',
            'notification_type' => 'required|string|unique:email_notification_settings,notification_type,' . $id,
            'description' => 'nullable|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $setting = EmailNotificationSetting::findOrFail($id);
            $setting->update([
                'notification_name' => $request->notification_name,
                'notification_type' => $request->notification_type,
                'description' => $request->description
            ]);

            // Sync users (hapus yang lama, tambah yang baru)
            $userData = [];
            foreach ($request->user_ids as $userId) {
                $userData[$userId] = ['is_active' => true];
            }
            $setting->users()->sync($userData);

            return redirect()->route('email-notification-settings.index')
                ->with('success', 'Setting email berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate setting: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif/tidak aktif
     */
    public function toggleStatus($id)
    {
        try {
            $setting = EmailNotificationSetting::findOrFail($id);
            $setting->update(['is_active' => !$setting->is_active]);
            
            $status = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "Setting berhasil {$status}",
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
     * Hapus setting
     */
    public function destroy($id)
    {
        try {
            $setting = EmailNotificationSetting::findOrFail($id);
            $setting->delete();
            
            return redirect()->route('email-notification-settings.index')
                ->with('success', 'Setting email berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus setting: ' . $e->getMessage());
        }
    }
}
