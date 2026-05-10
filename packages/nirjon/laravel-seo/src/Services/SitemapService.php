<?php

namespace Nirjon\LaravelSeo\Services;

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
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . url('/') . "</loc>\n";
        $xml .= "        <changefreq>" . htmlspecialchars($changeFrequency) . "</changefreq>\n";
        $xml .= "        <priority>" . htmlspecialchars($defaultPriority) . "</priority>\n";
        $xml .= "    </url>\n";

        // Loop through Eloquent models
        $models = config('seo.sitemap.models', []);
        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                $records = $modelClass::all();
                foreach ($records as $record) {
                    if (method_exists($record, 'getSitemapUrl')) {
                        $url = $record->getSitemapUrl();
                        $xml .= "    <url>\n";
                        $xml .= "        <loc>" . htmlspecialchars($url) . "</loc>\n";
                        $xml .= "        <changefreq>weekly</changefreq>\n";
                        $xml .= "        <priority>0.6</priority>\n";
                        $xml .= "    </url>\n";
                    }
                }
            }
        }

        $xml .= '</urlset>';

        return $xml;
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

        $html .= '</ul>';

        return $html;
    }
}
