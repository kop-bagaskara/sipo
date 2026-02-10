<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $connection = 'mysql3'; // or your stock database connection
    protected $table = 'stock'; // adjust table name as needed

    protected $fillable = [
        'item_code',
        'item_name',
        'available_stock',
        'reserved_stock',
        'free_stock',
        'unit',
        'location',
        'warehouse',
        'last_updated',
        'min_stock',
        'max_stock'
    ];

    protected $casts = [
        'available_stock' => 'decimal:4',
        'reserved_stock' => 'decimal:4',
        'free_stock' => 'decimal:4',
        'min_stock' => 'decimal:4',
        'max_stock' => 'decimal:4',
        'last_updated' => 'datetime'
    ];

    /**
     * Get stock by item code
     */
    public static function getByItemCode($itemCode)
    {
        return static::where('item_code', $itemCode)->first();
    }

    /**
     * Check if stock is available for quantity
     */
    public function isAvailable($requiredQuantity)
    {
        return $this->free_stock >= $requiredQuantity;
    }

    /**
     * Get stock status
     */
    public function getStockStatus()
    {
        if ($this->free_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->free_stock <= $this->min_stock) {
            return 'low_stock';
        } elseif ($this->free_stock >= $this->max_stock) {
            return 'overstock';
        } else {
            return 'normal';
        }
    }

    /**
     * Get stock status badge class
     */
    public function getStockStatusBadgeClass()
    {
        switch ($this->getStockStatus()) {
            case 'out_of_stock':
                return 'badge-danger';
            case 'low_stock':
                return 'badge-warning';
            case 'overstock':
                return 'badge-info';
            default:
                return 'badge-success';
        }
    }
}
