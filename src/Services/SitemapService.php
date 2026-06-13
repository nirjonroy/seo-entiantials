<?php

namespace Nirjon\LaravelSeo\Services;

use Carbon\Carbon;
use DateTimeInterface;
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
        ob_start();
        $this->streamXml();

        return (string) ob_get_clean();
    }

    /**
     * Stream the XML sitemap in chunks.
     *
     * @return void
     */
    public function streamXml(): void
    {
        $changeFrequency = config('seo.sitemap.change_frequency', 'weekly');
        $defaultPriority = config('seo.sitemap.default_priority', '0.8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add the homepage
        echo $this->xmlUrl(url('/'), $changeFrequency, $defaultPriority);

        // Loop through Eloquent models
        $models = config('seo.sitemap.models', []);
        foreach ($models as $modelClass) {
            if (! class_exists($modelClass) || ! method_exists($modelClass, 'query')) {
                continue;
            }

            try {
                $modelClass::query()->chunkById(500, function ($records) {
                    foreach ($records as $record) {
                        try {
                            if (method_exists($record, 'getSitemapUrl')) {
                                $url = $record->getSitemapUrl();

                                if (is_string($url) && $url !== '') {
                                    echo $this->xmlUrl($url, 'weekly', '0.6', $record->updated_at ?? null);
                                }
                            }
                        } catch (\Throwable $exception) {
                            continue;
                        }
                    }
                });
            } catch (\Throwable $exception) {
                continue;
            }
        }

        SeoGeneratedPage::query()
            ->select(['id', 'url_slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($generatedPages) {
                foreach ($generatedPages as $page) {
                    try {
                        if (! is_string($page->url_slug) || $page->url_slug === '') {
                            continue;
                        }

                        echo $this->xmlUrl(
                            url('/' . ltrim($page->url_slug, '/')),
                            'weekly',
                            '0.8',
                            $page->updated_at
                        );
                    } catch (\Throwable $exception) {
                        continue;
                    }
                }
            }, 'id');

        echo '</urlset>';
    }

    private function xmlUrl(string $url, string $changeFrequency, string $priority, $lastModified = null): string
    {
        $xml = "    <url>\n";
        $xml .= "        <loc>" . $this->xmlEscape($url) . "</loc>\n";

        $lastModified = $this->formatLastModified($lastModified);

        if ($lastModified !== null) {
            $xml .= "        <lastmod>" . $this->xmlEscape($lastModified) . "</lastmod>\n";
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

    private function formatLastModified($lastModified): ?string
    {
        if ($lastModified instanceof DateTimeInterface) {
            return $lastModified->format(DateTimeInterface::ATOM);
        }

        if (is_string($lastModified) && trim($lastModified) !== '') {
            try {
                return Carbon::parse($lastModified)->toAtomString();
            } catch (\Throwable $exception) {
                return null;
            }
        }

        return null;
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
