<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KeepSessionAlive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan session sudah dimulai
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Refresh session setiap request untuk mencegah timeout
        if (Auth::check()) {
            // Regenerate session ID untuk keamanan setiap 30 menit
            if (!$request->session()->has('regenerated_at') ||
                now()->diffInMinutes($request->session()->get('regenerated_at')) > 120) {

                try {
                    $request->session()->regenerate();
                    $request->session()->put('regenerated_at', now());

                    // Log untuk debugging di production
                    if (config('app.debug')) {
                        Log::info('Session regenerated for user: ' . Auth::id());
                    }
                } catch (\Exception $e) {
                    Log::error('Session regeneration failed: ' . $e->getMessage());
                }
            }

            // Update last activity time
            $request->session()->put('last_activity', now());
        }

        return $next($request);
    }
}
