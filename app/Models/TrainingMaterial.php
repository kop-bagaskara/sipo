<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql3';
    protected $table = 'tb_training_materials';

    protected $fillable = [
        'material_code',
        'material_title',
        'description',
        'category_id',
        'video_path',
        'video_resolution',
        'video_duration_seconds',
        'display_order',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'video_duration_seconds' => 'integer',
    ];

    /**
     * Boot method to generate material code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->material_code)) {
                $model->material_code = $model->generateMaterialCode();
            }
        });
    }

    /**
     * Generate unique material code
     */
    public function generateMaterialCode()
    {
        $prefix = 'MAT-';
        $year = date('y');
        $month = date('m');
        $day = date('d');

        $lastMaterial = static::whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', $month)
                            ->whereDay('created_at', $day)
                            ->orderBy('id', 'desc')
                            ->first();

        $sequence = $lastMaterial ? (intval(substr($lastMaterial->material_code, -4)) + 1) : 1;

        return $prefix . $year . $month . $day . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TrainingMaterialCategory::class, 'category_id');
    }

    /**
     * Get questions for this material
     */
    public function questions(): HasMany
    {
        return $this->hasMany(TrainingQuestionBank::class, 'material_id');
    }

    /**
     * Get progress records
     */
    public function progress(): HasMany
    {
        return $this->hasMany(TrainingMaterialProgress::class, 'material_id');
    }

    /**
     * Get exams for this material
     */
    public function exams(): HasMany
    {
        return $this->hasMany(TrainingExam::class, 'material_id');
    }

    /**
     * Scope for active materials
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
}

