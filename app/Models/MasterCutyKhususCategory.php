<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCutyKhususCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_cuty_khusus_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
        'default_duration_days',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk mengambil hanya kategori yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
