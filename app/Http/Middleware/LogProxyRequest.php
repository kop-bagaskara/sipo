<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogProxyRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     return $next($request);
    // }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Log the original request URL
        Log::info('Original Request URL: ' . $request->url());

        // Log the headers forwarded by the proxy
        if ($request->header('X-Forwarded-For')) {
            Log::info('X-Forwarded-For (client): ' . $request->header('X-Forwarded-For'));
        }

        if ($request->header('X-Forwarded-Host')) {
            Log::info('X-Forwarded-Host: ' . $request->header('X-Forwarded-Host'));
            
        }


        // You can also log the full URL or the IP that was forwarded
        Log::info('Forwarded URL: ' . $request->fullUrl());


        //if ($request->header('X-Forwarded-Host') === 'vpn.krisanthium.com:7090' || str_contains($request->url(),'7090')) {
                
                // if(str_contains($request->url(),'login'))
                // {
                //     Log::warning('redirect login ');
                //     return redirect('https://vpn.krisanthium.com/guest/login');
                // }
                // elseif(str_contains($request->url(),'admin'))
                // {
                //     Log::warning('redirect admin ');
                //     return redirect('https://vpn.krisanthium.com/guest/admin');
                // }
                // else
                // {
                //     Log::warning('redirect home ');
                //     return redirect('https://vpn.krisanthium.com/guest');
                // }
            // Redirect to a different URL (for example, to a new route or URL)
            
          //  }

        return $next($request);
    }
}
