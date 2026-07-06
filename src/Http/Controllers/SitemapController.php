<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
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
        $sitemapService = app(SitemapService::class);
        $requestedFile = $sitemapFile ?: $sitemapService->baseFilename();

        if (! $sitemapService->canServe($requestedFile)) {
            abort(404);
        }

        return response($sitemapService->generateXml($requestedFile), 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
