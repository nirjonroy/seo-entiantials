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
    public function index()
    {
        $sitemapService = new SitemapService();
        $xml = $sitemapService->generateXml();

        return response($xml)->header('Content-Type', 'text/xml');
    }
}
