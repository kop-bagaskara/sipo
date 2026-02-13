<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingSessionProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_session_progress';

    protected $fillable = [
        'assignment_id',
        'session_id',
        'employee_id',
        'status',
        'score',
        'correct_answers_count',
        'total_questions',
        'questions_data',
        'answers_data',
        'started_at',
        'completed_at',
        'duration_seconds',
        'attempts_count',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'correct_answers_count' => 'integer',
        'total_questions' => 'integer',
        'questions_data' => 'array',
        'answers_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'integer',
        'attempts_count' => 'integer',
    ];

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';

    /**
     * Get assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TrainingAssignment::class, 'assignment_id');
    }

    /**
     * Get session
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    /**
     * Get employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Start the session (generate questions and mark as in_progress)
     */
    public function startSession()
    {
        if ($this->status === self::STATUS_NOT_STARTED) {
            // Generate questions from the session, pass assignment_id to filter by assignment materials
            $questions = $this->session->generateQuestionsForUser($this->assignment_id);
            $this->questions_data = $questions;
            $this->total_questions = count($questions);
            $this->status = self::STATUS_IN_PROGRESS;
            $this->started_at = now();
            $this->attempts_count = 1;
            $this->save();
        } elseif ($this->status === self::STATUS_IN_PROGRESS && (empty($this->questions_data) || (is_array($this->questions_data) && count($this->questions_data) === 0))) {
            // Jika status sudah in_progress tapi questions_data kosong, generate ulang
            $questions = $this->session->generateQuestionsForUser($this->assignment_id);
            $this->questions_data = $questions;
            $this->total_questions = count($questions);
            $this->save();
        }
    }

    /**
     * Submit answers and calculate score
     */
    public function submitAnswers(array $answers)
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return false;
        }

        $this->answers_data = $answers;
        $correctCount = 0;
        $totalScore = 0;

        // Get question IDs from stored questions_data
        // Handle duplikat: gunakan original_id jika ada, jika tidak pakai id
        $questionIds = collect($this->questions_data)->map(function ($q) {
            return $q['original_id'] ?? $q['id'];
        })->unique()->values();

        // Fetch correct answers from question bank
        $questionBank = TrainingQuestionBank::whereIn('id', $questionIds)->get()->keyBy('id');
        
        // Create mapping dari unique_id ke original_id
        $idMapping = collect($this->questions_data)->mapWithKeys(function ($q) {
            return [$q['id'] => ($q['original_id'] ?? $q['id'])];
        });

        // Check each answer
        foreach ($answers as $questionId => $userAnswer) {
            // Gunakan original_id untuk lookup di question bank
            $originalId = $idMapping->get($questionId, $questionId);
            $question = $questionBank->get($originalId);

            if ($question && $question->isCorrectAnswer($userAnswer)) {
                $correctCount++;
                $totalScore += $question->score;
            }
        }

        $this->correct_answers_count = $correctCount;
        $this->score = $totalScore;
        $this->completed_at = now();

        // Calculate duration
        if ($this->started_at) {
            $this->duration_seconds = $this->completed_at->diffInSeconds($this->started_at);
        }

        // Determine status based on passing score
        $passingScore = $this->session->passing_score ?? 70.00;
        $percentageScore = ($correctCount / $this->total_questions) * 100;

        if ($percentageScore >= $passingScore) {
            $this->status = self::STATUS_PASSED;
        } else {
            $this->status = self::STATUS_FAILED;
        }

        $this->save();

        return $this->status === self::STATUS_PASSED;
    }

    /**
     * Retry the session (regenerate questions)
     */
    public function retry()
    {
        // Regenerate questions, pass assignment_id to filter by assignment materials
        $this->questions_data = $this->session->generateQuestionsForUser($this->assignment_id);
        $this->total_questions = count($this->questions_data);
        $this->answers_data = null;
        $this->status = self::STATUS_IN_PROGRESS;
        $this->started_at = now();
        $this->completed_at = null;
        $this->duration_seconds = null;
        $this->score = null;
        $this->correct_answers_count = 0;
        $this->attempts_count++;
        $this->save();
    }

    /**
     * Check if user can start this session
     */
    public function canStart()
    {
        return $this->session->canUserStart($this->employee_id, $this->assignment_id);
    }

    /**
     * Get next session in the training
     */
    public function getNextSession()
    {
        $currentSession = $this->session;
        $nextSession = TrainingSession::where('training_id', $currentSession->training_id)
            ->where('session_order', '>', $currentSession->session_order)
            ->active()
            ->orderBy('session_order', 'asc')
            ->first();

        return $nextSession;
    }

    /**
     * Check if user can proceed to next session
     */
    public function canProceedToNext()
    {
        return in_array($this->status, [self::STATUS_PASSED, self::STATUS_COMPLETED]);
    }

    /**
     * Scope by assignment
     */
    public function scopeByAssignment($query, $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }

    /**
     * Scope by employee
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_PASSED]);
    }

    /**
     * Scope in progress
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }
}
