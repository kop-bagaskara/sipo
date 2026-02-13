<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuNavigationSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'tb_menu_navigation_settings';

    protected $fillable = [
        'menu_key',
        'menu_name',
        'menu_icon',
        'menu_route',
        'allowed_divisi',
        'allowed_jabatan',
        'excluded_divisi',
        'excluded_jabatan',
        'is_active',
        'display_order',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allowed_divisi' => 'array',
        'allowed_jabatan' => 'array',
        'excluded_divisi' => 'array',
        'excluded_jabatan' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Check if user can access this menu
     */
    public function canAccess($user)
    {
        if (!$this->is_active) {
            return false;
        }

        // Check excluded first
        if ($this->excluded_divisi && in_array($user->divisi, $this->excluded_divisi)) {
            return false;
        }

        if ($this->excluded_jabatan && in_array($user->jabatan, $this->excluded_jabatan)) {
            return false;
        }

        // Check allowed divisi
        if ($this->allowed_divisi && !empty($this->allowed_divisi)) {
            if (!in_array($user->divisi, $this->allowed_divisi)) {
                return false;
            }
        }

        // Check allowed jabatan
        if ($this->allowed_jabatan && !empty($this->allowed_jabatan)) {
            if (!in_array($user->jabatan, $this->allowed_jabatan)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered menus
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('menu_name', 'asc');
    }

    /**
     * Get creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

