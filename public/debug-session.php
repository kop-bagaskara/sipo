<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

try {
    echo "<h1>Debug Session & CSRF</h1>";
    
    // Test Laravel bootstrap
    echo "<h2>1. Laravel Bootstrap</h2>";
    echo "<p>Laravel version: " . app()->version() . "</p>";
    
    // Test session configuration
    echo "<h2>2. Session Configuration</h2>";
    echo "<p>Session driver: " . config('session.driver') . "</p>";
    echo "<p>Session cookie: " . config('session.cookie') . "</p>";
    echo "<p>Session files path: " . config('session.files') . "</p>";
    echo "<p>Session lifetime: " . config('session.lifetime') . " minutes</p>";
    echo "<p>Session domain: " . config('session.domain') . "</p>";
    echo "<p>Session secure: " . (config('session.secure') ? 'true' : 'false') . "</p>";
    echo "<p>Session same_site: " . config('session.same_site') . "</p>";
    
    // Test session status
    echo "<h2>3. Session Status</h2>";
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<p>Session status: Active</p>";
        echo "<p>Session ID: " . session_id() . "</p>";
        echo "<p>Session name: " . session_name() . "</p>";
        echo "<p>Session save path: " . session_save_path() . "</p>";
    } else {
        echo "<p>Session status: " . session_status() . "</p>";
    }
    
    // Test CSRF token
    echo "<h2>4. CSRF Token</h2>";
    try {
        $csrfToken = csrf_token();
        echo "<p>CSRF token: " . ($csrfToken ? $csrfToken : 'EMPTY') . "</p>";
        echo "<p>CSRF token length: " . strlen($csrfToken) . "</p>";
    } catch (Exception $e) {
        echo "<p>CSRF Error: " . $e->getMessage() . "</p>";
    }
    
    // Test session data
    echo "<h2>5. Session Data</h2>";
    echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
    
    // Test Laravel session
    echo "<h2>6. Laravel Session</h2>";
    try {
        $laravelSession = app('session');
        echo "<p>Laravel session ID: " . $laravelSession->getId() . "</p>";
        echo "<p>Laravel session token: " . $laravelSession->token() . "</p>";
        echo "<p>Laravel session data: " . print_r($laravelSession->all(), true) . "</p>";
    } catch (Exception $e) {
        echo "<p>Laravel Session Error: " . $e->getMessage() . "</p>";
    }
    
    // Test form with CSRF
    echo "<h2>7. Test Form</h2>";
    echo "<form method='POST' action=''>";
    echo csrf_field();
    echo "<input type='text' name='test' value='test value'>";
    echo "<input type='submit' value='Test Submit'>";
    echo "</form>";
    
    // Test POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>8. POST Request Result</h2>";
        echo "<p>POST data: " . print_r($_POST, true) . "</p>";
        echo "<p>Session after POST: " . print_r($_SESSION, true) . "</p>";
        
        // Test CSRF validation
        try {
            $request = app('request');
            echo "<p>Request CSRF token: " . $request->input('_token') . "</p>";
            echo "<p>Session CSRF token: " . app('session')->token() . "</p>";
            echo "<p>CSRF match: " . ($request->input('_token') === app('session')->token() ? 'YES' : 'NO') . "</p>";
        } catch (Exception $e) {
            echo "<p>CSRF Validation Error: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}
?>
