<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrNotification extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_hr_notifications';

    // Notification type constants
    const TYPE_REQUEST_SUBMITTED = 'request_submitted';
    const TYPE_SUPERVISOR_APPROVAL = 'supervisor_approval';
    const TYPE_HR_APPROVAL = 'hr_approval';
    const TYPE_REQUEST_APPROVED = 'request_approved';
    const TYPE_REQUEST_REJECTED = 'request_rejected';
    const TYPE_REQUEST_CANCELLED = 'request_cancelled';
    const TYPE_REMINDER = 'reminder';

    protected $fillable = [
        'request_id',
        'recipient_id',
        'notification_type',
        'title',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function request()
    {
        return $this->belongsTo(EmployeeRequest::class, 'request_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}
