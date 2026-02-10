<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanFirstProduction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_plan_first_productions';

    protected $fillable = [
        'code_plan',
        'code_machine',
        'code_item',
        'wo_docno',
        'so_docno',
        'quantity',
        'start_jam',
        'end_jam',
        'est_jam',
        'flag_status',
        'process',
        'material_name'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'code_machine', 'Code');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'code_item', 'MaterialCode');
    }
}
