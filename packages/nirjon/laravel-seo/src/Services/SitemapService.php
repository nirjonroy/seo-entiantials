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

        // TODO: Loop through Eloquent models and other static URLs later

        $xml .= '</urlset>';

        return $xml;
    }
}
