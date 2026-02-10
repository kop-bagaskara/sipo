<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeEmployee extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_overtime_employees';

    protected $fillable = [
        'request_id',
        'employee_id',
        'employee_name',
        'department',
        'start_time',
        'end_time',
        'job_description',
        'is_signed',
        'signed_at'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_signed' => 'boolean',
        'signed_at' => 'datetime'
    ];

    /**
     * Get the request that owns this overtime employee
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(EmployeeRequest::class, 'request_id');
    }

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get time range for display
     */
    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get overtime duration in hours
     */
    public function getDurationHoursAttribute()
    {
        $start = $this->start_time;
        $end = $this->end_time;

        // Handle overnight overtime
        if ($end < $start) {
            $end->addDay();
        }

        return $start->diffInHours($end);
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Mark as signed
     */
    public function markAsSigned()
    {
        $this->update([
            'is_signed' => true,
            'signed_at' => now()
        ]);
    }

    /**
     * Scope for signed employees
     */
    public function scopeSigned($query)
    {
        return $query->where('is_signed', true);
    }

    /**
     * Scope for unsigned employees
     */
    public function scopeUnsigned($query)
    {
        return $query->where('is_signed', false);
    }
}
