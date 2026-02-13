<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingSessionProgress;
use App\Models\TrainingAssignment;

class TrainingSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_sessions';

    protected $fillable = [
        'training_id',
        'session_order',
        'session_title',
        'description',
        'difficulty_level_id',
        'theme',
        'question_count',
        'passing_score',
        'has_video',
        'video_url',
        'google_drive_file_id',
        'video_source',
        'video_duration_seconds',
        'content',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'has_video' => 'boolean',
        'is_active' => 'boolean',
        'session_order' => 'integer',
        'display_order' => 'integer',
        'video_duration_seconds' => 'integer',
        'question_count' => 'integer',
        'passing_score' => 'decimal:2',
    ];

    /**
     * Get training
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingMaster::class, 'training_id');
    }

    /**
     * Get difficulty level
     */
    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(TrainingDifficultyLevel::class, 'difficulty_level_id');
    }

    /**
     * Get creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get session progress records
     */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(TrainingSessionProgress::class, 'session_id');
    }

    /**
     * Get formatted video duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->video_duration_seconds) {
            return null;
        }

        $hours = floor($this->video_duration_seconds / 3600);
        $minutes = floor(($this->video_duration_seconds % 3600) / 60);
        $seconds = $this->video_duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get random questions from question bank based on difficulty and theme
     * Questions are filtered by materials from the assignment (if provided) or training master (fallback)
     *
     * @param int|null $assignmentId Optional assignment ID to filter by assignment materials
     * @return \Illuminate\Support\Collection
     */
    public function get_random_questions($assignmentId = null)
    {
        $query = TrainingQuestionBank::active();

        // Filter by difficulty level if specified
        if ($this->difficulty_level_id) {
            $query->where('difficulty_level_id', $this->difficulty_level_id);
        }

        // Filter by theme if specified
        if ($this->theme) {
            $query->where('theme', $this->theme);
        }

        // Filter by materials - prioritize assignment materials, fallback to training master materials
        $materialIds = [];

        // Jika ada assignmentId, gunakan materials dari assignment
        if ($assignmentId) {
            $assignment = TrainingAssignment::find($assignmentId);
            if ($assignment && $assignment->material_ids) {
                $materialIds = is_array($assignment->material_ids)
                    ? $assignment->material_ids
                    : json_decode($assignment->material_ids, true);
            }
        }

        // Jika tidak ada materials dari assignment, gunakan materials dari training master
        if (empty($materialIds)) {
            $training = $this->training;
            if ($training) {
                // Get materials collection first, then pluck IDs to avoid ambiguous column error
                $materials = $training->materials;
                $materialIds = $materials->pluck('id')->toArray();
            }
        }

        // Apply material filter
        if (!empty($materialIds)) {
            $query->whereIn('material_id', $materialIds);
        } else {
            // Jika tidak ada material sama sekali, return empty collection
            Log::warning("Training Session {$this->id}: Tidak ada material yang ditemukan untuk filter soal.");
            return collect();
        }

        // Get all available questions first
        $allQuestions = $query->get();

        // Jika tidak ada soal sama sekali, return empty collection
        if ($allQuestions->isEmpty()) {
            Log::warning("Training Session {$this->id}: Tidak ada soal yang ditemukan dengan filter yang diberikan.");
            return collect();
        }

        // Shuffle the collection to ensure truly random order for each call
        // This ensures each user gets different random questions, not just different order
        $shuffledQuestions = $allQuestions->shuffle();

        // Jika soal yang ditemukan kurang dari yang dibutuhkan, duplikat random sampai mencapai jumlah yang dibutuhkan
        $questions = collect();
        $requiredCount = $this->question_count;
        $availableCount = $shuffledQuestions->count();

        if ($availableCount >= $requiredCount) {
            // Jika soal cukup, ambil sesuai jumlah yang dibutuhkan
            $questions = $shuffledQuestions->take($requiredCount);
        } else {
            // Jika soal kurang, duplikat random sampai mencapai jumlah yang dibutuhkan
            $questions = $shuffledQuestions;

            // Hitung berapa kali perlu duplikat
            $duplicateCount = $requiredCount - $availableCount;

            // Duplikat random dari soal yang ada
            for ($i = 0; $i < $duplicateCount; $i++) {
                // Ambil random soal dari collection yang sudah ada
                $randomQuestion = $shuffledQuestions->random();

                // Clone question (tetap pakai ID asli untuk scoring)
                $duplicatedQuestion = clone $randomQuestion;

                // Tambahkan ke collection
                $questions->push($duplicatedQuestion);
            }

            // Shuffle lagi setelah duplikat untuk randomisasi
            $questions = $questions->shuffle();

            Log::info("Training Session {$this->id}: Hanya menemukan {$availableCount} soal, menduplikat {$duplicateCount} soal random untuk mencapai {$requiredCount} soal.");
        }

        return $questions;
    }

    /**
     * Generate questions data for user session (to be stored in session_progress)
     *
     * @param int|null $assignmentId Optional assignment ID to filter by assignment materials
     * @return array
     */
    public function generateQuestionsForUser($assignmentId = null)
    {
        $questions = $this->get_random_questions($assignmentId);

        // Track question IDs untuk handle duplikat
        $usedIds = [];
        $duplicateCounter = 0;

        return $questions->map(function ($q, $index) use (&$usedIds, &$duplicateCounter) {
            $originalId = $q->id;
            $uniqueId = $originalId;

            // Jika ID sudah digunakan (duplikat), buat ID unik
            if (isset($usedIds[$originalId])) {
                $duplicateCounter++;
                $uniqueId = $originalId . '_dup_' . $duplicateCounter;
            } else {
                $usedIds[$originalId] = true;
            }

            return [
                'id' => $uniqueId, // ID unik untuk form submission
                'original_id' => $originalId, // ID asli untuk lookup di question bank
                'question' => $q->question,
                'question_type' => $q->question_type,
                'answer_options' => $q->answer_options,
                'order' => $index + 1,
                'score' => $q->score,
            ];
        })->values();
    }

    /**
     * Check if user can start this session
     * User must complete previous sessions before accessing the next one
     */
    public function canUserStart($employeeId, $assignmentId)
    {
        // Get all sessions for this training, ordered by session_order
        $allSessions = self::where('training_id', $this->training_id)
            ->active()
            ->ordered()
            ->get();

        // Find current session position
        $currentPosition = $allSessions->search(fn($s) => $s->id === $this->id);

        if ($currentPosition === false) {
            return false;
        }

        // If this is the first session, can start
        if ($currentPosition === 0) {
            return true;
        }

        // Check if all previous sessions are completed (passed, completed, or failed - as long as they're submitted)
        $previousSessions = $allSessions->take($currentPosition);

        foreach ($previousSessions as $prevSession) {
            $prevProgress = TrainingSessionProgress::where('assignment_id', $assignmentId)
                ->where('session_id', $prevSession->id)
                ->where('employee_id', $employeeId)
                ->first();

            // If previous session doesn't exist, can't start this session
            if (!$prevProgress) {
                return false;
            }

            // Previous session must be submitted (passed, completed, or failed) - not in_progress or not_started
            // User can proceed to next session even if previous session failed
            if (in_array($prevProgress->status, [TrainingSessionProgress::STATUS_NOT_STARTED, TrainingSessionProgress::STATUS_IN_PROGRESS])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope ordered by session order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('session_order', 'asc');
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

