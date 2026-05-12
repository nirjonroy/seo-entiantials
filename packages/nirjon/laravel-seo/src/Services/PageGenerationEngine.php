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

        // Build an associative array of bundle slugs to keywords
        $keywordArrays = [];
        foreach ($template->bundles as $bundle) {
            $keywordArrays[$bundle->slug] = $bundle->keywords->pluck('keyword')->toArray();
        }

        if (empty($keywordArrays)) {
            return 0;
        }

        // Get all combinations
        $combinations = $this->getCartesianProduct($keywordArrays);

        $generatedCount = 0;

        foreach ($combinations as $combination) {
            $title = $template->title_structure ?? '';
            $slug = $template->slug_structure ?? '';
            $content = $template->content ?? '';

            // Replace {bundle_slug} placeholders
            foreach ($combination as $bundleSlug => $keyword) {
                $placeholder = '{' . $bundleSlug . '}';
                $title = str_replace($placeholder, $keyword, $title);
                $slug = str_replace($placeholder, $keyword, $slug);
                $content = str_replace($placeholder, $keyword, $content);
            }

            // Generate clean URL slug
            $urlSlug = Str::slug($slug);

            if (empty($urlSlug)) {
                continue;
            }

            // Save the generated page
            SeoGeneratedPage::updateOrCreate(
                ['url_slug' => $urlSlug],
                [
                    'template_id' => $template->id,
                    'final_title' => $title,
                    'final_content' => $content,
                ]
            );

            $generatedCount++;
        }

        return $generatedCount;
    }
}
