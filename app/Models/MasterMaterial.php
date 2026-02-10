<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMaterial extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'mastermaterial';
    protected $primaryKey = 'Code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Code',
        'Name',
        'NameInPO',
        'Substitute',
        'SmallestUnit',
        'SoldUnit',
        'SKUUnit',
        'Group1',
        'Group2',
    ];

    /**
     * Get material by code
     */
    public static function getByCode($code)
    {
        return static::where('Code', $code)->first();
    }
}

