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

        return response()->stream(function () use ($sitemapService, $requestedFile) {
            $sitemapService->streamXml($requestedFile);
        }, 200, ['Content-Type' => 'text/xml; charset=UTF-8']);
    }
}
