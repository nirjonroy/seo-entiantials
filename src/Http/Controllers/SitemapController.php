<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
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

        $sitemapService = new SitemapService();
        $xml = $sitemapService->generateXml();

        return response($xml)->header('Content-Type', 'text/xml');
    }

    private function configuredFilename(): string
    {
        $filename = (string) config('seo.sitemap.filename', 'sitemap.xml');
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $baseName = Str::slug($baseName ?: 'sitemap');

        return ($baseName ?: 'sitemap') . '.xml';
    }
}
