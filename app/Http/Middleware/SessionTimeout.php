<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Hanya cek untuk user yang sudah login
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $timeout = config('session.lifetime', 4320) * 60; // Convert to seconds

            // THROTTLING: Cek timeout hanya setiap 5 menit untuk mengurangi overhead
            $lastTimeoutCheck = Session::get('last_timeout_check', 0);
            $checkInterval = 300; // 5 menit

            if ((time() - $lastTimeoutCheck) > $checkInterval) {
                // Jika session sudah timeout
                if ($lastActivity) {
                    // Pastikan lastActivity adalah timestamp, bukan Carbon object
                    $lastActivityTime = is_numeric($lastActivity) ? $lastActivity : $lastActivity->timestamp;

                    if ((time() - $lastActivityTime) > $timeout) {
                        Log::warning('Session timeout detected, logging out user', [
                            'user_id' => Auth::id(),
                            'last_activity' => $lastActivity,
                            'timeout_threshold' => $timeout,
                            'time_since_activity' => time() - $lastActivityTime,
                        ]);

                        Auth::logout();
                        Session::flush();

                        // Redirect ke login dengan pesan timeout
                        return redirect()->route('login')->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
                    }
                }

                // Update last timeout check
                Session::put('last_timeout_check', time());
            }

            // Update last activity dengan throttling (hanya setiap 2 menit)
            $lastUpdate = Session::get('last_activity_update', 0);
            $updateInterval = 120; // 2 menit

            if ((time() - $lastUpdate) > $updateInterval) {
                Session::put('last_activity', time());
                Session::put('last_activity_update', time());
            }
        }

        return $next($request);
    }
}
