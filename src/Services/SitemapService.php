<?php

namespace Nirjon\LaravelSeo\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;
use Nirjon\LaravelSeo\Models\SeoSetting;

class SitemapService
{
    public function generateXml(?string $requestedFilename = null): string
    {
        ob_start();
        $this->streamXml($requestedFilename);

        return (string) ob_get_clean();
    }

    public function streamXml(?string $requestedFilename = null): void
    {
        $requestedFilename = $this->sanitizeFilename($requestedFilename ?: $this->baseFilename());
        $totalUrls = $this->totalUrlCount();
        $urlsPerFile = $this->urlsPerFile();

        if ($totalUrls > $urlsPerFile) {
            $pageNumber = $this->pageNumberForFilename($requestedFilename);

            if ($this->isIndexFilename($requestedFilename)) {
                $this->streamIndex($totalUrls);
                return;
            }

            if ($pageNumber !== null) {
                $this->streamUrlset($pageNumber);
                return;
            }
        }

        $this->streamUrlset(1);
    }

    public function canServe(?string $requestedFilename = null): bool
    {
        $requestedFilename = $this->sanitizeFilename($requestedFilename ?: $this->baseFilename());

        if ($this->isIndexFilename($requestedFilename)) {
            return true;
        }

        if ($this->totalUrlCount() <= $this->urlsPerFile()) {
            return false;
        }

        return $this->pageNumberForFilename($requestedFilename) !== null;
    }

    public function baseFilename(): string
    {
        return $this->storedFilename('sitemap.filename', config('seo.sitemap.filename', 'sitemap.xml'));
    }

    public function urlsPerFile(): int
    {
        $value = (int) $this->storedValue('sitemap.urls_per_file', config('seo.sitemap.urls_per_file', 1000));

        return max(1, min($value, 50000));
    }

    public function childPattern(): string
    {
        $pattern = (string) $this->storedValue(
            'sitemap.child_pattern',
            config('seo.sitemap.child_pattern', '{base}-{page}.xml')
        );

        return str_contains($pattern, '{page}') ? $pattern : '{base}-{page}.xml';
    }

    public function pageFilenames(): array
    {
        $pageCount = $this->pageCount();

        if ($pageCount <= 1) {
            return [$this->baseFilename()];
        }

        return array_map(fn ($page) => $this->childFilename($page), range(1, $pageCount));
    }

    public function pageCount(): int
    {
        return (int) ceil($this->totalUrlCount() / $this->urlsPerFile());
    }

    public function totalUrlCount(): int
    {
        $count = 1;

        foreach (config('seo.sitemap.models', []) as $modelClass) {
            if (! class_exists($modelClass) || ! method_exists($modelClass, 'query')) {
                continue;
            }

            try {
                $count += (int) $modelClass::query()->count();
            } catch (\Throwable $exception) {
                continue;
            }
        }

        try {
            if (Schema::hasTable('nirjon_seo_generated_pages')) {
                $count += SeoGeneratedPage::count();
            }
        } catch (\Throwable $exception) {
            //
        }

        return $count;
    }

    private function streamIndex(int $totalUrls): void
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($this->pageFilenames() as $filename) {
            echo "    <sitemap>\n";
            echo "        <loc>" . $this->xmlEscape(url($filename)) . "</loc>\n";
            echo "        <lastmod>" . $this->xmlEscape(now()->toAtomString()) . "</lastmod>\n";
            echo "    </sitemap>\n";
        }

