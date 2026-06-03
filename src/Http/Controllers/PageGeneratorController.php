<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;
use Nirjon\LaravelSeo\Models\SeoKeyword;
use Nirjon\LaravelSeo\Models\SeoKeywordBundle;
use Nirjon\LaravelSeo\Models\SeoTemplate;

class PageGeneratorController extends Controller
{
    public function index()
    {
        return view('seo::admin.generator');
    }

    public function apiGetPages()
    {
        return response()->json(SeoGeneratedPage::latest()->take(100)->get());
    }

    public function apiShowPage($id)
    {
        $page = SeoGeneratedPage::with('template.bundles.keywords')->findOrFail($id);
        $template = $page->template;
        $bundle1 = '';
        $bundle2 = '';

        if ($template) {
            $bundles = $template->bundles->values();
            $bundle1Model = $bundles->get(0);
            $bundle2Model = $bundles->get(1);
            $bundle1 = $bundle1Model ? $bundle1Model->keywords->pluck('keyword')->implode(', ') : '';
            $bundle2 = $bundle2Model ? $bundle2Model->keywords->pluck('keyword')->implode(', ') : '';
        }

        return response()->json([
            'page' => $page,
            'template' => $template,
            'keyword_bundle_1' => $bundle1,
            'keyword_bundle_2' => $bundle2,
        ]);
    }

    public function apiGenerate(Request $request)
    {
        $bundle1Values = $this->keywordsFromInput($request, 0, 'keyword_bundle_1', 'bundle1');
        $bundle2Values = $this->keywordsFromInput($request, 1, 'keyword_bundle_2', 'bundle2');

        if (empty($bundle1Values) || empty($bundle2Values)) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword Bundle 1 and Keyword Bundle 2 are required.',
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
            $existingTemplate = SeoTemplate::where('slug_structure', $templateSlug)->first();
            $logoImage = $request->input('logoImage', optional($existingTemplate)->logo_image ?: '');

            if ($metaImage === '' && $existingTemplate) {
                $metaImage = $existingTemplate->featured_image ?: $existingTemplate->meta_image ?: '';
            }

            if ($request->hasFile('logo_image')) {
                $logoImage = $request->file('logo_image')->store('seo-logos', 'public');
            }

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
                    'logo_image' => $logoImage,
                    'primary_color' => $request->input('primaryColor', '#111827'),
                    'accent_color' => $request->input('accentColor', '#2563eb'),
                    'background_color' => $request->input('backgroundColor', '#f8fafc'),
                    'text_color' => $request->input('textColor', '#1f2937'),
                    'font_family' => $request->input('fontFamily', 'Inter, Arial, sans-serif'),
                    'container_width' => $request->input('containerWidth', '960px'),
                    'custom_css' => $request->input('customCss', ''),
                    'header_css' => $request->input('headerCss', ''),
                    'header_js' => $request->input('headerJs', ''),
                ]
            );

            $bundleIds = [
                $this->syncKeywordBundle('Bundle 1', '0', $bundle1Values)->id,
                $this->syncKeywordBundle('Bundle 2', '1', $bundle2Values)->id,
            ];

            $template->bundles()->sync($bundleIds);
            $template->generatedPages()->delete();

            $generatedCount = 0;

            foreach ($bundle1Values as $keyword1) {
                foreach ($bundle2Values as $keyword2) {
                    $replacements = [$keyword1, $keyword2];

                    $slug = $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateSlug));
                    $urlSlug = Str::slug($slug);

                    if (empty($urlSlug)) {
                        continue;
                    }

                    SeoGeneratedPage::create([
                        'template_id' => $template->id,
                        'url_slug' => $urlSlug,
                        'final_title' => $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateTitle)),
                        'final_content' => $this->normalizeGeneratedContent(
                            $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateContent))
                        ),
                        'meta_title' => $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateMetaTitle)),
                        'meta_description' => $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateMetaDescription)),
                        'meta_keywords' => $this->parseSpintax(str_replace(['{0}', '{1}'], $replacements, $templateMetaKeywords)),
                        'featured_image' => $metaImage,
                    ]);

                    $generatedCount++;
                }
            }

            return $generatedCount;
        });

        return response()->json([
            'success' => true,
            'message' => $generatedCount . ' generated page(s) created successfully.',
        ]);
    }

    public function destroy($id)
    {
        SeoGeneratedPage::findOrFail($id)->delete();

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

        if (! is_array($bundles) || ! isset($bundles[$bundleIndex]['keywords']) || ! is_array($bundles[$bundleIndex]['keywords'])) {
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
