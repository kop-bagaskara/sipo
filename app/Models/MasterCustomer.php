<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCustomer extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'mastercustomer';
    protected $primaryKey = 'Code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Code',
        'Name',
        'Address',
        'Address2',
        'City',
        'Country',
        'Phone',
        'Fax',
        'Email',
        'Contact',
        'Mobile',
    ];

    /**
     * Scope untuk search customer
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('Code', 'like', '%' . $search . '%')
              ->orWhere('Name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope untuk customer yang tidak dihapus
     */
    public function scopeActive($query)
    {
        return $query->where('IsDeleted', 0);
    }
}

