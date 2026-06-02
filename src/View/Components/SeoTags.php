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
    public $title;
    public $description;
    public $keywords;
    public $canonical;
    public $ogTitle;
    public $ogDescription;
    public $image;
    public $author;
    public $publisher;
    public $copyright;
    public $siteName;
    public $robots;
    public $pageSchema;

    /**
     * Create a new component instance.
     *
     * @param mixed $model
     * @param string|null $title
     * @param string|null $description
     * @param string|null $keywords
     * @param string|null $canonical
     * @param string|null $ogTitle
     * @param string|null $ogDescription
     * @param string|null $image
     * @return void
     */
    public function __construct($model = null, $title = null, $description = null, $keywords = null, $canonical = null, $ogTitle = null, $ogDescription = null, $image = null, $author = null, $publisher = null, $copyright = null, $siteName = null, $robots = null)
    {
        $this->model = $model;
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->canonical = $canonical;
        $this->ogTitle = $ogTitle;
        $this->ogDescription = $ogDescription;
        $this->image = $image;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->copyright = $copyright;
        $this->siteName = $siteName;
        $this->robots = $robots;
        $this->pageSchema = ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $title, 'description' => $description, 'image' => $image, 'url' => $canonical];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('seo::components.tags');
    }
}
