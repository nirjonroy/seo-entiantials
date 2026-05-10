<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nirjon\LaravelSeo\Models\SeoRedirect;
use Symfony\Component\HttpFoundation\Response;

class SeoRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeRedirects = SeoRedirect::where('is_active', true)->get();
        $requestPath = $request->path();

        foreach ($activeRedirects as $redirect) {
            $sourceUrl = $redirect->source_url;
            $currentPath = $requestPath;

            if ($redirect->ignore_case) {
                $sourceUrl = strtolower($sourceUrl);
                $currentPath = strtolower($currentPath);
            }

            $isMatch = false;

            if ($redirect->match_type === 'exact') {
                // Compare paths without leading or trailing slashes to ensure consistent matching
                $isMatch = trim($sourceUrl, '/') === trim($currentPath, '/');
            }
            // Add other match_type implementations like 'regex', 'starts_with', etc., here later

            if ($isMatch) {
                if (in_array($redirect->redirect_type, [410, 451])) {
                    abort($redirect->redirect_type);
                }

                return redirect($redirect->destination_url, $redirect->redirect_type);
            }
        }

        return $next($request);
    }
}
