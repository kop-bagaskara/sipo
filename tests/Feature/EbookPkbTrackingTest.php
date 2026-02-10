<!-- Save this file as: tests/Feature/EbookPkbTrackingTest.php -->

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EbookPkbReadingLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EbookPkbTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'name' => 'Test Reader',
            'email' => 'reader@test.com',
        ]);
    }

    /**
     * Test: Can access ebook PKB page when authenticated
     */
    public function test_can_access_ebook_page_when_authenticated()
    {
        $response = $this->actingAs($this->user)
            ->get('/ebook-pkb');

        $response->assertStatus(200);
        $response->assertViewHas('currentPage', 1);
        $response->assertViewHas('totalPages', 46);
    }

    /**
     * Test: Reading session is created on first visit
     */
    public function test_reading_session_created_on_first_visit()
    {
        $this->actingAs($this->user)
            ->get('/ebook-pkb');

        // Check session was created
        $log = EbookPkbReadingLog::where('user_id', $this->user->id)
            ->whereNull('session_end_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(1, $log->start_page);
        $this->assertEquals(1, $log->last_page_viewed);
        $this->assertContains(1, $log->pages_visited);
    }

    /**
     * Test: Can track reading progress
     */
    public function test_can_track_reading_progress()
    {
        $this->actingAs($this->user)
            ->get('/ebook-pkb');

        // Simulate page change
        $response = $this->actingAs($this->user)
            ->postJson('/api/ebook-pkb/tracking/update-progress', [
                'current_page' => 5,
                'time_spent_seconds' => 300,
                'interaction_type' => 'page_view',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verify database
        $log = EbookPkbReadingLog::where('user_id', $this->user->id)
            ->whereNull('session_end_at')
            ->first();

        $this->assertEquals(5, $log->last_page_viewed);
        $this->assertEquals(300, $log->time_spent_seconds);
        $this->assertContains(5, $log->pages_visited);
    }

    /**
     * Test: Can mark session as complete
     */
    public function test_can_mark_session_complete()
    {
        // Create initial session
        $this->actingAs($this->user)
            ->get('/ebook-pkb');

        // Mark as complete
        $response = $this->actingAs($this->user)
            ->postJson('/api/ebook-pkb/tracking/mark-complete', [
                'completed_page' => 46,
                'total_time_seconds' => 1800,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $response->assertJsonPath('reading_duration', '30m');

        // Verify database
        $log = EbookPkbReadingLog::where('user_id', $this->user->id)->first();

        $this->assertTrue($log->marked_as_complete);
        $this->assertNotNull($log->completed_at);
        $this->assertNotNull($log->session_end_at);
        $this->assertEquals(46, $log->last_page_viewed);
    }

    /**
     * Test: Can get current session info
     */
    public function test_can_get_current_session()
    {
        // Create and update session
        $this->actingAs($this->user)
            ->get('/ebook-pkb');

        $this->actingAs($this->user)
            ->postJson('/api/ebook-pkb/tracking/update-progress', [
                'current_page' => 10,
                'time_spent_seconds' => 600,
                'interaction_type' => 'page_view',
            ]);

        // Get session
        $response = $this->actingAs($this->user)
            ->getJson('/api/ebook-pkb/tracking/current-session');

        $response->assertStatus(200);
        $response->assertJsonPath('session.last_page_viewed', 10);
        $response->assertJsonPath('session.time_spent_seconds', 600);
    }

    /**
     * Test: Can get user statistics
     */
    public function test_can_get_user_statistics()
    {
        // Create multiple sessions
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($this->user)
                ->get('/ebook-pkb');

            $this->actingAs($this->user)
                ->postJson('/api/ebook-pkb/tracking/update-progress', [
                    'current_page' => 5 + $i,
                    'time_spent_seconds' => 600,
                    'interaction_type' => 'page_view',
                ]);

            $this->actingAs($this->user)
                ->postJson('/api/ebook-pkb/tracking/mark-complete', [
                    'completed_page' => 5 + $i,
                    'total_time_seconds' => 600,
                ]);
        }

        // Get statistics
        $response = $this->actingAs($this->user)
            ->getJson('/api/ebook-pkb/tracking/user-statistics');

        $response->assertStatus(200);
        $response->assertJsonPath('statistics.total_sessions', 3);
        $response->assertJsonPath('statistics.completed_sessions', 3);
    }

    /**
     * Test: Cannot track without authentication
     */
    public function test_cannot_track_without_authentication()
    {
        $response = $this->postJson('/api/ebook-pkb/tracking/update-progress', [
            'current_page' => 5,
            'time_spent_seconds' => 300,
            'interaction_type' => 'page_view',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Interaction log records all interactions
     */
    public function test_interaction_log_records_interactions()
    {
        // Create session
        $this->actingAs($this->user)
            ->get('/ebook-pkb');

        // Track multiple interactions
        $this->actingAs($this->user)
            ->postJson('/api/ebook-pkb/tracking/update-progress', [
                'current_page' => 1,
                'time_spent_seconds' => 100,
                'interaction_type' => 'scroll',
            ]);

        $this->actingAs($this->user)
            ->postJson('/api/ebook-pkb/tracking/update-progress', [
                'current_page' => 1,
                'time_spent_seconds' => 150,
                'interaction_type' => 'search',
            ]);

        // Verify interactions recorded
        $log = EbookPkbReadingLog::where('user_id', $this->user->id)
            ->whereNull('session_end_at')
            ->first();

        $interactions = $log->interaction_log;
        $types = array_column($interactions, 'type');

        $this->assertContains('session_start', $types);
        $this->assertContains('scroll', $types);
        $this->assertContains('search', $types);
    }

    /**
     * Test: Reading duration calculation is correct
     */
    public function test_reading_duration_calculation()
    {
        // Create session with known duration
        $log = EbookPkbReadingLog::create([
            'user_id' => $this->user->id,
            'start_page' => 1,
            'last_page_viewed' => 10,
            'time_spent_seconds' => 3665, // 1 hour, 1 minute, 5 seconds
            'pages_visited' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        ]);

        $duration = $log->getReadingDuration();

        // Should be "1j 1m 5d" format
        $this->assertStringContainsString('1j', $duration);
        $this->assertStringContainsString('1m', $duration);
        $this->assertStringContainsString('5d', $duration);
    }

    /**
     * Test: Can detect active session
     */
    public function test_can_detect_active_session()
    {
        // Create active session
        EbookPkbReadingLog::create([
            'user_id' => $this->user->id,
            'start_page' => 1,
            'last_page_viewed' => 5,
            'session_end_at' => null, // Active session
            'pages_visited' => [1, 2, 3, 4, 5],
        ]);

        // Get active session
        $activeSession = EbookPkbReadingLog::getActiveSession($this->user->id);

        $this->assertNotNull($activeSession);
        $this->assertNull($activeSession->session_end_at);
        $this->assertEquals(5, $activeSession->last_page_viewed);
    }
}

/**
 * HOW TO RUN TESTS:
 *
 * 1. From command line:
 *    php artisan test tests/Feature/EbookPkbTrackingTest.php
 *
 * 2. Run specific test:
 *    php artisan test tests/Feature/EbookPkbTrackingTest.php --filter=test_can_access_ebook_page_when_authenticated
 *
 * 3. Run with verbose output:
 *    php artisan test tests/Feature/EbookPkbTrackingTest.php --verbose
 *
 * 4. Run with database output:
 *    php artisan test tests/Feature/EbookPkbTrackingTest.php --verbose --no-coverage
 */
