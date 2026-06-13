<?php

namespace Nirjon\LaravelSeo\Services;

use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoTemplate;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;

class PageGenerationEngine
{
    /**
     * Dynamically calculates the N-dimensional Cartesian product for unlimited arrays
     * and returns all combinations.
     *
     * @param array $arrays Associative array of arrays (e.g., ['product' => ['iPhone', 'Samsung'], 'city' => ['London']])
     * @return array
     */
    private function getCartesianProduct(array $arrays)
    {
        if (empty($arrays)) {
            return [];
        }

        $result = [[]];

        foreach ($arrays as $key => $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    /**
     * Generates pages based on an SEO Template and Cartesian Product of its keyword bundles.
     *
     * @param int $templateId
     * @return int Number of generated pages
     */
    public function generatePages($templateId)
    {
        // Find the template by ID and fetch its attached bundles and their keywords
        $template = SeoTemplate::with('bundles.keywords')->findOrFail($templateId);

        // Build arrays by bundle position so UI placeholders like {0}, {1} resolve correctly.
        $keywordArrays = [];
        $bundleSlugs = [];
        foreach ($template->bundles->values() as $index => $bundle) {
            $keywordArrays[$index] = $bundle->keywords->pluck('keyword')->toArray();
            $bundleSlugs[$index] = $bundle->slug;
        }

        if (empty($keywordArrays)) {
            return 0;
        }

        // Get all combinations
        $combinations = $this->getCartesianProduct($keywordArrays);

        $generatedCount = 0;
        $existingGeneratedPageCount = SeoGeneratedPage::count();

        foreach ($combinations as $combination) {
            $title = $template->title_structure ?? '';
            $slug = $template->slug_structure ?? '';
            $content = $template->content ?? '';
            $metaTitle = $template->meta_title ?? '';
            $metaDescription = $template->meta_description ?? '';
            $metaKeywords = $template->meta_keywords ?? '';
            $featuredImage = $template->featured_image ?? '';

            // Replace both current numeric placeholders ({0}, {1}) and legacy bundle-slug placeholders.
            foreach ($combination as $bundleIndex => $keyword) {
                $placeholders = [
                    '{' . $bundleIndex . '}',
                ];

                if (isset($bundleSlugs[$bundleIndex])) {
                    $placeholders[] = '{' . $bundleSlugs[$bundleIndex] . '}';
                }

                foreach ($placeholders as $placeholder) {
                    $title = str_replace($placeholder, $keyword, $title);
                    $slug = str_replace($placeholder, $keyword, $slug);
                    $content = str_replace($placeholder, $keyword, $content);
                    $metaTitle = str_replace($placeholder, $keyword, $metaTitle);
                    $metaDescription = str_replace($placeholder, $keyword, $metaDescription);
                    $metaKeywords = str_replace($placeholder, $keyword, $metaKeywords);
                    $featuredImage = str_replace($placeholder, $keyword, $featuredImage);
                }
            }

            $title = $this->parseSpintax($title);
            $slug = $this->parseSpintax($slug);
            $content = $this->parseSpintax($content);
            $metaTitle = $this->parseSpintax($metaTitle);
            $metaDescription = $this->parseSpintax($metaDescription);
            $metaKeywords = $this->parseSpintax($metaKeywords);
            $featuredImage = $this->parseSpintax($featuredImage);

            // Generate clean URL slug
            $urlSlug = Str::slug($slug);

            if (empty($urlSlug)) {
                continue;
            }

            SeoGeneratedPage::create([
                'template_id' => $template->id,
                'url_slug' => $this->uniqueGeneratedSlug($urlSlug),
                'final_title' => $title,
                'final_content' => $content,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
                'featured_image' => $featuredImage,
            ]);

            $generatedCount++;
        }

        $finalGeneratedPageCount = SeoGeneratedPage::count();
        $expectedMinimumCount = $existingGeneratedPageCount + $generatedCount;

        if ($finalGeneratedPageCount < $expectedMinimumCount) {
            throw new \RuntimeException('PageForge generation was stopped because existing generated pages would be removed.');
        }

        return $generatedCount;
    }

    /**
     * Parses standard and nested spintax.
     *
     * @param string $text
     * @return string
     */
    private function parseSpintax($text)
    {
        if (!is_string($text)) {
            return $text;
        }

        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            function ($matches) {
                $parts = explode('|', $matches[1]);
                return $parts[array_rand($parts)];
            },
            $text
        );
    }

    private function uniqueGeneratedSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while (SeoGeneratedPage::where('url_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