        echo '</sitemapindex>';
    }

    private function streamUrlset(int $pageNumber): void
    {
        $changeFrequency = config('seo.sitemap.change_frequency', 'weekly');
        $defaultPriority = (string) config('seo.sitemap.default_priority', '0.1');
        $urlsPerFile = $this->urlsPerFile();
        $offset = max(0, ($pageNumber - 1) * $urlsPerFile);
        $remaining = $urlsPerFile;

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if ($offset === 0 && $remaining > 0) {
            echo $this->xmlUrl(url('/'), $changeFrequency, $defaultPriority);
            $remaining--;
        } else {
            $offset = max(0, $offset - 1);
        }

        foreach (config('seo.sitemap.models', []) as $modelClass) {
            if ($remaining <= 0) {
                break;
            }

            if (! class_exists($modelClass) || ! method_exists($modelClass, 'query')) {
                continue;
            }

            try {
                $modelCount = (int) $modelClass::query()->count();

                if ($offset >= $modelCount) {
                    $offset -= $modelCount;
                    continue;
                }

                $modelClass::query()
                    ->skip($offset)
                    ->take($remaining)
                    ->get()
                    ->each(function ($record) use (&$remaining) {
                        if ($remaining <= 0) {
                            return;
                        }

                        try {
                            if (method_exists($record, 'getSitemapUrl')) {
                                $url = $record->getSitemapUrl();

                                if (is_string($url) && $url !== '') {
                                    echo $this->xmlUrl($url, 'weekly', '0.1', $record->updated_at ?? null);
                                    $remaining--;
                                }
                            }
                        } catch (\Throwable $exception) {
                            //
                        }
                    });

                $offset = 0;
            } catch (\Throwable $exception) {
                continue;
            }
        }

        if ($remaining > 0) {
            $this->streamGeneratedPages($offset, $remaining);
        }

        echo '</urlset>';
    }

    private function streamGeneratedPages(int $offset, int &$remaining): void
    {
        try {
            SeoGeneratedPage::query()
                ->select(['id', 'url_slug', 'updated_at'])
                ->orderBy('id')
                ->skip($offset)
                ->take($remaining)
                ->get()
                ->each(function ($page) use (&$remaining) {
                    if ($remaining <= 0) {
                        return;
                    }

                    try {
                        if (! is_string($page->url_slug) || $page->url_slug === '') {
                            return;
                        }

                        echo $this->xmlUrl(
                            url('/' . ltrim($page->url_slug, '/')),
                            'weekly',
                            '0.1',
                            $page->updated_at
                        );
                        $remaining--;
                    } catch (\Throwable $exception) {
                        //
                    }
                });
        } catch (\Throwable $exception) {
            //
        }
    }

    private function pageNumberForFilename(string $filename): ?int
    {
        foreach ($this->pageFilenames() as $index => $pageFilename) {
            if ($filename === $pageFilename) {
                return $index + 1;
            }
        }

        return null;
    }

    private function childFilename(int $page): string
    {
        $baseName = pathinfo($this->baseFilename(), PATHINFO_FILENAME);
        $filename = str_replace(['{base}', '{page}'], [$baseName, (string) $page], $this->childPattern());

        return $this->sanitizeFilename($filename);
    }

    private function isIndexFilename(string $filename): bool
    {
        return in_array($filename, ['sitemap.xml', $this->baseFilename()], true);
    }

    private function storedFilename(string $key, $default): string
    {
        return $this->sanitizeFilename((string) $this->storedValue($key, $default));
    }

    private function storedValue(string $key, $default)
    {
        try {
            if (Schema::hasTable('nirjon_seo_settings')) {
                $value = SeoSetting::where('key', $key)->value('value');

                if (is_string($value) && trim($value) !== '') {
                    return $value;
                }
            }
        } catch (\Throwable $exception) {
            //
        }

        return $default;
    }

    private function sanitizeFilename(string $filename): string
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $baseName = Str::slug($baseName ?: 'sitemap');

        return ($baseName ?: 'sitemap') . '.xml';
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

    public function generateHtml(): string
    {
        $html = '<ul class="seo-html-sitemap">' . "\n";
        $html .= '    <li><a href="' . url('/') . '">Home</a></li>' . "\n";

        foreach (config('seo.sitemap.models', []) as $modelClass) {
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
