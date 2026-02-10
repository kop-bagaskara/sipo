<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Handle419Error
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

        // Cek apakah ini adalah POST request yang mungkin mengalami 419
        if ($request->isMethod('POST')) {
            // Cek apakah CSRF token ada dan valid
            $csrfToken = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

            if (empty($csrfToken)) {
                Log::warning('CSRF token missing in POST request', [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'session_id' => $request->session()->getId(),
                ]);

                // Jangan regenerate session di sini - biarkan VerifyCsrfToken yang handle
                // Hanya log warning untuk monitoring
            }
        }

        $response = $next($request);

        // Cek apakah response adalah 419 error
        if ($response->getStatusCode() === 419) {
            Log::warning('419 error detected', [
                'url' => $request->url(),
                'method' => $request->method(),
                'session_id' => $request->session()->getId(),
                'csrf_token_exists' => !empty($request->input('_token')),
                'header_token_exists' => !empty($request->header('X-CSRF-TOKEN')),
            ]);

            // Jangan regenerate session di sini - biarkan middleware lain yang handle
            // Hanya log warning untuk monitoring
        }

        return $response;
    }
}
