<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingExam extends Model
{
    use HasFactory;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_exams';

    protected $fillable = [
        'assignment_id',
        'material_id',
        'employee_id',
        'question_ids',
        'user_answers',
        'score',
        'max_score',
        'passing_score',
        'status',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'started_at',
        'completed_at',
        'duration_seconds',
        'time_limit_seconds',
        'notes',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'user_answers' => 'array',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'integer',
        'time_limit_seconds' => 'integer',
    ];

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TrainingAssignment::class, 'assignment_id');
    }

    /**
     * Get material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(TrainingMaterial::class, 'material_id');
    }

    /**
     * Get employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get exam questions
     */
    public function examQuestions(): HasMany
    {
        return $this->hasMany(TrainingExamQuestion::class, 'exam_id');
    }

    /**
     * Get questions from question bank
     */
    public function getQuestionsAttribute()
    {
        if (!$this->question_ids) {
            return collect();
        }

        $questionIds = is_array($this->question_ids)
            ? $this->question_ids
            : json_decode($this->question_ids, true);

        return TrainingQuestionBank::whereIn('id', $questionIds)->get();
    }

    /**
     * Check if exam is passed
     */
    public function isPassed()
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            return false;
        }

        $percentage = ($this->max_score > 0) ? ($this->score / $this->max_score) * 100 : 0;
        return $percentage >= $this->passing_score;
    }

    /**
     * Check if exam is expired
     */
    public function isExpired()
    {
        if (!$this->time_limit_seconds || !$this->started_at) {
            return false;
        }

        $elapsed = now()->diffInSeconds($this->started_at);
        return $elapsed > $this->time_limit_seconds;
    }

    /**
     * Calculate score from answers
     */
    public function calculateScore()
    {
        $totalScore = 0;
        $maxScore = 0;
        $correctCount = 0;
        $wrongCount = 0;

        foreach ($this->examQuestions as $examQuestion) {
            $maxScore += $examQuestion->max_score;

            if ($examQuestion->is_correct) {
                $totalScore += $examQuestion->score_earned;
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        $this->score = $totalScore;
        $this->max_score = $maxScore;
        $this->correct_answers = $correctCount;
        $this->wrong_answers = $wrongCount;
        $this->save();
    }
}

