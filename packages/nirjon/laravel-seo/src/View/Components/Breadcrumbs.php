<?php

namespace Nirjon\LaravelSeo\View\Components;

use Illuminate\View\Component;
use Nirjon\LaravelSeo\Services\BreadcrumbService;

class Breadcrumbs extends Component
{
    public $breadcrumbService;

    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    public function render()
    {
        return view('seo::components.breadcrumbs');
    }
}
