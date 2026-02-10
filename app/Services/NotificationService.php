<?php

namespace App\Services;

use App\Models\JobPrepress;
use App\Models\User;
use App\Models\Notification;
use App\Models\EmailNotificationSetting;
use App\Mail\JobOrderPrepressNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Kirim notifikasi untuk job order prepress baru
     */
    public function sendJobOrderPrepressNotification(JobPrepress $jobOrder)
    {
        try {
            // Website notification for all users
            $allUsers = User::all();
            foreach ($allUsers as $user) {
                $this->createWebsiteNotification($user, $jobOrder);
            }

            // Email notification based on master setting
            $emailSetting = EmailNotificationSetting::where('notification_type', 'job_order_prepress')
                                                    ->where('is_active', true)
                                                    ->first();

            // dd($emailSetting);

            if ($emailSetting) {
                $usersToEmail = $emailSetting->getActiveUsers(); // Get users linked to this setting
                Log::info('Email notification setting found', [
                    'setting_id' => $emailSetting->id,
                    'users_count' => $usersToEmail->count()
                ]);

                foreach ($usersToEmail as $user) {
                    if ($user->email) {
                        Log::info('Sending email to user', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'job_order_id' => $jobOrder->id
                        ]);

                        $this->sendEmailNotification($user, $jobOrder);
                    } else {
                        Log::warning('User has no email', ['user_id' => $user->id, 'user_name' => $user->name]);
                    }
                }
            } else {
                Log::warning('No email notification setting found for job_order_prepress');
            }

            Log::info('Job order notification sent successfully', [
                'job_order_id' => $jobOrder->id,
                'website_notifications' => $allUsers->count(),
                'email_setting_found' => $emailSetting ? true : false
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send job order notification', [
                'job_order_id' => $jobOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Buat notifikasi website
     */
    private function createWebsiteNotification(User $user, JobPrepress $jobOrder)
    {
        $notification = new Notification();
        $notification->id = Str::uuid();
        $notification->type = 'App\Notifications\JobOrderPrepressNotification';
        $notification->notifiable_type = User::class;
        $notification->notifiable_id = $user->id;
        $notification->data = [
            'id' => $jobOrder->id,
            'title' => 'Job Order Prepress Baru',
            'message' => "Job order baru: {$jobOrder->kode_design} - {$jobOrder->product}",
            'job_order_id' => $jobOrder->id,
            'kode_design' => $jobOrder->kode_design,
            'product' => $jobOrder->product,
            'customer' => $jobOrder->customer,
            'priority' => $jobOrder->prioritas_job,
            'created_at' => now()->toISOString(),
            'url' => route('prepress.job-order.detail', $jobOrder->id)
        ];
        $notification->save();
    }

    /**
     * Kirim notifikasi email
     */
    private function sendEmailNotification(User $user, JobPrepress $jobOrder)
    {
        try {
            Mail::to($user->email)->send(new JobOrderPrepressNotification($jobOrder, $user));
        } catch (\Exception $e) {
            // Log error jika email gagal dikirim
            \Illuminate\Support\Facades\Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan notifikasi yang belum dibaca untuk user
     */
    public function getUnreadNotifications(User $user, int $limit = 10)
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Dapatkan jumlah notifikasi yang belum dibaca untuk user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     */
    public function markAllAsRead(User $user)
    {
        Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }



    /**
     * Hapus notifikasi lama (lebih dari 30 hari)
     */
    public function cleanOldNotifications()
    {
        $thirtyDaysAgo = now()->subDays(30);

        Notification::where('created_at', '<', $thirtyDaysAgo)
            ->delete();
    }
}
