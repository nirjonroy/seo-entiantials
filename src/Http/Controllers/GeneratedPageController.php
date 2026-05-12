<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Nirjon\LaravelSeo\Models\SeoGeneratedPage;

class GeneratedPageController extends Controller
{
    public function show($slug)
    {
        $page = SeoGeneratedPage::where('url_slug', $slug)->firstOrFail();

        return view('seo::front.generated-page', compact('page'));
    }
}
