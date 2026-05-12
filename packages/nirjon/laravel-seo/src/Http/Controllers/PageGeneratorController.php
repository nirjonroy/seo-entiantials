<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Nirjon\LaravelSeo\Models\SeoTemplate;
use Nirjon\LaravelSeo\Jobs\GeneratePagesJob;

class PageGeneratorController extends Controller
{
    /**
     * Generate pages from a template using a background job.
     *
     * @param Request $request
     * @param SeoTemplate $template
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGenerate(Request $request, SeoTemplate $template)
    {
        GeneratePagesJob::dispatch($template->id);

        return response()->json([
            'success' => true,
            'message' => 'Page generation job has been queued successfully.'
        ]);
    }
}
