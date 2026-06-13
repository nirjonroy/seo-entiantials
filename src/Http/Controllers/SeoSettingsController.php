<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;
use Nirjon\LaravelSeo\Models\SeoSetting;
use Nirjon\LaravelSeo\Services\SitemapService;

class SeoSettingsController extends Controller
{
    private array $modules = [
        'meta' => 'Meta Tags',
        'sitemaps' => 'Sitemap',
        'schema' => 'Schema',
        'redirections' => 'Redirects',
        'image_seo' => 'Image SEO',
        'minify_html' => 'Minification',
    ];

    public function index()
    {
        $settings = SeoSetting::whereIn('key', $this->settingKeys())->pluck('value', 'key')->all();
        $modules = $this->modules;
        $moduleLinks = $this->moduleLinks();
        $moduleValues = [];
        $sitemapService = app(SitemapService::class);
        $sitemapFilename = $sitemapService->baseFilename();
        $sitemapUrl = url($sitemapFilename);
        $sitemapUrlsPerFile = $sitemapService->urlsPerFile();
        $sitemapChildPattern = $sitemapService->childPattern();
        $sitemapPageFilenames = $sitemapService->pageFilenames();
        $generatedPageCount = SeoGeneratedPage::count();
        $generatedPages = SeoGeneratedPage::query()
            ->latest('id')
            ->select(['id', 'url_slug', 'final_title', 'updated_at'])
            ->take(100)
            ->get();

        foreach ($modules as $key => $label) {
            $settingKey = $this->settingKey($key);
            $moduleValues[$key] = array_key_exists($settingKey, $settings)
                ? filter_var($settings[$settingKey], FILTER_VALIDATE_BOOLEAN)
                : (bool) config("seo.modules.{$key}", false);
        }

        return view('seo::admin.settings', compact(
            'modules',
            'moduleValues',
            'moduleLinks',
            'sitemapFilename',
            'sitemapUrl',
            'sitemapUrlsPerFile',
            'sitemapChildPattern',
            'sitemapPageFilenames',
            'generatedPageCount',
            'generatedPages'
        ));
    }

    public function update(Request $request)
    {
        $enabledModules = $request->input('modules', []);

        foreach ($this->modules as $key => $label) {
            SeoSetting::updateOrCreate(
                ['key' => $this->settingKey($key)],
                ['value' => in_array($key, $enabledModules, true) ? '1' : '0']
            );
        }

        $requestedSitemapFilename = $request->input('sitemap_filename');

        if (is_string($requestedSitemapFilename) && trim($requestedSitemapFilename) !== '') {
            SeoSetting::updateOrCreate(
                ['key' => 'sitemap.filename'],
                ['value' => $this->sitemapFilename($requestedSitemapFilename)]
            );
        }

        SeoSetting::updateOrCreate(
            ['key' => 'sitemap.urls_per_file'],
            ['value' => (string) $this->urlsPerFile($request->input('sitemap_urls_per_file', 1000))]
        );

        SeoSetting::updateOrCreate(
            ['key' => 'sitemap.child_pattern'],
            ['value' => $this->childPattern($request->input('sitemap_child_pattern', '{base}-{page}.xml'))]
        );

        return redirect('admin/seo-admin/settings')->with('status', 'SEO settings saved successfully.');
    }

    private function settingKey(string $module): string
    {
        return "modules.{$module}";
    }

    private function settingKeys(): array
    {
        return array_map(fn ($key) => $this->settingKey($key), array_keys($this->modules));
    }

    private function sitemapFilename(?string $filename): string
    {
        $baseName = pathinfo((string) $filename, PATHINFO_FILENAME);
        $baseName = Str::slug($baseName ?: 'sitemap');

        return ($baseName ?: 'sitemap') . '.xml';
    }

    private function urlsPerFile($value): int
    {
        return max(1, min((int) $value, 50000));
    }

    private function childPattern($pattern): string
    {
        $pattern = trim((string) $pattern);

        return str_contains($pattern, '{page}') ? $pattern : '{base}-{page}.xml';
    }

    private function moduleLinks(): array
    {
        return [
            'PageForge' => route('seo.generator'),
            'Redirects' => Route::has('seo.redirects') ? route('seo.redirects') : '#',
            'Meta Tags' => Route::has('seo.meta') ? route('seo.meta') : '#',
            'Sitemap' => Route::has('seo.sitemap') ? route('seo.sitemap') : '#',
            'Schema' => Route::has('seo.schema') ? route('seo.schema') : '#',
            'Reports' => Route::has('seo.reports') ? route('seo.reports') : '#',
        ];
    }
}
