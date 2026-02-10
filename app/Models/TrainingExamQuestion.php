<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingExamQuestion extends Model
{
    use HasFactory;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_exam_questions';

    protected $fillable = [
        'exam_id',
        'question_bank_id',
        'question_order',
        'user_answer',
        'is_correct',
        'score_earned',
        'max_score',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'question_order' => 'integer',
        'score_earned' => 'decimal:2',
        'max_score' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    /**
     * Get exam
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(TrainingExam::class, 'exam_id');
    }

    /**
     * Get question bank
     */
    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(TrainingQuestionBank::class, 'question_bank_id');
    }

    /**
     * Check answer and update score
     */
    public function checkAnswer($userAnswer)
    {
        $this->user_answer = $userAnswer;
        $this->answered_at = now();

        $question = $this->questionBank;
        $this->is_correct = $question->isCorrectAnswer($userAnswer);
        $this->score_earned = $this->is_correct ? $this->max_score : 0;

        $this->save();
    }
}

