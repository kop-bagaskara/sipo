<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBomDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'masterbomd';
    public $timestamps = false;

    protected $fillable = [
        'MaterialCode',
        'Formula',
        'Unit',
        'Qty'
    ];

    /**
     * Get BOM detail by material code
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
     * Relation to BOM Header
     */
    public function bomHeader()
    {
        return $this->belongsTo(MasterBomHeader::class, 'MaterialCode', 'MaterialCode');
    }

    /**
     * Get stock information for this item (placeholder for future stock integration)
     */
    public function getStockInfo()
    {
        // TODO: Implement stock checking from stock table
        // For now, return placeholder data
        return [
            'available_stock' => 0,
            'reserved_stock' => 0,
            'free_stock' => 0,
            'unit' => $this->Unit,
            'location' => '-',
            'last_updated' => null
        ];
    }
}
