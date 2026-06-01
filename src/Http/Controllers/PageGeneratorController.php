<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Nirjon\LaravelSeo\Models\SeoTemplate;
use Nirjon\LaravelSeo\Models\SeoKeywordBundle;
use Nirjon\LaravelSeo\Models\SeoKeyword;
use Nirjon\LaravelSeo\Jobs\GeneratePagesJob;
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
     * Delete a generated page from the UI.
     *
     * @param SeoGeneratedPage $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiDeletePage(SeoGeneratedPage $page)
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Generated page deleted successfully.'
        ]);
    }

    /**
     * Generate pages from a template using a background job.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGenerate(Request $request)
    {
        $metaImage = $request->input('metaImage', '');

        if ($request->hasFile('featured_image')) {
            $metaImage = $request->file('featured_image')->store('seo-images', 'public');
        }

        // Explicitly map only string values for the template to prevent Array to string conversion errors
        $template = SeoTemplate::updateOrCreate(
            ['slug_structure' => (string) $request->input('slug', '')],
            [
                'name' => 'PageForge Live Template - ' . $request->input('title', ''),
                'title_structure' => (string) $request->input('title', ''),
                'content' => (string) $request->input('content', ''),

                'meta_title' => $request->input('metaTitle', ''),
                'meta_description' => $request->input('metaDescription', ''),
                'keywords' => $request->input('metaKeywords', ''),
                'meta_image' => $metaImage,
                'author' => $request->input('author', ''),
                'publisher' => $request->input('publisher', ''),
                'copyright' => $request->input('copyright', ''),
                'site_name' => $request->input('siteName', ''),
            ]
        );

        $bundleIds = [];
        $bundlesInput = $request->input('bundles');
        $bundles = is_string($bundlesInput) ? json_decode($bundlesInput, true) : [];

        if (is_array($bundles)) {
            foreach ($bundles as $key => $bundleData) {
                if (!is_array($bundleData) || empty($bundleData['name'])) {
                    continue;
                }

                $bundleSlug = (string) $key;

                $bundle = SeoKeywordBundle::updateOrCreate(
                    ['slug' => $bundleSlug],
                    ['name' => (string) $bundleData['name'], 'is_active' => true]
                );

                $bundleIds[] = $bundle->id;

                if (isset($bundleData['keywords']) && is_array($bundleData['keywords'])) {
                    // Wipe and recreate keywords for this bundle
                    $bundle->keywords()->delete();
                    foreach ($bundleData['keywords'] as $keywordValue) {
                        SeoKeyword::create([
                            'bundle_id' => $bundle->id,
                            'keyword' => (string) $keywordValue
                        ]);
                    }
                }
            }
        }

        // Finally, sync them to the template
        $template->bundles()->sync($bundleIds);

        GeneratePagesJob::dispatch($template->id);

        return response()->json([
            'success' => true,
            'message' => 'Generation started in background!'
        ]);
    }
}
