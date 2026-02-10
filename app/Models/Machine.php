<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory;

    // protected $connection = 'mysql3';
    protected $table = 'tb_machines';
    // protected $primaryKey = 'Code';
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = [
        'Code',
        'Description',
        'Department',
        'CapacityPerHour',
        'Unit',
        // 'is_active'
    ];

    public function planFirstProductions()
    {
        return $this->hasMany(PlanFirstProduction::class, 'Code', 'code_machine');
    }

    /**
     * Scope untuk mesin yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
