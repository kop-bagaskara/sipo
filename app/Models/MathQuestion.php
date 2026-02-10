<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MathQuestion extends Model
{
    use HasFactory;

    protected $table = 'tb_math_questions';
    protected $connection = 'pgsql2';

    protected $fillable = [
        'question',
        'answer',
        'question_number',
        'question_type',
        'difficulty_level',
        'explanation',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Check if user answer is correct
     * Handles multiple answer formats
     */
    public function checkAnswer($userAnswer)
    {
        // Normalize user answer
        $userAnswer = trim($userAnswer);

        // Check if answer allows multiple formats (comma-separated)
        $correctAnswers = array_map('trim', explode(',', $this->answer));

        foreach ($correctAnswers as $correctAnswer) {
            // Normalize comparison
            $normalizedUser = strtolower($userAnswer);
            $normalizedCorrect = strtolower($correctAnswer);

            // Exact match
            if ($normalizedUser === $normalizedCorrect) {
                return true;
            }

            // Numeric comparison (handle decimals)
            if (is_numeric($userAnswer) && is_numeric($correctAnswer)) {
                if (floatval($userAnswer) == floatval($correctAnswer)) {
                    return true;
                }
            }

            // Handle fraction format (1 1/4, 1.25, 5/4)
            if ($this->isFractionFormat($userAnswer) && $this->isFractionFormat($correctAnswer)) {
                if ($this->normalizeFraction($userAnswer) == $this->normalizeFraction($correctAnswer)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if answer is in fraction format
     */
    private function isFractionFormat($answer)
    {
        return preg_match('/\d+\s*\d+\/\d+|\d+\/\d+/', $answer);
    }

    /**
     * Normalize fraction to decimal for comparison
     */
    private function normalizeFraction($fraction)
    {
        // Handle mixed fraction like "1 1/4"
        if (preg_match('/(\d+)\s+(\d+)\/(\d+)/', $fraction, $matches)) {
            $whole = intval($matches[1]);
            $numerator = intval($matches[2]);
            $denominator = intval($matches[3]);
            return $whole + ($numerator / $denominator);
        }

        // Handle simple fraction like "5/4"
        if (preg_match('/(\d+)\/(\d+)/', $fraction, $matches)) {
            $numerator = intval($matches[1]);
            $denominator = intval($matches[2]);
            return $numerator / $denominator;
        }

        return floatval($fraction);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('question_number', 'asc');
    }

    public function scopeRandom($query, $limit = null)
    {
        $query = $query->inRandomOrder();
        if ($limit) {
            $query->limit($limit);
        }
        return $query;
    }
}

