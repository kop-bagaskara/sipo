<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingDifficultyLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_difficulty_levels';

    protected $fillable = [
        'level_code',
        'level_name',
        'description',
        'score_multiplier',
        'display_order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'score_multiplier' => 'decimal:2',
        'display_order' => 'integer',
    ];

    /**
     * Get questions for this difficulty level
     */
    public function questions()
    {
        return $this->hasMany(TrainingQuestionBank::class, 'difficulty_level_id');
    }

    /**
     * Scope for active levels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
