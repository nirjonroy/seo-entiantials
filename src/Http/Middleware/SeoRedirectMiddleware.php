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
        if (! config('seo.modules.redirections', true)) {
            return $next($request);
        }

        $activeRedirects = SeoRedirect::where('is_active', true)->get();
        $requestPath = '/' . trim($request->path(), '/');
        $requestUrl = rtrim($request->fullUrl(), '/');

        foreach ($activeRedirects as $redirect) {
            $sourceUrl = $this->normalizeSource($redirect->source_url);
            $currentPath = $requestPath;
            $currentUrl = $requestUrl;

            if ($redirect->ignore_case) {
                $sourceUrl = strtolower($sourceUrl);
                $currentPath = strtolower($currentPath);
                $currentUrl = strtolower($currentUrl);
            }

            $isMatch = match ($redirect->match_type) {
                'starts_with' => str_starts_with($currentPath, $sourceUrl) || str_starts_with($currentUrl, $sourceUrl),
                'contains' => str_contains($currentPath, $sourceUrl) || str_contains($currentUrl, $sourceUrl),
                default => trim($sourceUrl, '/') === trim($currentPath, '/') || rtrim($sourceUrl, '/') === rtrim($currentUrl, '/'),
            };

            if ($isMatch) {
                if (in_array($redirect->redirect_type, [410, 451])) {
                    abort($redirect->redirect_type);
                }

                return redirect($redirect->destination_url, $redirect->redirect_type);
            }
        }

        return $next($request);
    }

    private function normalizeSource(string $source): string
    {
        $source = trim($source);
        $path = parse_url($source, PHP_URL_PATH);

        if ($path && parse_url($source, PHP_URL_SCHEME)) {
            return '/' . trim($path, '/');
        }

        return '/' . trim($source, '/');
    }
}
