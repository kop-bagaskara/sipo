<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PreventFormSubmissionInterruption
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

        // Cek apakah ini adalah form submission
        $isFormSubmission = $request->isMethod('POST') ||
                           $request->isMethod('PUT') ||
                           $request->isMethod('PATCH') ||
                           $request->has('_token') ||
                           $request->header('X-CSRF-TOKEN');

        // Jika sedang submit form, set flag untuk mencegah regeneration
        if ($isFormSubmission) {
            $request->session()->put('form_submission_in_progress', true);
            $request->session()->put('form_submission_time', time());

            Log::info('Form submission detected - preventing session regeneration', [
                'url' => $request->url(),
                'method' => $request->method(),
                'has_csrf_token' => $request->has('_token'),
                'has_header_token' => !empty($request->header('X-CSRF-TOKEN')),
                'session_id' => $request->session()->getId(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
            ]);
        }

        $response = $next($request);

        // Clear flag setelah form submission selesai
        if ($isFormSubmission) {
            $request->session()->forget('form_submission_in_progress');
            $request->session()->forget('form_submission_time');
        }

        return $response;
    }
}
