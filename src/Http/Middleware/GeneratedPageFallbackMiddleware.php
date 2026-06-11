<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;
use Nirjon\LaravelSeo\Models\SeoSetting;
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
                return response(app(SitemapService::class)->generateXml(), 200, ['Content-Type' => 'text/xml']);
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

        return in_array($slug, ['sitemap.xml', $this->configuredSitemapFilename()], true);
    }

    protected function configuredSitemapFilename(): string
    {
        $filename = (string) config('seo.sitemap.filename', 'sitemap.xml');

        try {
            if (Schema::hasTable('nirjon_seo_settings')) {
                $storedFilename = SeoSetting::where('key', 'sitemap.filename')->value('value');

                if (is_string($storedFilename) && trim($storedFilename) !== '') {
                    $filename = $storedFilename;
                }
            }
        } catch (\Throwable $exception) {
            //
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $baseName = Str::slug($baseName ?: 'sitemap');

        return ($baseName ?: 'sitemap') . '.xml';
    }
}
