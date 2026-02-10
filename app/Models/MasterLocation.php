<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLocation extends Model
{
    use HasFactory;

    protected $connection = 'mysql3';
    protected $table = 'masterlocation';

    protected $fillable = [
        'Code',
        'Name',
        'Description',
        'is_active',
    ];

    /**
     * Scope untuk location yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope untuk search location
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('Code', 'like', '%' . $search . '%')
              ->orWhere('Name', 'like', '%' . $search . '%')
              ->orWhere('Description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get locations for dropdown
     */
    public static function getForDropdown()
    {
        return static::active()
            ->orderBy('Code')
            ->get(['Code', 'Name']);
    }
}

