<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingQuestionBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_question_banks';

    protected $fillable = [
        'question',
        'question_type',
        'difficulty_level_id',
        'material_id',
        'theme',
        'type_number',
        'correct_answer',
        'answer_options',
        'explanation',
        'score',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'answer_options' => 'array',
        'score' => 'decimal:2',
    ];

    // Question types
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_ESSAY = 'essay';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_FILL_BLANK = 'fill_blank';

    /**
     * Get difficulty level
     */
    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(TrainingDifficultyLevel::class, 'difficulty_level_id');
    }

    /**
     * Get material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(TrainingMaterial::class, 'material_id');
    }

    /**
     * Get exam questions using this question bank
     */
    public function examQuestions(): HasMany
    {
        return $this->hasMany(TrainingExamQuestion::class, 'question_bank_id');
    }

    /**
     * Scope for active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by difficulty level
     */
    public function scopeByDifficulty($query, $difficultyLevelId)
    {
        return $query->where('difficulty_level_id', $difficultyLevelId);
    }

    /**
     * Scope by material
     */
    public function scopeByMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    /**
     * Scope by theme
     */
    public function scopeByTheme($query, $theme)
    {
        return $query->where('theme', $theme);
    }

    /**
     * Scope by type number
     */
    public function scopeByTypeNumber($query, $typeNumber)
    {
        return $query->where('type_number', $typeNumber);
    }

    /**
     * Check if answer is correct
     */
    public function isCorrectAnswer($userAnswer)
    {
        if ($this->question_type === self::TYPE_MULTIPLE_CHOICE) {
            return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer));
        }

        return trim($userAnswer) === trim($this->correct_answer);
    }
}

