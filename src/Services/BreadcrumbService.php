<?php

namespace Nirjon\LaravelSeo\Services;

class BreadcrumbService
{
    protected $links = [];

    public function __construct()
    {
        if (config('seo.breadcrumbs.enabled')) {
            $this->add(config('seo.breadcrumbs.home_label', 'Home'), url('/'));
        }
    }

    public function add($label, $url = null)
    {
        $this->links[] = [
            'label' => $label,
            'url' => $url
        ];

        return $this;
    }

    public function generateHtml()
    {
        if (empty($this->links)) {
            return '';
        }

        $separator = config('seo.breadcrumbs.separator', '»');
        $html = '<nav aria-label="breadcrumb">' . PHP_EOL;
        $html .= '    <ol>' . PHP_EOL;

        $count = count($this->links);
        foreach ($this->links as $index => $link) {
            $html .= '        <li>' . PHP_EOL;
            
            if ($link['url'] && $index < $count - 1) {
                $html .= '            <a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['label']) . '</a> ' . htmlspecialchars($separator) . PHP_EOL;
            } else {
                $html .= '            <span aria-current="page">' . htmlspecialchars($link['label']) . '</span>' . PHP_EOL;
            }
            
            $html .= '        </li>' . PHP_EOL;
        }

        $html .= '    </ol>' . PHP_EOL;
        $html .= '</nav>' . PHP_EOL;

        return $html;
    }

    public function generateSchema()
    {
        if (!config('seo.breadcrumbs.generate_schema') || empty($this->links)) {
            return '';
        }

        $itemListElement = [];
        $position = 1;

        foreach ($this->links as $link) {
            $item = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $link['label'],
            ];

            if ($link['url']) {
                $item['item'] = $link['url'];
            }

            $itemListElement[] = $item;
            $position++;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];

        return '<script type="application/ld+json">' . PHP_EOL . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL . '</script>' . PHP_EOL;
    }

    public function render()
    {
        return $this->generateHtml() . PHP_EOL . $this->generateSchema();
    }
}
