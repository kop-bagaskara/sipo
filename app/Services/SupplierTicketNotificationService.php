<?php

namespace App\Services;

use App\Models\SupplierTicket;
use App\Models\User;
use App\Models\EmailNotificationSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupplierTicketNotificationService
{
    /**
     * Kirim notifikasi email untuk supplier ticket baru
     */
    public function sendSupplierTicketNotification(SupplierTicket $supplierTicket)
    {
        try {
            Log::info('Sending supplier ticket notification', [
                'ticket_id' => $supplierTicket->id,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

            // Dapatkan setting email untuk supplier ticket
            $emailSetting = EmailNotificationSetting::where('notification_type', 'kedatangan_supplier')
                                                    ->where('is_active', true)
                                                    ->first();

            if (!$emailSetting) {
                Log::warning('No email notification setting found for kedatangan_supplier');
                return;
            }

            // Dapatkan user yang harus mendapat notifikasi
            $usersToEmail = $emailSetting->getActiveUsers();
            
            if ($usersToEmail->isEmpty()) {
                Log::warning('No active users found for supplier ticket notification');
                return;
            }

            Log::info('Found users to notify', [
                'users_count' => $usersToEmail->count(),
                'setting_id' => $emailSetting->id
            ]);

            // Kirim email ke setiap user
            foreach ($usersToEmail as $user) {
                if ($user->email) {
                    $this->sendEmailNotification($user, $supplierTicket);
                } else {
                    Log::warning('User has no email', [
                        'user_id' => $user->id,
                        'user_name' => $user->name
                    ]);
                }
            }

            Log::info('Supplier ticket notification sent successfully', [
                'ticket_id' => $supplierTicket->id,
                'users_notified' => $usersToEmail->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket notification', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Kirim notifikasi email untuk update status supplier ticket
     */
    public function sendSupplierTicketStatusUpdateNotification(SupplierTicket $supplierTicket, $oldStatus, $newStatus)
    {
        try {
            Log::info('Sending supplier ticket status update notification', [
                'ticket_id' => $supplierTicket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Dapatkan setting email untuk supplier ticket
            $emailSetting = EmailNotificationSetting::where('notification_type', 'kedatangan_supplier')
                                                    ->where('is_active', true)
                                                    ->first();

            if (!$emailSetting) {
                Log::warning('No email notification setting found for kedatangan_supplier status update');
                return;
            }

            // Dapatkan user yang harus mendapat notifikasi
            $usersToEmail = $emailSetting->getActiveUsers();
            
            if ($usersToEmail->isEmpty()) {
                Log::warning('No active users found for supplier ticket status update notification');
                return;
            }

            // Kirim email ke setiap user
            foreach ($usersToEmail as $user) {
                if ($user->email) {
                    $this->sendStatusUpdateEmailNotification($user, $supplierTicket, $oldStatus, $newStatus);
                }
            }

            Log::info('Supplier ticket status update notification sent successfully', [
                'ticket_id' => $supplierTicket->id,
                'users_notified' => $usersToEmail->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket status update notification', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Kirim email notifikasi supplier ticket baru
     */
    private function sendEmailNotification(User $user, SupplierTicket $supplierTicket)
    {
        try {
            $data = [
                'user' => $user,
                'supplierTicket' => $supplierTicket,
                'supplier' => $supplierTicket->supplier,
                'subject' => 'Notifikasi Kedatangan Supplier - ' . $supplierTicket->ticket_number
            ];

            Mail::send('emails.supplier-ticket-notification', $data, function ($message) use ($user, $supplierTicket) {
                $message->to($user->email, $user->name)
                        ->subject('Notifikasi Kedatangan Supplier - ' . $supplierTicket->ticket_number);
            });

            Log::info('Supplier ticket email sent successfully', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket email', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim email notifikasi update status supplier ticket
     */
    private function sendStatusUpdateEmailNotification(User $user, SupplierTicket $supplierTicket, $oldStatus, $newStatus)
    {
        try {
            $data = [
                'user' => $user,
                'supplierTicket' => $supplierTicket,
                'supplier' => $supplierTicket->supplier,
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'subject' => 'Update Status Supplier Ticket - ' . $supplierTicket->ticket_number
            ];

            Mail::send('emails.supplier-ticket-status-update', $data, function ($message) use ($user, $supplierTicket) {
                $message->to($user->email, $user->name)
                        ->subject('Update Status Supplier Ticket - ' . $supplierTicket->ticket_number);
            });

            Log::info('Supplier ticket status update email sent successfully', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket status update email', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim notifikasi email untuk approval supplier ticket
     */
    public function sendSupplierTicketApprovalNotification(SupplierTicket $supplierTicket)
    {
        try {
            Log::info('Sending supplier ticket approval notification', [
                'ticket_id' => $supplierTicket->id,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

            // Dapatkan setting email untuk supplier ticket
            $emailSetting = EmailNotificationSetting::where('notification_type', 'supplier_ticket')
                                                    ->where('is_active', true)
                                                    ->first();

            if (!$emailSetting) {
                Log::warning('No email notification setting found for supplier_ticket approval');
                return;
            }

            // Dapatkan user yang harus mendapat notifikasi
            $usersToEmail = $emailSetting->getActiveUsers();
            
            if ($usersToEmail->isEmpty()) {
                Log::warning('No active users found for supplier ticket approval notification');
                return;
            }

            // Kirim email ke setiap user
            foreach ($usersToEmail as $user) {
                if ($user->email) {
                    $this->sendApprovalEmailNotification($user, $supplierTicket);
                }
            }

            Log::info('Supplier ticket approval notification sent successfully', [
                'ticket_id' => $supplierTicket->id,
                'users_notified' => $usersToEmail->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket approval notification', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Kirim notifikasi email untuk rejection supplier ticket dengan surat penolakan
     */
    public function sendSupplierTicketRejectionNotification(SupplierTicket $supplierTicket, $rejectionLetter)
    {
        try {
            Log::info('Sending supplier ticket rejection notification', [
                'ticket_id' => $supplierTicket->id,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

            // Dapatkan setting email untuk supplier ticket
            $emailSetting = EmailNotificationSetting::where('notification_type', 'supplier_ticket')
                                                    ->where('is_active', true)
                                                    ->first();

            if (!$emailSetting) {
                Log::warning('No email notification setting found for supplier_ticket rejection');
                return;
            }

            // Dapatkan user yang harus mendapat notifikasi
            $usersToEmail = $emailSetting->getActiveUsers();
            
            if ($usersToEmail->isEmpty()) {
                Log::warning('No active users found for supplier ticket rejection notification');
                return;
            }

            // Kirim email ke setiap user
            foreach ($usersToEmail as $user) {
                if ($user->email) {
                    $this->sendRejectionEmailNotification($user, $supplierTicket, $rejectionLetter);
                }
            }

            Log::info('Supplier ticket rejection notification sent successfully', [
                'ticket_id' => $supplierTicket->id,
                'users_notified' => $usersToEmail->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket rejection notification', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Kirim email notifikasi approval supplier ticket
     */
    private function sendApprovalEmailNotification(User $user, SupplierTicket $supplierTicket)
    {
        try {
            $data = [
                'user' => $user,
                'supplierTicket' => $supplierTicket,
                'supplier' => $supplierTicket->supplier,
                'subject' => 'Approval Supplier Ticket - ' . $supplierTicket->ticket_number
            ];

            Mail::send('emails.supplier-ticket-approval', $data, function ($message) use ($user, $supplierTicket) {
                $message->to($user->email, $user->name)
                        ->subject('Approval Supplier Ticket - ' . $supplierTicket->ticket_number);
            });

            Log::info('Supplier ticket approval email sent successfully', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket approval email', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim email notifikasi rejection supplier ticket dengan surat penolakan
     */
    private function sendRejectionEmailNotification(User $user, SupplierTicket $supplierTicket, $rejectionLetter)
    {
        try {
            $data = [
                'user' => $user,
                'supplierTicket' => $supplierTicket,
                'supplier' => $supplierTicket->supplier,
                'rejectionLetter' => $rejectionLetter,
                'subject' => 'Rejection Supplier Ticket - ' . $supplierTicket->ticket_number
            ];

            Mail::send('emails.supplier-ticket-rejection', $data, function ($message) use ($user, $supplierTicket) {
                $message->to($user->email, $user->name)
                        ->subject('Rejection Supplier Ticket - ' . $supplierTicket->ticket_number);
            });

            Log::info('Supplier ticket rejection email sent successfully', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send supplier ticket rejection email', [
                'user_email' => $user->email,
                'ticket_number' => $supplierTicket->ticket_number,
                'error' => $e->getMessage()
            ]);
        }
    }
}
