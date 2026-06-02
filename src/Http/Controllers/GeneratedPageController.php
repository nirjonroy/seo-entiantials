<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;

class GeneratedPageController extends Controller
{
    public function show($slug)
    {
        $page = SeoGeneratedPage::with('template')->where('url_slug', $slug)->firstOrFail();
        $relatedPages = SeoGeneratedPage::query()
            ->whereKeyNot($page->getKey())
            ->when($page->template_id, function ($query) use ($page) {
                $query->where('template_id', $page->template_id);
            })
            ->latest()
            ->take(6)
            ->get();

        if ($relatedPages->count() < 3) {
            $fallbackPages = SeoGeneratedPage::query()
                ->whereKeyNot($page->getKey())
                ->whereNotIn('id', $relatedPages->pluck('id'))
                ->latest()
                ->take(6 - $relatedPages->count())
                ->get();

            $relatedPages = $relatedPages->concat($fallbackPages);
        }

        return view('seo::front.generated-page', compact('page', 'relatedPages'));
    }

    public function media($path)
    {
        $path = ltrim(str_replace('\\', '/', (string) $path), '/');

        abort_if($path === '' || str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
