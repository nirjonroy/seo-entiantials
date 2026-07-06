<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;
use Nirjon\LaravelSeo\Services\SitemapService;
use Symfony\Component\HttpFoundation\Response;

class GeneratedPageFallbackMiddleware
{
    /**
     * Render a generated PageForge page only when the host application did not
     * match one of its own routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 404 || ! $request->isMethodSafe()) {
            return $response;
        }

        $slug = trim($request->path(), '/');

        if ($this->isSitemapRequest($slug)) {
            try {
                return response(app(SitemapService::class)->generateXml($slug), 200)
                    ->header('Content-Type', 'text/xml; charset=UTF-8');
            } catch (\Throwable $exception) {
                return $response;
            }
        }

        if ($slug === '' || str_contains($slug, '/') || str_ends_with($slug, '.xml') || $this->shouldSkip($slug)) {
            return $response;
        }

        try {
            if (! SeoGeneratedPage::where('url_slug', $slug)->exists()) {
                return $response;
            }

            return response(app(GeneratedPageController::class)->show($slug));
        } catch (\Throwable $exception) {
            return $response;
        }
    }

    protected function shouldSkip(string $slug): bool
    {
        $reserved = [
            'admin',
            'api',
            'seo-media',
            'storage',
            'uploads',
            'assets',
            'css',
            'js',
            'images',
            'sitemap.xml',
            'robots.txt',
            'llms.txt',
        ];

        return in_array($slug, $reserved, true);
    }

    protected function isSitemapRequest(string $slug): bool
    {
        if (! str_ends_with($slug, '.xml')) {
            return false;
        }

        return app(SitemapService::class)->canServe($slug);
    }
}
