<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoSetting;

class SeoFilesController extends Controller
{
    public function robots()
    {
        $content = config('seo.files.robots_txt', '');

        if ($content === '' || str_contains($content, '/sitemap.xml')) {
            $content = "User-agent: *\nAllow: /\nSitemap: " . url($this->sitemapFilename()) . "\n";
        }

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    public function llms()
{
    $content = config('seo.files.llms_txt');
    
    return response($content, 200)->header('Content-Type', 'text/plain');
}

    public function security()
    {
        return response(config('seo.files.security_txt', ''), 200, ['Content-Type' => 'text/plain']);
    }

    private function sitemapFilename(): string
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
