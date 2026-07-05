<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleSeoRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('seo.modules.redirections', true)) {
            return $next($request);
        }

        $currentPath = '/' . trim($request->path(), '/');
        $currentUrl = rtrim($request->fullUrl(), '/');

        $redirects = \Nirjon\LaravelSeo\Models\SeoRedirect::where('is_active', 1)->get();

        foreach ($redirects as $redirect) {
            if (! $this->matches($redirect, $currentPath, $currentUrl)) {
                continue;
            }

            if (in_array((int) $redirect->redirect_type, [410, 451], true)) {
                abort((int) $redirect->redirect_type);
            }

            return redirect($redirect->destination_url, (int) $redirect->redirect_type);
        }

        return $next($request);
    }

    private function matches($redirect, string $currentPath, string $currentUrl): bool
    {
        $source = $this->normalizeSource($redirect->source_url);
        $path = $currentPath;
        $url = $currentUrl;

        if ($redirect->ignore_case) {
            $source = strtolower($source);
            $path = strtolower($path);
            $url = strtolower($url);
        }

        return match ($redirect->match_type) {
            'starts_with' => str_starts_with($path, $source) || str_starts_with($url, $source),
            'contains' => str_contains($path, $source) || str_contains($url, $source),
            default => trim($path, '/') === trim($source, '/') || rtrim($url, '/') === rtrim($source, '/'),
        };
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
