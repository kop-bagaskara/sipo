<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAbsenceSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $connection = 'pgsql2';
    protected $table = 'tb_master_absence_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'absence_type',
        'min_deadline_days',
        'max_deadline_days',
        'attachment_required',
        'deadline_text',
        'description',
        'is_active',
        'master_sub_absence',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_deadline_days' => 'integer',
        'max_deadline_days' => 'integer',
        'attachment_required' => 'boolean',
        'is_active' => 'boolean',
        'master_sub_absence' => 'array',
    ];

    /**
     * Scope untuk mengambil hanya setting yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get setting by absence type
     */
    public static function getByType($absenceType)
    {
        return static::active()->where('absence_type', $absenceType)->first();
    }
}
