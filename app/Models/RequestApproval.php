<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RequestApproval extends Model
{
    protected $connection = 'pgsql2';
    protected $table = 'tb_request_approvals';

    protected $fillable = [
        'request_id',
        'approver_id',
        'level',
        'status',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'status' => 'string',
        'approved_at' => 'datetime',
    ];

    // Relations
    public function request()
    {
        return $this->belongsTo(EmployeeRequest::class, 'request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Scopes
    public function scopeForLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
