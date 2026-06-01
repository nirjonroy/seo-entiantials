<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;

class SeoFilesController extends Controller
{
    public function robots()
    {
        return response(config('seo.files.robots_txt', ''), 200, ['Content-Type' => 'text/plain']);
    }

    public function llms()
{
    $content = config('seo.files.llms_txt');
    
    return response($content, 200)->header('Content-Type', 'text/plain');
}

    public function security()
    {
        return response(config('seo.files.security_txt', ''), 200, ['Content-Type' => 'text/plain']);
    }
}
