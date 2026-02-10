<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'master_material';
    protected $primaryKey = 'MaterialCode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaterialCode',
        'MaterialName',
        'Unit',
        'Content'
    ];

    public function planFirstProductions()
    {
        return $this->hasMany(PlanFirstProduction::class, 'code_item', 'MaterialCode');
    }
}