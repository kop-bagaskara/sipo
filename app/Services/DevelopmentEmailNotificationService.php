<?php

namespace App\Services;

use App\Models\DevelopmentEmailNotificationSetting;
use App\Models\User;
use App\Mail\DevelopmentNotificationMail;
use App\Mail\DevelopmentPrepressNotificationMail;
use App\Mail\PicPrepressReminderMail;
use App\Mail\FinishPrepressNotificationMail;
use App\Mail\ProsesProduksiNotificationMail;
use App\Mail\JobDeadlineFulltimeNotificationMail;
use App\Mail\ProgressJobNotificationMail;
use App\Mail\ProgressJobStructuredNotificationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DevelopmentEmailNotificationService
{
    /**
     * Kirim notifikasi email untuk proses development
     */
    public function sendNotification($processCode, $jobData, $additionalData = [])
    {
        try {
            // Cari setting notifikasi berdasarkan process_code
            $setting = DevelopmentEmailNotificationSetting::where('process_code', $processCode)
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: {$processCode}");
                return false;
            }

            // Ambil reminder schedule yang sesuai
            $reminderSchedule = $setting->reminder_schedule ?? [];

            if (empty($reminderSchedule)) {
                Log::info("No reminder schedule found for process_code: {$processCode}");
                return false;
            }

            // Kirim email untuk setiap reminder yang dikonfigurasi
            foreach ($reminderSchedule as $reminder) {
                $this->sendReminderEmail($setting, $reminder, $jobData, $additionalData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending development email notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim email reminder untuk satu jadwal
     */
    private function sendReminderEmail($setting, $reminder, $jobData, $additionalData)
    {
        try {
            $users = $reminder['users'] ?? [];

            if (empty($users)) {
                Log::info("No users configured for reminder in process_code: {$setting->process_code}");
                return;
            }

            // Ambil data user
            $recipients = User::whereIn('id', $users)->get();

            if ($recipients->isEmpty()) {
                Log::info("No valid users found for reminder in process_code: {$setting->process_code}");
                return;
            }

            // Pastikan action_url tersedia
            if (!isset($additionalData['action_url'])) {
                $additionalData['action_url'] = route('development.marketing-jobs.list');
            }

            // Siapkan data untuk email
            $emailData = [
                'setting' => $setting,
                'reminder' => $reminder,
                'jobData' => $jobData,
                'additionalData' => $additionalData,
                'recipients' => $recipients
            ];

            // Kirim email ke setiap user
            foreach ($recipients as $user) {
                try {
                    Mail::to($user->email)->send(new DevelopmentNotificationMail($emailData));
                    Log::info("Development notification email sent to: {$user->email} for process: {$setting->process_code}");
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("Error sending reminder email: " . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi untuk input awal development
     */
    public function sendInputAwalNotification($jobData)
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'input_awal')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: input_awal");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            // Cari reminder dengan days = 'first'
            $targetReminder = null;
            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === 'first') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for input_awal");
                return false;
            }

            // Siapkan data production schedules untuk template terstruktur (kosong untuk job baru)
            $productionSchedules = [];

            // Gabungkan data production schedules ke jobData
            $structuredJobData = array_merge($jobData, [
                'production_schedules' => $productionSchedules,
                'status_after' => 'INPUT_AWAL',
                'status_after_desc' => 'Input Awal',
                'progress_percentage' => 0,
                'job_deadline' => $jobData['job_deadline'] ? Carbon::parse($jobData['job_deadline'])->format('d/m/Y') : '-',
                'days_left' => $jobData['job_deadline'] ? Carbon::now()->diffInDays(Carbon::parse($jobData['job_deadline']), false) : 0
            ]);

            $emailAdditionalData = [
                'notification_type' => 'input_awal_structured',
                'action_text' => 'Lihat Detail Job'
            ];

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new ProgressJobStructuredNotificationMail($setting, $targetReminder, $structuredJobData, $emailAdditionalData, $recipient));
                    Log::info("Input awal structured notification email sent to: {$recipient->email} for job {$jobData['job_code']}");
                } catch (\Exception $e) {
                    Log::error("Failed to send input awal structured notification to user {$userId} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending input awal structured notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi ketika job dikirim ke assign_prepress
     */
    public function sendPrepressNotification($jobData)
    {
        // dd($jobData);
        try {
            // Cari setting notifikasi untuk assign_prepress
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'assign_prepress')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: assign_prepress");
                return false;
            }

            // Ambil reminder pertama (first)
            $reminderSchedule = $setting->reminder_schedule ?? [];
            $firstReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === 'first') {
                    $firstReminder = $reminder;
                    break;
                }
            }

            if (!$firstReminder) {
                Log::info("No first reminder found for assign_prepress process");
                return false;
            }

            // Ambil recipients dari reminder
            $recipients = [];
            if (isset($firstReminder['users']) && is_array($firstReminder['users'])) {
                $recipients = User::whereIn('id', $firstReminder['users'])->get();
            }

            if ($recipients->isEmpty()) {
                Log::info("No recipients found for assign_prepress notification");
                return false;
            }

            $additionalData = [
                'notification_type' => 'assign_prepress',
                'action_text' => 'Lihat Job Prepress'
            ];

            // Kirim email ke setiap recipient
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new DevelopmentPrepressNotificationMail(
                        $setting,
                        $firstReminder,
                        $jobData,
                        $additionalData,
                        $recipient
                    ));
                    Log::info("Prepress notification email sent to: {$recipient->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send assign_prepress notification to {$recipient->email}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending assign_prepress notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim reminder untuk job assign_prepress (H-2 atau H-1)
     */
    public function sendPrepressReminderNotification($jobData, $reminderType = 'H-2')
    {
        try {
            // Cari setting notifikasi untuk assign_prepress
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'assign_prepress')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: assign_prepress");
                return false;
            }

            // Cari reminder yang sesuai berdasarkan type
            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminderType === 'H-2' && $reminder['days'] === '2') {
                    $targetReminder = $reminder;
                    break;
                } elseif ($reminderType === 'H-1' && $reminder['days'] === '1') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType}");
                return false;
            }

            $additionalData = [
                'notification_type' => 'assign_prepress_reminder',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Job Prepress'
            ];

            // Ambil recipients dari reminder
            $recipients = [];
            if (isset($targetReminder['users']) && is_array($targetReminder['users'])) {
                $recipients = User::whereIn('id', $targetReminder['users'])->get();
            }

            // dd($recipients);

            if ($recipients->isEmpty()) {
                Log::info("No recipients found for assign_prepress reminder: {$reminderType}");
                return false;
            }

            // Kirim email ke setiap recipient
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new DevelopmentPrepressNotificationMail(
                        $setting,
                        $targetReminder,
                        $jobData,
                        $additionalData,
                        $recipient
                    ));
                    Log::info("Prepress reminder email sent to: {$recipient->email} for {$reminderType}");
                } catch (\Exception $e) {
                    Log::error("Failed to send assign_prepress reminder to {$recipient->email}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending assign_prepress reminder notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi reminder prepress untuk multiple jobs dalam satu email
     */
    public function sendPrepressReminderNotificationMultiple($jobsData, $reminderType = 'H-2')
    {
        try {
            // Cari setting notifikasi untuk assign_prepress
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'assign_prepress')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: assign_prepress");
                return false;
            }

            // Cari reminder yang sesuai berdasarkan type
            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminderType === 'H-2' && $reminder['days'] === '2') {
                    $targetReminder = $reminder;
                    break;
                } elseif ($reminderType === 'H-1' && $reminder['days'] === '1') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType}");
                return false;
            }

            $additionalData = [
                'notification_type' => 'assign_prepress_reminder',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Job Prepress'
            ];

            // Ambil recipients dari reminder
            $recipients = [];
            if (isset($targetReminder['users']) && is_array($targetReminder['users'])) {
                $recipients = User::whereIn('id', $targetReminder['users'])->get();
            }

            if ($recipients->isEmpty()) {
                Log::info("No recipients found for assign_prepress reminder: {$reminderType}");
                return false;
            }

            // Kirim email ke setiap recipient dengan multiple jobs
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new DevelopmentPrepressNotificationMail(
                        $setting,
                        $targetReminder,
                        $jobsData[0], // First job for backward compatibility
                        $additionalData,
                        $recipient,
                        $jobsData // Pass multiple jobs
                    ));
                    Log::info("Prepress reminder email sent to: {$recipient->email} for {$reminderType} with " . count($jobsData) . " jobs");
                } catch (\Exception $e) {
                    Log::error("Failed to send assign_prepress reminder to {$recipient->email}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending assign_prepress reminder notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim reminder untuk PIC Prepress (H-3, H-2, H-1)
     */
    public function sendPicPrepressReminderNotification($jobData, $reminderType = 'H-3')
    {
        try {
            // Cari setting notifikasi untuk PIC Prepress
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'pic_prepress_reminder')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: pic_prepress_reminder");
                return false;
            }

            // Cari reminder yang sesuai berdasarkan type
            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminderType === 'H-3' && $reminder['days'] === '3') {
                    $targetReminder = $reminder;
                    break;
                } elseif ($reminderType === 'H-2' && $reminder['days'] === '2') {
                    $targetReminder = $reminder;
                    break;
                } elseif ($reminderType === 'H-1' && $reminder['days'] === '1') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType}");
                return false;
            }

            $additionalData = [
                'notification_type' => 'pic_prepress_reminder',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Job Prepress'
            ];

            // Ambil PIC yang bertanggung jawab untuk job ini dari tb_assign_job_prepresses
            $assignedPics = DB::table('tb_assign_job_prepresses')
                ->join('users', 'tb_assign_job_prepresses.id_user_pic', '=', 'users.id')
                ->where('tb_assign_job_prepresses.id_job_order', $jobData['id'])
                // ->where('users.divisi', '3') // Pastikan hanya PIC Prepress
                ->select('users.*')
                ->get();

            // dd($jobData);

            if ($assignedPics->isEmpty()) {
                Log::info("No assigned PIC found for job: {$jobData['job_code']}");
                return false;
            }

            // Kirim email ke PIC yang bertanggung jawab untuk job ini
            foreach ($assignedPics as $recipientData) {
                try {
                    // Convert stdClass ke User model
                    $recipient = User::find($recipientData->id);

                    if (!$recipient) {
                        Log::warning("User not found with ID: {$recipientData->id}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new PicPrepressReminderMail(
                        $setting,
                        $targetReminder,
                        $jobData,
                        $additionalData,
                        $recipient
                    ));
                    Log::info("PIC Prepress reminder email sent to: {$recipient->email} for job {$jobData['job_code']} ({$reminderType})");
                } catch (\Exception $e) {
                    Log::error("Failed to send PIC Prepress reminder to {$recipientData->email} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending PIC Prepress reminder notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi ketika job prepress selesai
     */
    public function sendFinishPrepressNotification($jobData)
    {
        try {
            Log::info("DevelopmentEmailNotificationService::sendFinishPrepressNotification called for job: {$jobData['job_code']}");
            
            // Cari setting notifikasi untuk finish prepress
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'finish_prepress')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                Log::info("No active email notification setting found for process_code: finish_prepress");
                return false;
            }

            // Ambil reminder pertama (first)
            $reminderSchedule = $setting->reminder_schedule ?? [];
            $firstReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === 'first') {
                    $firstReminder = $reminder;
                    break;
                }
            }

            if (!$firstReminder) {
                Log::info("No first reminder found for finish_prepress");
                return false;
            }

            $additionalData = [
                'notification_type' => 'finish_prepress',
                'action_text' => 'Lihat Job Prepress'
            ];

            // Ambil user yang akan dikirimi email (Marketing dan Prepress SPV)
            $recipientIds = $firstReminder['users'] ?? [];
            $recipients = User::whereIn('id', $recipientIds)->get();

            if ($recipients->isEmpty()) {
                Log::info("No recipients found for finish_prepress notification");
                return false;
            }

            // Kirim email ke setiap recipient
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new FinishPrepressNotificationMail(
                        $setting,
                        $firstReminder,
                        $jobData,
                        $additionalData,
                        $recipient
                    ));
                    Log::info("Finish prepress notification email sent to: {$recipient->email} for job {$jobData['job_code']}");
                } catch (\Exception $e) {
                    Log::error("Failed to send finish prepress notification to {$recipient->email}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending finish prepress notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi untuk proses produksi berdasarkan lead time configuration
     */
    public function sendProsesProduksiNotification($jobData, $reminderType = 'first')
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'proses_produksi')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: proses_produksi");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === $reminderType) {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType}");
                return false;
            }

            $additionalData = [
                'notification_type' => 'proses_produksi',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Detail Job'
            ];

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new ProsesProduksiNotificationMail($setting, $targetReminder, $jobData, $additionalData, $recipient));
                    Log::info("Proses produksi notification email sent to: {$recipient->email} for job {$jobData['job_code']} ({$reminderType})");
                } catch (\Exception $e) {
                    Log::error("Failed to send proses produksi notification to user {$userId} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending proses produksi notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi reminder proses produksi untuk multiple jobs dalam satu email (sama seperti prepress)
     */
    public function sendProsesProduksiReminderNotificationMultiple($jobsData, $reminderType = 'H-4')
    {
        try {
            // Cari setting notifikasi untuk proses_produksi
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'proses_produksi')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: proses_produksi");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            // Cari reminder yang sesuai berdasarkan days
            $daysMapping = [
                'H-4' => '4',
                'H-2' => '2', 
                'H' => '0'
            ];

            $targetDays = $daysMapping[$reminderType] ?? '4';

            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === $targetDays) {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType} (days: {$targetDays})");
                return false;
            }

            $additionalData = [
                'notification_type' => 'proses_produksi_reminder',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Job Development',
                'action_url' => '#'
            ];

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    // Gunakan template yang sama seperti prepress reminder
                    Mail::to($recipient->email)->send(new DevelopmentPrepressNotificationMail(
                        $setting,
                        $targetReminder,
                        $jobsData,
                        $additionalData,
                        $recipient,
                        $jobsData // Pass jobs as separate parameter
                    ));
                    Log::info("Proses produksi reminder email sent to: {$recipient->email} for {$reminderType}");
                } catch (\Exception $e) {
                    Log::error("Failed to send proses produksi reminder to user {$userId}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending proses produksi reminder notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi untuk job deadline fulltime
     */
    public function sendJobDeadlineFulltimeNotification($jobData, $reminderType = '10')
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'job_deadline_fulltime')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: job_deadline_fulltime");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            $targetReminder = null;

            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === $reminderType) {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for type: {$reminderType}");
                return false;
            }

            $additionalData = [
                'notification_type' => 'job_deadline_fulltime',
                'reminder_type' => $reminderType,
                'action_text' => 'Lihat Detail Job'
            ];

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new JobDeadlineFulltimeNotificationMail($setting, $targetReminder, $jobData, $additionalData, $recipient));
                    Log::info("Job deadline fulltime notification email sent to: {$recipient->email} for job {$jobData['job_code']} ({$reminderType})");
                } catch (\Exception $e) {
                    Log::error("Failed to send job deadline fulltime notification to user {$userId} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending job deadline fulltime notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi untuk progress job
     */
    public function sendProgressJobNotification($jobData, $additionalData = [])
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'progress_job')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: progress_job");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            // Cari reminder dengan days = 'first'
            $targetReminder = null;
            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === 'first') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for progress_job");
                return false;
            }

            // Siapkan data production schedules untuk template terstruktur
            $productionSchedules = [];
            if (isset($jobData['production_schedules']) && is_array($jobData['production_schedules'])) {
                foreach ($jobData['production_schedules'] as $index => $schedule) {
                    $productionSchedules[] = [
                        'proses' => $schedule['proses'] ?? 'Process ' . ($index + 1),
                        'status' => $schedule['status'] ?? 'pending',
                        'status_label' => $schedule['status_label'] ?? ucfirst($schedule['status'] ?? 'Pending'),
                        'rnd_approval_status' => $schedule['rnd_approval_status'] ?? 'pending',
                        'rnd_approval_status_label' => $schedule['rnd_approval_status_label'] ?? 'Pending',
                        'production_date_time' => $schedule['production_date_time'] ?? '-',
                        'deadline' => $schedule['deadline'] ?? '-',
                        'days_difference' => $schedule['days_difference'] ?? null
                    ];
                }
            }

            // Gabungkan data production schedules ke jobData
            $structuredJobData = array_merge($jobData, [
                'production_schedules' => $productionSchedules
            ]);

            $emailAdditionalData = array_merge([
                'notification_type' => 'progress_job_structured',
                'action_text' => 'Lihat Detail Job'
            ], $additionalData);

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new ProgressJobStructuredNotificationMail($setting, $targetReminder, $structuredJobData, $emailAdditionalData, $recipient));
                    Log::info("Progress job notification email sent to: {$recipient->email} for job {$jobData['job_code']}");
                } catch (\Exception $e) {
                    Log::error("Failed to send progress job notification to user {$userId} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending progress job notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi progress job dengan format terstruktur (seperti form CIR)
     */
    public function sendProgressJobStructuredNotification($jobData, $additionalData = [])
    {
        try {
            $setting = DevelopmentEmailNotificationSetting::where('process_code', 'progress_job')->where('is_active', true)->first();
            if (!$setting) {
                Log::info("No active email notification setting found for process_code: progress_job");
                return false;
            }

            $reminderSchedule = $setting->reminder_schedule ?? [];
            // Cari reminder dengan days = 'first'
            $targetReminder = null;
            foreach ($reminderSchedule as $reminder) {
                if ($reminder['days'] === 'first') {
                    $targetReminder = $reminder;
                    break;
                }
            }

            if (!$targetReminder) {
                Log::info("No reminder found for progress_job");
                return false;
            }

            // Siapkan data production schedules untuk tabel
            $productionSchedules = [];
            if (isset($jobData['production_schedules']) && is_array($jobData['production_schedules'])) {
                foreach ($jobData['production_schedules'] as $index => $schedule) {
                    $productionSchedules[] = [
                        'proses' => $schedule['proses'] ?? 'Process ' . ($index + 1),
                        'status' => $schedule['status'] ?? 'pending',
                        'status_label' => $schedule['status_label'] ?? ucfirst($schedule['status'] ?? 'Pending'),
                        'rnd_approval_status' => $schedule['rnd_approval_status'] ?? 'pending',
                        'rnd_approval_status_label' => $schedule['rnd_approval_status_label'] ?? 'Pending',
                        'production_date_time' => $schedule['production_date_time'] ?? '-',
                        'deadline' => $schedule['deadline'] ?? '-',
                        'days_difference' => $schedule['days_difference'] ?? null
                    ];
                }
            }

            // Gabungkan data production schedules ke jobData
            $structuredJobData = array_merge($jobData, [
                'production_schedules' => $productionSchedules
            ]);

            $emailAdditionalData = array_merge([
                'notification_type' => 'progress_job_structured',
                'action_text' => 'Lihat Detail Job'
            ], $additionalData);

            // Kirim email ke semua user yang dikonfigurasi
            foreach ($targetReminder['users'] as $userId) {
                try {
                    $recipient = User::find($userId);
                    if (!$recipient) {
                        Log::warning("User not found with ID: {$userId}");
                        continue;
                    }

                    Mail::to($recipient->email)->send(new ProgressJobStructuredNotificationMail($setting, $targetReminder, $structuredJobData, $emailAdditionalData, $recipient));
                    Log::info("Progress job structured notification email sent to: {$recipient->email} for job {$jobData['job_code']}");
                } catch (\Exception $e) {
                    Log::error("Failed to send progress job structured notification to user {$userId} for job {$jobData['job_code']}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending progress job structured notification: " . $e->getMessage());
            return false;
        }
    }
}
