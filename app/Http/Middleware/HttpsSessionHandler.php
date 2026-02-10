<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HttpsSessionHandler
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

        // Handle HTTPS session khusus - hanya sekali per session
        // Gunakan header dari reverse proxy untuk deteksi HTTPS
        $isSecure = $request->isSecure() ||
                    $request->header('X-Forwarded-Proto') === 'https' ||
                    $request->header('X-Forwarded-Ssl') === 'on';

        // THROTTLING AGGRESIF: Hanya regenerate jika benar-benar diperlukan
        if ($isSecure && !$request->session()->has('https_session_established')) {
            // Cek apakah sudah pernah di-regenerate dalam interval tertentu
            $lastRegeneration = $request->session()->get('last_regeneration_time');
            $minInterval = 1800; // 30 menit minimum interval (lebih ketat)

            // Cek juga apakah user sedang dalam proses form submission
            $isFormSubmission = $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH');
            $formSubmissionInProgress = $request->session()->get('form_submission_in_progress', false);

            if (!$lastRegeneration || (time() - $lastRegeneration) > $minInterval) {
                // Jangan regenerate jika user sedang submit form atau ada form submission in progress
                if (!$isFormSubmission && !$formSubmissionInProgress) {
                    $oldSessionId = $request->session()->getId();

                    // Set flag untuk throttling middleware
                    $request->session()->put('regeneration_attempted', true);

                    // Regenerate session untuk keamanan HTTPS
                    $request->session()->regenerate();

                    $request->session()->put('https_session_established', true);
                    $request->session()->put('https_regenerated_at', now());
                    $request->session()->put('last_regeneration_time', time());

                    Log::info('HTTPS session established', [
                        'old_session_id' => $oldSessionId,
                        'new_session_id' => $request->session()->getId(),
                        'url' => $request->url(),
                        'method' => $request->method(),
                    ]);
                } else {
                    // Jika sedang submit form, tunda regeneration
                    $request->session()->put('https_regeneration_pending', true);
                    Log::info('HTTPS regeneration delayed due to form submission', [
                        'url' => $request->url(),
                        'method' => $request->method(),
                        'form_submission_in_progress' => $formSubmissionInProgress,
                    ]);
                }
            }
        }

        // Update session cookie settings untuk HTTPS
        if ($request->isSecure() && config('session.secure') === null) {
            config(['session.secure' => true]);
        }

        return $next($request);
    }
}
