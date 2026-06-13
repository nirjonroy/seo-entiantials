<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoSetting;
use Nirjon\LaravelSeo\Services\SitemapService;

class SitemapController extends Controller
{
    /**
     * Display the XML sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(?string $sitemapFile = 'sitemap.xml')
    {
        $configuredFile = $this->configuredFilename();
        $requestedFile = $sitemapFile ?: 'sitemap.xml';

        if (! in_array($requestedFile, ['sitemap.xml', $configuredFile], true)) {
            abort(404);
        }

        return response()->stream(function () {
            app(SitemapService::class)->streamXml();
        }, 200, ['Content-Type' => 'text/xml; charset=UTF-8']);
    }

    private function configuredFilename(): string
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
