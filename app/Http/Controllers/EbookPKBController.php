<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EbookPkbReadingLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EbookPKBController extends Controller
{
    public function index()
    {
        // Get page number from query, default to page 1
        $page = (int) request()->get('page', 1);
        $totalPages = 46; // Total pages in PKB (sesuai DOCX PKB FINAL)

        // Validate page number
        $page = max(1, min($page, $totalPages));

        // Check if specific page blade exists, otherwise use template
        $pageView = "main.ebook-pkb.pages.page-{$page}";
        $pageTemplate = view()->exists($pageView) ? $pageView : 'main.ebook-pkb.pages._template';

        // Initialize reading session if user is authenticated
        if (Auth::check()) {
            $this->initializeReadingSession($page);
        }

        return view('main.ebook-pkb.index', [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageView' => $pageTemplate,
        ]);
    }

    /**
     * Initialize or get reading session
     */
    private function initializeReadingSession($startPage)
    {
        $user = Auth::user();

        // Get active session
        $session = EbookPkbReadingLog::getActiveSession($user->id);

        if (!$session) {
            // Create new session
            EbookPkbReadingLog::create([
                'user_id' => $user->id,
                'start_page' => $startPage,
                'last_page_viewed' => $startPage,
                'pages_visited' => [$startPage],
                'total_pages_viewed' => 1,
                'time_spent_seconds' => 0,
                'session_start_at' => now(),
                'session_end_at' => now(),
                'marked_as_complete' => false,
                'interaction_log' => [
                    [
                        'type' => 'session_start',
                        'page' => $startPage,
                        'timestamp' => now()->toIso8601String(),
                    ]
                ],
            ]);
        }
    }

    /**
     * Track reading progress via AJAX
     */
    public function trackProgress(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'current_page' => 'required|integer|min:1',
            'time_spent_seconds' => 'nullable|integer|min:0',
            'interaction_type' => 'nullable|string', // page_view, scroll, search, etc
        ]);

        $user = Auth::user();
        $session = EbookPkbReadingLog::getActiveSession($user->id);

        if (!$session) {
            // Create new session if doesn't exist
            $session = EbookPkbReadingLog::create([
                'user_id' => $user->id,
                'start_page' => $validated['current_page'],
                'last_page_viewed' => $validated['current_page'],
                'pages_visited' => [$validated['current_page']],
                'total_pages_viewed' => 1,
                'time_spent_seconds' => 0,
                'session_start_at' => now(),
                'session_end_at' => now(),
                'marked_as_complete' => false,
            ]);
        }

        // Calculate delta time on server to avoid client tampering and page reload resets
        $now = Carbon::now();
        $lastUpdate = $session->session_end_at ?? $session->session_start_at ?? $now;
        $deltaSeconds = max(0, $lastUpdate->diffInSeconds($now));

        // Cap delta to avoid runaway when tab idle too long (e.g., 90s)
        $deltaSeconds = min($deltaSeconds, 90);

        $session->updateProgress(
            $validated['current_page'],
            $deltaSeconds,
            $validated['interaction_type'] ?? 'page_view',
            $now
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progress tracked',
            'session_id' => $session->id,
        ]);
    }

    /**
     * Mark reading session as complete
     */
    public function markSessionComplete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'completed_page' => 'required|integer|min:1',
            'total_time_seconds' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $session = EbookPkbReadingLog::getActiveSession($user->id);

        if (!$session) {
            // Fallback: pakai sesi terbaru yang belum ditandai selesai
            $session = EbookPkbReadingLog::where('user_id', $user->id)
                ->where('marked_as_complete', false)
                ->latest('session_start_at')
                ->first();
        }

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 404);
        }

        // Add final delta before marking complete
        $now = Carbon::now();
        $lastUpdate = $session->session_end_at ?? $session->session_start_at ?? $now;
        $deltaSeconds = max(0, $lastUpdate->diffInSeconds($now));
        $deltaSeconds = min($deltaSeconds, 90);
        $session->updateProgress($validated['completed_page'], $deltaSeconds, 'session_complete', $now);

        // Update session
        $session->update([
            'last_page_viewed' => $validated['completed_page'],
            // Keep accumulated time_spent_seconds from server-side calculation; fallback to provided total if any
            'time_spent_seconds' => $session->time_spent_seconds,
            'marked_as_complete' => true,
            'completed_at' => $now,
            'session_end_at' => $now,
        ]);

        // Add completion interaction
        $interactions = $session->interaction_log ?? [];
        $interactions[] = [
            'type' => 'session_complete',
            'page' => $validated['completed_page'],
            'timestamp' => $now->toIso8601String(),
        ];
        $session->update(['interaction_log' => $interactions]);

        return response()->json([
            'status' => 'success',
            'message' => 'Session marked as complete',
            'reading_duration' => $session->getReadingDuration(),
        ]);
    }

    /**
     * Get current reading session
     */
    public function getCurrentSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $session = EbookPkbReadingLog::getActiveSession($user->id);

        if (!$session) {
            return response()->json(['session' => null]);
        }

        return response()->json([
            'session' => [
                'id' => $session->id,
                'start_page' => $session->start_page,
                'last_page_viewed' => $session->last_page_viewed,
                'pages_visited' => $session->pages_visited,
                'time_spent_seconds' => $session->time_spent_seconds,
                'total_pages_viewed' => $session->total_pages_viewed,
                'is_complete' => $session->marked_as_complete,
            ]
        ]);
    }

    /**
     * Simple log viewer page
     */
    public function logs()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $logs = EbookPkbReadingLog::with(['user.divisiUser', 'user.jabatanUser'])
            ->latest('session_start_at')
            ->limit(100)
            ->get();

        return view('main.ebook-pkb.logs', compact('logs'));
    }

    /**
     * Get user reading statistics
     */
    public function getUserStatistics(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        $stats = EbookPkbReadingLog::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(time_spent_seconds) as total_time_spent,
                MAX(last_page_viewed) as highest_page_reached,
                COUNT(CASE WHEN marked_as_complete = 1 THEN 1 END) as completed_sessions,
                AVG(total_pages_viewed) as avg_pages_per_session
            ')
            ->first();

        return response()->json([
            'statistics' => [
                'total_sessions' => $stats->total_sessions ?? 0,
                'total_time_spent_seconds' => $stats->total_time_spent ?? 0,
                'highest_page_reached' => $stats->highest_page_reached ?? 0,
                'completed_sessions' => $stats->completed_sessions ?? 0,
                'avg_pages_per_session' => round($stats->avg_pages_per_session ?? 0, 2),
            ]
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        $results = [];
        $totalPages = 45; // Total pages in PKB (sesuai DOCX PKB FINAL)

        if (empty($keyword)) {
            return response()->json([
                'keyword' => $keyword,
                'results' => [],
                'total' => 0
            ]);
        }

        // Search across all pages
        for ($page = 1; $page <= $totalPages; $page++) {
            $pageView = "main.ebook-pkb.pages.page-{$page}";
            $template = view()->exists($pageView) ? $pageView : 'main.ebook-pkb.pages._template';

            try {
                // Render view to string for search
                $content = view($template, ['pageNumber' => $page])->render();

                // Remove HTML tags for search
                $textContent = strip_tags($content);
                $textContent = html_entity_decode($textContent, ENT_QUOTES, 'UTF-8');

                // Case-insensitive search
                $count = substr_count(strtolower($textContent), strtolower($keyword));

                if ($count > 0) {
                    $results[] = [
                        'page' => $page,
                        'matches' => $count,
                        'preview' => substr($textContent, 0, 150) . '...'
                    ];
                }
            } catch (\Exception $e) {
                // Skip pages that can't be rendered
                continue;
            }
        }

        return response()->json([
            'keyword' => $keyword,
            'results' => $results,
            'total' => count($results)
        ]);
    }
}

