<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_type',
        'action_type',
        'table_name',
        'record_id',
        'record_identifier',
        'old_data',
        'new_data',
        'changed_fields',
        'description',
        'ip_address',
        'user_agent',
        'user_id',
        'user_name',
        'user_jabatan'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope untuk filter berdasarkan log type
     */
    public function scopeByLogType($query, $logType)
    {
        return $query->where('log_type', $logType);
    }

    /**
     * Scope untuk filter berdasarkan action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope untuk filter berdasarkan table name
     */
    public function scopeByTableName($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan record identifier
     */
    public function scopeByRecordIdentifier($query, $identifier)
    {
        return $query->where('record_identifier', $identifier);
    }

    /**
     * Get formatted created at
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Get log type badge color
     */
    public function getLogTypeBadgeColorAttribute()
    {
        $colors = [
            'development' => 'primary',
            'prepress' => 'info',
            'production' => 'success',
            'general' => 'secondary'
        ];

        return $colors[$this->log_type] ?? 'secondary';
    }

    /**
     * Get action type badge color
     */
    public function getActionTypeBadgeColorAttribute()
    {
        $colors = [
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'status_change' => 'info'
        ];

        return $colors[$this->action_type] ?? 'secondary';
    }

    /**
     * Get user info
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
