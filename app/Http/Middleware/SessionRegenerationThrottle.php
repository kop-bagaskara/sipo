<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class SessionRegenerationThrottle
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
        // Pastikan session sudah dimulai
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Cek apakah ada regeneration yang terlalu sering
        $regenerationCount = $request->session()->get('regeneration_count', 0);
        $lastRegenerationTime = $request->session()->get('last_regeneration_time', 0);
        $currentTime = time();

        // Reset counter jika sudah lebih dari 1 jam
        if (($currentTime - $lastRegenerationTime) > 3600) {
            $regenerationCount = 0;
            $request->session()->put('regeneration_count', 0);
        }

        // THROTTLING AGGRESIF: Jika regeneration terlalu sering, block
        if ($regenerationCount > 2) { // Kurangi dari 3 ke 2
            Log::error('Session regeneration blocked - too frequent', [
                'url' => $request->url(),
                'method' => $request->method(),
                'regeneration_count' => $regenerationCount,
                'last_regeneration' => $lastRegenerationTime,
                'time_since' => $currentTime - $lastRegenerationTime,
                'session_id' => $request->session()->getId(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
            ]);

            // Return error response tanpa regenerate session
            return response()->json([
                'error' => 'Session regeneration blocked due to excessive attempts',
                'message' => 'Silakan refresh halaman dan coba lagi. Jika masalah berlanjut, hubungi admin.',
                'code' => 'SESSION_THROTTLED'
            ], 429);
        }

        // Increment counter setiap kali ada regeneration
        if ($request->session()->has('regeneration_attempted')) {
            $regenerationCount++;
            $request->session()->put('regeneration_count', $regenerationCount);
            $request->session()->put('last_regeneration_time', $currentTime);
            $request->session()->forget('regeneration_attempted');

            Log::warning('Session regeneration detected', [
                'url' => $request->url(),
                'method' => $request->method(),
                'regeneration_count' => $regenerationCount,
                'session_id' => $request->session()->getId(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
            ]);
        }

        $response = $next($request);

        // Cek apakah ada session regeneration dalam response
        if ($request->session()->has('regeneration_attempted')) {
            Log::info('Session regeneration completed', [
                'url' => $request->url(),
                'method' => $request->method(),
                'session_id' => $request->session()->getId(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
            ]);
        }

        return $response;
    }
}
