<?php

namespace Nirjon\LaravelSeo\View\Components;

use Illuminate\View\Component;
use Nirjon\LaravelSeo\Services\SEOMetaService;

class SeoTags extends Component
{
    /**
     * The generated SEO tags.
     *
     * @var array
     */
    public $tags;

    /**
     * The generated schema JSON tags.
     *
     * @var string
     */
    public $schemas;

    public $model;

    /**
     * Create a new component instance.
     *
     * @param mixed $model
     * @return void
     */
    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('seo::components.tags', ['tags' => $this->tags]);
    }
}
