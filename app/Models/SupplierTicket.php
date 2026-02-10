<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\POService;

class SupplierTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_supplier_tickets';
    protected $posService;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->posService = new POService();
    }
    protected $fillable = [
        'ticket_number',
        'po_number',
        'supplier_delivery_doc',
        'delivery_date',
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'supplier_address',
        'description',
        'status',
        'notes',
        'rejection_reason',
        'rejected_quantity',
        'accepted_quantity',
        'rejection_date',
        'rejected_item_json',
        'grd_number',
        'pqc_number',
        'grd_created_by',
        'grd_created_at',
        'pqc_created_by',
        'pqc_created_at',
        'created_by',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'processed_at' => 'datetime',
        'rejection_date' => 'date',
        'grd_created_at' => 'datetime',
        'pqc_created_at' => 'datetime',
        'rejected_item_json' => 'array',
        // 'rejected_quantity' => 'decimal:2',
        // 'accepted_quantity' => 'decimal:2'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PROCESSED = 'processed';
    const STATUS_COMPLETED = 'completed';

    // Generate unique ticket number
    public static function generateTicketNumber()
    {
        $prefix = 'TSP-';
        // Format: TSP-yyMMdd-0001 (e.g., TSP-250910-0001)
        $date = now()->format('ymd');
        $lastTicket = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTicket ? (int)substr($lastTicket->ticket_number, -4) + 1 : 1;
        
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Get PO details from MySQL3
    public function getPODetails()
    {
        $poDetails = $this->posService->getPODetails($this->po_number);
        // dd($poDetails);
        // This will be implemented in a service class
        return $poDetails;
    }

    // Relationship with user who created the ticket
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with user who processed the ticket
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scope for pending tickets
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope for approved tickets
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // Check if ticket can be processed
    public function canBeProcessed()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PENDING]);
    }

    

    // Get status badge class
    public function getStatusBadgeClass()
    {
        switch($this->status) {
            case self::STATUS_PENDING:
                return 'bg-warning text-white';
            case self::STATUS_APPROVED:
                return 'bg-info text-white';
            case self::STATUS_REJECTED:
                return 'bg-danger text-white';
            case self::STATUS_PROCESSED:
                return 'bg-primary text-white';
            case self::STATUS_COMPLETED:
                return 'bg-success text-white';
            default:
                return 'bg-secondary text-white';
        }
    }

    // Get rejected items as array
    public function getRejectedItems()
    {
        $items = $this->rejected_item_json;
        
        // If it's already an array, return it
        if (is_array($items)) {
            return $items;
        }
        
        // If it's a JSON string, decode it
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    // Check if ticket has rejected items
    public function hasRejectedItems()
    {
        $items = $this->getRejectedItems();
        return !empty($items) && is_array($items) && count($items) > 0;
    }

}
