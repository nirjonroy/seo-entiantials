<?php

namespace Nirjon\LaravelSeo\Services;

use Nirjon\LaravelSeo\Models\SeoGeneratedPage;

class SitemapService
{
    /**
     * Generate the XML sitemap string.
     *
     * @return string
     */
    public function generateXml(): string
    {
        $changeFrequency = config('seo.sitemap.change_frequency', 'weekly');
        $defaultPriority = config('seo.sitemap.default_priority', '0.8');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add the homepage
        $xml .= $this->xmlUrl(url('/'), $changeFrequency, $defaultPriority);

        // Loop through Eloquent models
        $models = config('seo.sitemap.models', []);
        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                $records = $modelClass::all();
                foreach ($records as $record) {
                    if (method_exists($record, 'getSitemapUrl')) {
                        $url = $record->getSitemapUrl();
                        $xml .= $this->xmlUrl($url, 'weekly', '0.6', $record->updated_at ?? null);
                    }
                }
            }
        }

        SeoGeneratedPage::query()
            ->select(['url_slug', 'updated_at'])
            ->orderBy('id')
            ->chunk(500, function ($generatedPages) use (&$xml) {
                foreach ($generatedPages as $page) {
                    $xml .= $this->xmlUrl(
                        url('/' . ltrim($page->url_slug, '/')),
                        'weekly',
                        '0.8',
                        $page->updated_at
                    );
                }
            });

        $xml .= '</urlset>';

        return $xml;
    }

    private function xmlUrl(string $url, string $changeFrequency, string $priority, $lastModified = null): string
    {
        $xml = "    <url>\n";
        $xml .= "        <loc>" . $this->xmlEscape($url) . "</loc>\n";

        if ($lastModified) {
            $xml .= "        <lastmod>" . $this->xmlEscape($lastModified->toAtomString()) . "</lastmod>\n";
        }

        $xml .= "        <changefreq>" . $this->xmlEscape($changeFrequency) . "</changefreq>\n";
        $xml .= "        <priority>" . $this->xmlEscape($priority) . "</priority>\n";
        $xml .= "    </url>\n";

        return $xml;
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * Generate the HTML sitemap string.
     *
     * @return string
     */
    public function generateHtml(): string
    {
        $html = '<ul class="seo-html-sitemap">' . "\n";
        $html .= '    <li><a href="' . url('/') . '">Home</a></li>' . "\n";

        // Loop through Eloquent models
        $models = config('seo.sitemap.models', []);
        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                $records = $modelClass::all();
                foreach ($records as $record) {
                    if (method_exists($record, 'getSitemapUrl')) {
                        $title = $record->title ?? $record->name ?? null;
                        if ($title) {
                            $url = $record->getSitemapUrl();
                            $html .= '    <li><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a></li>' . "\n";
                        }
                    }
                }
            }
        }

        SeoGeneratedPage::query()
            ->select(['url_slug', 'final_title'])
            ->orderBy('final_title')
            ->chunk(500, function ($generatedPages) use (&$html) {
                foreach ($generatedPages as $page) {
                    $title = $page->final_title ?: $page->url_slug;
                    $html .= '    <li><a href="' . htmlspecialchars(url($page->url_slug)) . '">' . htmlspecialchars($title) . '</a></li>' . "\n";
                }
            });

        $html .= '</ul>';

        return $html;
    }
}
