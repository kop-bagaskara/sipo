<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentEmailNotificationSetting;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DevelopmentEmailNotificationController extends Controller
{
    /**
     * Tampilkan halaman index master setting email development
     */
    public function index()
    {
        $settings = DevelopmentEmailNotificationSetting::with('users')->get();
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        $divisis = Divisi::all();

        return view('admin.development-email-notification-settings.index', compact('settings', 'users', 'divisis'));
    }

    /**
     * Tampilkan form untuk membuat setting baru
     */
    public function create()
    {
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        $divisis = Divisi::all();

        return view('admin.development-email-notification-settings.create', compact('users', 'divisis'));
    }

    /**
     * Simpan setting email baru
     */
    public function store(Request $request)
    {
        // dd($request->all());

        try {
            $setting = DevelopmentEmailNotificationSetting::create([
                'process_name' => $request->process_name,
                'process_code' => $request->process_code,
                'description' => $request->description,
                'recipient_roles' => [], // We'll use users instead
                'reminder_schedule' => $request->reminder_schedule,
                'is_active' => $request->has('is_active'),
            ]);


            return redirect()->route('development-email-notification-settings.index')
                ->with('success', 'Email notification setting berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit
     */
    public function edit($id)
    {
        $setting = DevelopmentEmailNotificationSetting::findOrFail($id);
        $users = User::select('id', 'name', 'divisi', 'jabatan')->get();
        $divisis = Divisi::all();

        return view('admin.development-email-notification-settings.edit', compact('setting', 'users', 'divisis'));
    }

    /**
     * Update setting
     */
    public function update(Request $request, $id)
    {
        $setting = DevelopmentEmailNotificationSetting::findOrFail($id);

        $request->validate([
            'process_name' => 'required|string|max:255',
            'process_code' => 'required|string|unique:tb_development_email_notification_settings,process_code,' . $id,
            'description' => 'nullable|string',
            'recipient_users' => 'required|array|min:1',
            'recipient_users.*' => 'integer|exists:users,id',
            'reminder_schedule' => 'nullable|array',
            'reminder_schedule.*.days' => 'required|integer|min:0|max:30',
            'reminder_schedule.*.description' => 'nullable|string|max:255',
            'reminder_schedule.*.users' => 'nullable|array',
            'reminder_schedule.*.users.*' => 'integer|exists:users,id',
            'is_active' => 'boolean',
            'send_to_rnd_on_every_change' => 'boolean'
        ]);

        try {
            $setting->update([
                'process_name' => $request->process_name,
                'process_code' => $request->process_code,
                'description' => $request->description,
                'recipient_roles' => [], // We'll use users instead
                'reminder_schedule' => $request->reminder_schedule,
                'is_active' => $request->has('is_active'),
                'send_to_rnd_on_every_change' => $request->has('send_to_rnd_on_every_change')
            ]);

            // Sync users to the setting
            if ($request->has('recipient_users')) {
                $setting->users()->sync($request->recipient_users);
            }

            return redirect()->route('development-email-notification-settings.index')
                ->with('success', 'Email notification setting berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus setting
     */
    public function destroy($id)
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::findOrFail($id);
            $setting->delete();

            return redirect()->route('development-email-notification-settings.index')
                ->with('success', 'Email notification setting berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::findOrFail($id);
            $setting->update(['is_active' => !$setting->is_active]);

            $status = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                ->with('success', "Email notification setting berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
