<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SplEmployee extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_spl_employees';

    protected $fillable = [
        'spl_request_id',
        'employee_id',
        'nip',
        'employee_name',
        'is_manual',
        'is_signed',
        'signed_at',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
    ];

    /**
     * Get SPL request
     */
    public function splRequest(): BelongsTo
    {
        return $this->belongsTo(SplRequest::class, 'spl_request_id');
    }

    /**
     * Get employee (if linked)
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Mark as signed
     */
    public function markAsSigned()
    {
        $this->update([
            'is_signed' => true,
            'signed_at' => now(),
        ]);
    }
}
