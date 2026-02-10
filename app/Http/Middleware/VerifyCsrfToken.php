<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Tambahkan route yang perlu dikecualikan dari CSRF jika diperlukan
        // 'api/*',
        // 'webhook/*',
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        try {
            // Pastikan session sudah dimulai
            if (!$request->session()->isStarted()) {
                $request->session()->start();
            }

            // THROTTLING AGGRESIF: Jangan regenerate session jika sudah ada token yang valid
            $sessionToken = $request->session()->token();
            $inputToken = $request->input('_token');
            $headerToken = $request->header('X-CSRF-TOKEN');

            // Cek apakah sudah pernah di-regenerate dalam interval tertentu
            $lastTokenRegeneration = $request->session()->get('last_token_regeneration_time');
            $minInterval = 900; // 15 menit minimum interval

            // Jika session token kosong, baru regenerate dengan throttling
            if (empty($sessionToken) &&
                (!$lastTokenRegeneration || (time() - $lastTokenRegeneration) > $minInterval)) {

                // Cek apakah ada form submission in progress
                $formSubmissionInProgress = $request->session()->get('form_submission_in_progress', false);

                if (!$formSubmissionInProgress) {
                    Log::warning('Session token is empty, regenerating session', [
                        'url' => $request->url(),
                        'method' => $request->method(),
                        'last_regeneration' => $lastTokenRegeneration,
                    ]);

                    // Set flag untuk throttling middleware
                    $request->session()->put('regeneration_attempted', true);

                    $request->session()->regenerate();
                    $request->session()->put('last_token_regeneration_time', time());
                } else {
                    Log::info('Session regeneration delayed due to form submission', [
                        'url' => $request->url(),
                        'method' => $request->method(),
                        'form_submission_in_progress' => true,
                    ]);
                }
            }

            $result = parent::tokensMatch($request);

            // Log CSRF failures untuk debugging
            if (!$result) {
                Log::warning('CSRF token mismatch', [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'is_secure' => $request->isSecure(),
                    'session_token' => $request->session()->token(),
                    'input_token' => $inputToken,
                    'header_token' => $headerToken,
                    'x_xsrf_token' => $request->header('X-XSRF-TOKEN'),
                    'session_id' => $request->session()->getId(),
                    'user_agent' => $request->header('User-Agent'),
                ]);

                // THROTTLING AGGRESIF: Jangan regenerate session lagi jika sudah di-regenerate sebelumnya
                $lastCsrfRegeneration = $request->session()->get('last_csrf_regeneration_time');
                if (!$request->session()->has('csrf_regenerated') &&
                    (!$lastCsrfRegeneration || (time() - $lastCsrfRegeneration) > $minInterval)) {

                    // Cek apakah ada form submission in progress
                    $formSubmissionInProgress = $request->session()->get('form_submission_in_progress', false);

                    if (!$formSubmissionInProgress) {
                        // Set flag untuk throttling middleware
                        $request->session()->put('regeneration_attempted', true);

                        $request->session()->regenerate();
                        $request->session()->put('csrf_regenerated', true);
                        $request->session()->put('last_csrf_regeneration_time', time());
                        Log::info('Session regenerated due to CSRF mismatch');
                    } else {
                        Log::info('CSRF regeneration delayed due to form submission', [
                            'url' => $request->url(),
                            'method' => $request->method(),
                            'form_submission_in_progress' => true,
                        ]);
                    }
                } else {
                    Log::warning('CSRF regeneration throttled - too recent', [
                        'last_regeneration' => $lastCsrfRegeneration,
                        'time_since' => $lastCsrfRegeneration ? (time() - $lastCsrfRegeneration) : 'never',
                    ]);
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('CSRF verification error: ' . $e->getMessage(), [
                'url' => $request->url(),
                'method' => $request->method(),
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = urldecode($header);
        }

        return $token;
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
