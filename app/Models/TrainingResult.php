<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_results';

    protected $fillable = [
        'assignment_id',
        'employee_id',
        'total_score',
        'max_possible_score',
        'minimum_passing_score',
        'final_percentage',
        'status',
        'completed_date',
        'certificate_path',
        'certificate_number',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'max_possible_score' => 'decimal:2',
        'minimum_passing_score' => 'decimal:2',
        'final_percentage' => 'decimal:2',
        'completed_date' => 'date',
    ];

    // Status constants
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Boot method to generate certificate number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->certificate_number) && $model->status === self::STATUS_PASSED) {
                $model->certificate_number = $model->generateCertificateNumber();
            }
        });
    }

    /**
     * Generate unique certificate number
     */
    public function generateCertificateNumber()
    {
        $prefix = 'CERT-';
        $year = date('Y');
        $month = date('m');

        $lastCert = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->orderBy('id', 'desc')
                        ->first();

        $sequence = $lastCert ? (intval(substr($lastCert->certificate_number, -6)) + 1) : 1;

        return $prefix . $year . $month . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TrainingAssignment::class, 'assignment_id');
    }

    /**
     * Get employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Calculate final result from exams
     */
    public function calculateFromExams()
    {
        $assignment = $this->assignment;
        $exams = $assignment->exams()->where('status', TrainingExam::STATUS_COMPLETED)->get();

        $totalScore = 0;
        $maxScore = 0;

        foreach ($exams as $exam) {
            $totalScore += $exam->score;
            $maxScore += $exam->max_score;
        }

        $this->total_score = $totalScore;
        $this->max_possible_score = $maxScore;

        if ($maxScore > 0) {
            $this->final_percentage = ($totalScore / $maxScore) * 100;
        } else {
            $this->final_percentage = 0;
        }

        // Determine status
        if ($this->final_percentage >= $this->minimum_passing_score) {
            $this->status = self::STATUS_PASSED;
            if (!$this->certificate_number) {
                $this->certificate_number = $this->generateCertificateNumber();
            }
        } else {
            $this->status = self::STATUS_FAILED;
        }

        if (!$this->completed_date) {
            $this->completed_date = now()->toDateString();
        }

        $this->save();
    }

    /**
     * Check if result is passed
     */
    public function isPassed()
    {
        return $this->status === self::STATUS_PASSED;
    }
}

