<?php

namespace Nirjon\LaravelSeo\Services;

class SEOMetaService
{
    /**
     * Generate HTML meta tags based on the model's SEO data or fallback to configuration defaults.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return array
     */
    public function generateTags($model = null): array
    {
        $seo = $model && method_exists($model, 'seo') ? $model->seo : null;
        $defaults = config('seo.defaults', []);

        $siteName = $defaults['site_name'] ?? 'My Site';
        $separator = $defaults['title_separator'] ?? '|';

        // Determine title
        $title = $seo->title ?? $siteName;
        if ($seo && $seo->title) {
            $title = "{$seo->title} {$separator} {$siteName}";
        }

        // Determine other attributes
        $description = $seo->description ?? '';
        $keywords = $seo->keywords ?? '';
        $author = $seo->author ?? ($defaults['author'] ?? '');
        $robots = $seo->robots_tag ?? 'index, follow';
        $canonical = url()->current();

        $tags = [];

        // Title tag
        $tags[] = '<title>' . e($title) . '</title>';

        // Description meta tag
        if (!empty($description)) {
            $tags[] = '<meta name="description" content="' . e($description) . '">';
        }

        // Keywords meta tag
        if (!empty($keywords)) {
            $tags[] = '<meta name="keywords" content="' . e($keywords) . '">';
        }

        // Author meta tag
        if (!empty($author)) {
            $tags[] = '<meta name="author" content="' . e($author) . '">';
        }

        // Robots meta tag
        $tags[] = '<meta name="robots" content="' . e($robots) . '">';

        // Canonical link tag
        $tags[] = '<link rel="canonical" href="' . e($canonical) . '">';

        // Open Graph tags
        $tags[] = '<meta property="og:type" content="website">';
        
        if (!empty($title)) {
            $tags[] = '<meta property="og:title" content="' . e($title) . '">';
        }
        
        if (!empty($canonical)) {
            $tags[] = '<meta property="og:url" content="' . e($canonical) . '">';
        }

        if (!empty($siteName)) {
            $tags[] = '<meta property="og:site_name" content="' . e($siteName) . '">';
        }
        
        if (!empty($description)) {
            $tags[] = '<meta property="og:description" content="' . e($description) . '">';
        }

        $image = $seo->image ?? '';
        if (!empty($image)) {
            $tags[] = '<meta property="og:image" content="' . e($image) . '">';
        }

        // Twitter Card tags
        $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        
        if (!empty($title)) {
            $tags[] = '<meta name="twitter:title" content="' . e($title) . '">';
        }

        if (!empty($description)) {
            $tags[] = '<meta name="twitter:description" content="' . e($description) . '">';
        }

        if (!empty($image)) {
            $tags[] = '<meta name="twitter:image" content="' . e($image) . '">';
        }

        return $tags;
    }
}
