<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderH extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'workorderh';
    protected $primaryKey = 'DocNo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'DocNo',
        'Series',
        'DocDate',
        'SODocNo',
        'Template',
        'Formula',
        'MaterialCode',
        'Unit',
        'Qty',
        'CheckQtyOutput',
        'BatchNo',
        'BatchInfo',
        'ExpiryDate',
        'Information',
        'Status',
    ];

    protected $casts = [
        'DocDate' => 'date',
        'ExpiryDate' => 'date',
        'Qty' => 'decimal:4',
        'CheckQtyOutput' => 'boolean',
    ];

    /**
     * Search work order by DocNo (WOT)
     */
    public static function searchByWOT($wot)
    {
        return static::where('DocNo', $wot)->first();
    }

    /**
     * Scope untuk search work order
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('DocNo', 'like', '%' . $search . '%')
              ->orWhere('SODocNo', 'like', '%' . $search . '%')
              ->orWhere('MaterialCode', 'like', '%' . $search . '%');
        });
    }

    /**
     * Relationship to MasterMaterial
     */
    public function material()
    {
        return $this->belongsTo(MasterMaterial::class, 'MaterialCode', 'Code');
    }
}

