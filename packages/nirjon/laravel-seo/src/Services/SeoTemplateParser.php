<?php

namespace Nirjon\LaravelSeo\Services;

class SeoTemplateParser
{
    public function parse($template, $model = null)
    {
        $siteName = config('seo.fallbacks.site_name');
        $separator = config('seo.fallbacks.separator');

        $replacements = [
            '{site_name}' => $siteName,
            '{sep}'       => $separator,
        ];

        if ($model !== null) {
            $replacements['{title}'] = $model->title ?? $model->name ?? $model->post_title ?? 'Page';
        }

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
