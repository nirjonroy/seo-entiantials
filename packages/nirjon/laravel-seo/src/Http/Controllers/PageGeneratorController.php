<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoTemplate;
use Nirjon\LaravelSeo\Models\SeoKeywordBundle;
use Nirjon\LaravelSeo\Models\SeoKeyword;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;

class PageGeneratorController extends Controller
{
    /**
     * Show the generator admin UI.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('seo::admin.generator');
    }

    /**
     * Get generated pages for the UI.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetPages()
    {
        $pages = SeoGeneratedPage::latest()->take(100)->get();

        return response()->json($pages);
    }

    /**
     * Generate pages from a template and two keyword bundles.
     */
    public function apiGenerate(Request $request)
    {
        $bundle1Values = array_values(array_filter(array_map(
            fn ($keyword) => trim($keyword),
            explode(',', (string) $request->input('keyword_bundle_1', $request->input('bundle1', '')))
        )));

        $bundle2Values = array_values(array_filter(array_map(
            fn ($keyword) => trim($keyword),
            explode(',', (string) $request->input('keyword_bundle_2', $request->input('bundle2', '')))
        )));

        if (empty($bundle1Values) || empty($bundle2Values)) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword Bundle 1 and Keyword Bundle 2 are required.'
            ], 422);
        }

        $generatedCount = DB::transaction(function () use ($request, $bundle1Values, $bundle2Values) {
            $metaImage = $request->input('metaImage', '');

            if ($request->hasFile('featured_image')) {
                $metaImage = $request->file('featured_image')->store('seo-images', 'public');
            }

            $templateTitle = (string) $request->input('title', '');
            $templateSlug = (string) $request->input('slug', '');
            $templateContent = (string) $request->input('content', '');
            $templateMetaTitle = (string) $request->input('metaTitle', '');
            $templateMetaDescription = (string) $request->input('metaDescription', '');
            $templateMetaKeywords = (string) $request->input('metaKeywords', '');

            $template = SeoTemplate::updateOrCreate(
                ['slug_structure' => $templateSlug],
                [
                    'name' => 'PageForge Live Template - ' . $templateTitle,
                    'title_structure' => $templateTitle,
                    'content' => $templateContent,
                    'meta_title' => $templateMetaTitle,
                    'meta_description' => $templateMetaDescription,
                    'meta_keywords' => $templateMetaKeywords,
                    'featured_image' => $metaImage,
                    'meta_image' => $metaImage,
                    'author' => $request->input('author', ''),
                    'publisher' => $request->input('publisher', ''),
                    'copyright' => $request->input('copyright', ''),
                    'site_name' => $request->input('siteName', ''),
                ]
            );

            $bundleIds = [
                $this->syncKeywordBundle('Bundle 1', '0', $bundle1Values)->id,
                $this->syncKeywordBundle('Bundle 2', '1', $bundle2Values)->id,
            ];

            $template->bundles()->sync($bundleIds);
            $template->generatedPages()->delete();

            $placeholders = ['{0}', '{1}'];
            $generatedCount = 0;

            foreach ($bundle1Values as $keyword1) {
                foreach ($bundle2Values as $keyword2) {
                    $replacements = [$keyword1, $keyword2];

                    $title = $this->parseSpintax(str_replace($placeholders, $replacements, $templateTitle));
                    $slug = $this->parseSpintax(str_replace($placeholders, $replacements, $templateSlug));
                    $content = $this->normalizeGeneratedContent(
                        $this->parseSpintax(str_replace($placeholders, $replacements, $templateContent))
                    );
                    $metaTitle = $this->parseSpintax(str_replace($placeholders, $replacements, $templateMetaTitle));
                    $metaDescription = $this->parseSpintax(str_replace($placeholders, $replacements, $templateMetaDescription));
                    $metaKeywords = $this->parseSpintax(str_replace($placeholders, $replacements, $templateMetaKeywords));
                    $urlSlug = Str::slug($slug);

                    if (empty($urlSlug)) {
                        continue;
                    }

                    SeoGeneratedPage::create([
                        'template_id' => $template->id,
                        'url_slug' => $urlSlug,
                        'final_title' => $title,
                        'final_content' => $content,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => $metaKeywords,
                        'featured_image' => $metaImage,
                    ]);

                    $generatedCount++;
                }
            }

            return $generatedCount;
        });

        return response()->json([
            'success' => true,
            'message' => $generatedCount . ' generated page(s) created successfully.'
        ]);
    }

    public function destroy(SeoGeneratedPage $page)
    {
        $page->delete();

        return response()->json(['success' => true]);
    }

    private function keywordsFromInput(Request $request, int $bundleIndex, string $primaryInputName, string $fallbackInputName): array
    {
        $rawInput = (string) $request->input($primaryInputName, $request->input($fallbackInputName, ''));

        if ($rawInput !== '') {
            return $this->csvToArray($rawInput);
        }

        $bundlesInput = $request->input('bundles');
        $bundles = is_string($bundlesInput) ? json_decode($bundlesInput, true) : [];

        if (!is_array($bundles) || !isset($bundles[$bundleIndex]['keywords']) || !is_array($bundles[$bundleIndex]['keywords'])) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($keyword) => trim((string) $keyword),
            $bundles[$bundleIndex]['keywords']
        )));
    }

    private function csvToArray(string $value): array
    {
        return array_values(array_filter(array_map(
            fn ($item) => trim($item),
            explode(',', $value)
        )));
    }

    private function syncKeywordBundle(string $name, string $slug, array $keywords): SeoKeywordBundle
    {
        $bundle = SeoKeywordBundle::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'is_active' => true]
        );

        $bundle->keywords()->delete();

        foreach ($keywords as $keyword) {
            SeoKeyword::create([
                'bundle_id' => $bundle->id,
                'keyword' => $keyword,
            ]);
        }

        return $bundle;
    }

    private function parseSpintax(string $text): string
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            function ($matches) {
                $parts = explode('|', $matches[1]);
                return $parts[array_rand($parts)];
            },
            $text
        );
    }

    private function normalizeGeneratedContent(string $content): string
    {
        return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
