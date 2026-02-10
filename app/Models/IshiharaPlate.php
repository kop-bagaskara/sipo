<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IshiharaPlate extends Model
{
    use HasFactory;

    protected $table = 'tb_ishihara_plates';
    protected $connection = 'pgsql2';

    protected $fillable = [
        'plate_number',
        'image_path',
        'correct_answer',
        'difficulty_level',
        'description',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'difficulty_level' => 'integer',
        'display_order' => 'integer',
    ];

    // Scope untuk hanya ambil yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk filter berdasarkan level kesulitan
    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    // Scope untuk random select
    public function scopeRandom($query, $limit = 20)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    // Get full image URL
    public function getImageUrlAttribute()
    {
        return asset($this->image_path);
    }
}

