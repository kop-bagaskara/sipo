<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class EbookPkbReadingLog extends Model
{
    use HasFactory;

    protected $table = 'tb_ebook_pkb_reading_logs';

    protected $fillable = [
        'user_id',
        'start_page',
        'last_page_viewed',
        'total_pages_viewed',
        'time_spent_seconds',
        'session_start_at',
        'session_end_at',
        'pages_visited',
        'interaction_log',
        'marked_as_complete',
        'completed_at',
    ];

    protected $casts = [
        'pages_visited' => 'json',
        'interaction_log' => 'json',
        'session_start_at' => 'datetime',
        'session_end_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active reading session for current user
     */
    public static function getActiveSession($userId)
    {
        // Aktif = belum ditandai selesai, dan sesi masih dalam window waktu wajar
        return self::where('user_id', $userId)
            ->where('marked_as_complete', false)
            ->where(function ($q) {
                $q->whereNull('session_end_at')
                  ->orWhere('session_end_at', '>=', now()->subHours(6)); // anti zombie
            })
            ->latest('session_start_at')
            ->first();
    }

    /**
     * Mark session as complete
     */
    public function markAsComplete()
    {
        $this->update([
            'marked_as_complete' => true,
            'completed_at' => now(),
            'session_end_at' => now(),
        ]);
    }

    /**
     * Update reading progress (server-calculated delta)
     */
    public function updateProgress($currentPage, $deltaSeconds = null, $interactionType = null, $now = null)
    {
        $now = $now ?? now();
        $pages = $this->pages_visited ?? [];

        // Add current page if not already in array
        if (!in_array($currentPage, $pages)) {
            $pages[] = $currentPage;
        }

        // Add interaction log
        $interactions = $this->interaction_log ?? [];
        if ($interactionType) {
            $interactions[] = [
                'type' => $interactionType,
                'page' => $currentPage,
                'timestamp' => now()->toIso8601String(),
            ];
        }

        $updateData = [
            'last_page_viewed' => $currentPage,
            'pages_visited' => $pages,
            'total_pages_viewed' => count($pages),
            'interaction_log' => $interactions,
            'session_end_at' => $now,
        ];

        if ($deltaSeconds !== null) {
            $updateData['time_spent_seconds'] = ($this->time_spent_seconds ?? 0) + $deltaSeconds;
        }

        return $this->update($updateData);
    }

    /**
     * Get reading duration in human-readable format
     */
    public function getReadingDuration()
    {
        $seconds = $this->time_spent_seconds;
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($hours > 0) $parts[] = "{$hours}j";
        if ($minutes > 0) $parts[] = "{$minutes}m";
        if ($secs > 0) $parts[] = "{$secs}d";

        return implode(' ', $parts) ?: '0d';
    }
}
