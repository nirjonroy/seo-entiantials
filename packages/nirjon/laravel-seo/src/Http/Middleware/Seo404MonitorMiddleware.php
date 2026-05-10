<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Nirjon\LaravelSeo\Models\Seo404Log;

class Seo404MonitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() == 404) {
            $log = Seo404Log::firstOrNew(['url' => $request->fullUrl()]);
            
            $log->referer = $request->header('referer');
            $log->user_agent = $request->userAgent();
            $log->ip_address = $request->ip();
            
            if ($log->exists) {
                $log->hits++;
            } else {
                $log->hits = 1;
            }
            
            $log->save();
        }

        return $response;
    }
}
