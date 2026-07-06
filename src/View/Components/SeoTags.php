<?php

namespace Nirjon\LaravelSeo\View\Components;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nirjon\LaravelSeo\Models\SeoMeta;
use Illuminate\View\Component;

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
    public $twitterTitle;
    public $twitterDescription;
    public $twitterImage;
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
    public function __construct($model = null, $title = null, $description = null, $keywords = null, $canonical = null, $ogTitle = null, $ogDescription = null, $image = null, $author = null, $publisher = null, $copyright = null, $siteName = null, $robots = null, $twitterTitle = null, $twitterDescription = null, $twitterImage = null)
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
        $this->twitterTitle = $twitterTitle;
        $this->twitterDescription = $twitterDescription;
        $this->twitterImage = $twitterImage;
        $this->applyDynamicMeta();
        $this->pageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'url' => $this->canonical ?: url()->current(),
        ];
    }

    private function applyDynamicMeta(): void
    {
        if (! config('seo.modules.meta', true) || ! Schema::hasTable('nirjon_seo_metas') || ! Schema::hasColumn('nirjon_seo_metas', 'url_path')) {
            return;
        }

        $meta = SeoMeta::query()
            ->where('seoable_type', 'url')
            ->where('is_active', true)
            ->where('url_path', $this->currentPath())
            ->latest('id')
            ->first();

        if (! $meta) {
            return;
        }

        $this->title = $this->title ?: $meta->title;
        $this->description = $this->description ?: $meta->description;
        $this->keywords = $this->keywords ?: $meta->keywords;
        $this->canonical = $this->canonical ?: $meta->canonical_url;
        $this->ogTitle = $this->ogTitle ?: ($meta->og_title ?: $meta->title);
        $this->ogDescription = $this->ogDescription ?: ($meta->og_description ?: $meta->description);
        $this->image = $this->image ?: ($meta->og_image ?: $meta->twitter_image);
        $this->author = $this->author ?: $meta->author;
        $this->publisher = $this->publisher ?: $meta->publisher;
        $this->copyright = $this->copyright ?: $meta->copyright;
        $this->siteName = $this->siteName ?: $meta->site_name;
        $this->robots = $this->robots ?: $meta->robots_tag;
        $this->twitterTitle = $this->twitterTitle ?: ($meta->twitter_title ?: $this->ogTitle);
        $this->twitterDescription = $this->twitterDescription ?: ($meta->twitter_description ?: $this->ogDescription);
        $this->twitterImage = $this->twitterImage ?: ($meta->twitter_image ?: $this->image);
    }

    private function currentPath(): string
    {
        $path = '/' . ltrim(request()->path(), '/');
        $path = $path === '//' ? '/' : $path;

        $path = $path === '/.' ? '/' : (rtrim($path, '/') ?: '/');

        return Str::limit($path, 2048, '');
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
