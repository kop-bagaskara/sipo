<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBomHeader extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'masterbomh';
    protected $primaryKey = 'MaterialCode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaterialCode',
        'Formula',
        'Unit',
        'Qty',
        'IsAverageCOGM',
        'CompositionCode',
        'CreatedBy',
        'CreatedDate',
        'ChangedBy'
    ];

    /**
     * Get BOM data by material code
     */
    public static function getByMaterialCode($materialCode)
    {
        // Try exact match first, then LIKE match
        $result = static::where('MaterialCode', $materialCode)->get();
        if ($result->isEmpty()) {
            $result = static::where('MaterialCode', 'like', '%' . $materialCode . '%')->get();
        }
        return $result;
    }

    /**
     * Get BOM detail items from masterbomd
     */
    public function bomDetails()
    {
        return $this->hasMany(MasterBomDetail::class, 'MaterialCode', 'MaterialCode');
    }

    /**
     * Get processes from formula
     */
    public function getProcessesFromFormula()
    {
        $processes = [];
        $formula = $this->Formula;

        if (strpos($formula, '.PTG.') !== false) $processes[] = 'PTG';
        if (strpos($formula, '.CTK.') !== false) $processes[] = 'CTK';
        if (strpos($formula, '.EPL.') !== false) $processes[] = 'EPL';
        if (strpos($formula, '.EMB.') !== false) $processes[] = 'EMB';
        if (strpos($formula, '.PLG.') !== false) $processes[] = 'PLG';
        if (strpos($formula, '.KPS.') !== false) $processes[] = 'KPS';
        if (strpos($formula, '.STR.') !== false) $processes[] = 'STR';
        if (strpos($formula, '.LEM.') !== false) $processes[] = 'LEM';
        if (strpos($formula, '.TUM.') !== false) $processes[] = 'TUM';

        return $processes;
    }
}
