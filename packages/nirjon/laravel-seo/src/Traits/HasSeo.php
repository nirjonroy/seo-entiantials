<?php

namespace Nirjon\LaravelSeo\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Nirjon\LaravelSeo\Models\SeoData;

trait HasSeo
{
    /**
     * Get the SEO metadata associated with the model.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoData::class, 'seoable')->withDefault();
    }

    /**
     * Update or create the SEO metadata for the model.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|SeoData
     */
    public function updateSeo(array $data)
    {
        return $this->seo()->updateOrCreate([], $data);
    }

    /**
     * Get the SEO title.
     *
     * @return string
     */
    public function getSeoTitle()
    {
        if ($this->seo && $this->seo->meta_title) {
            return $this->seo->meta_title;
        }

        $template = config('seo.fallbacks.default_title');
        $parser = app(\Nirjon\LaravelSeo\Services\SeoTemplateParser::class);

        return $parser->parse($template, $this);
    }

    /**
     * Get the SEO description.
     *
     * @return string
     */
    public function getSeoDescription()
    {
        if ($this->seo && $this->seo->meta_description) {
            return $this->seo->meta_description;
        }

        $template = config('seo.fallbacks.default_description');
        $parser = app(\Nirjon\LaravelSeo\Services\SeoTemplateParser::class);

        return $parser->parse($template, $this);
    }

    /**
     * Get the SEO schema.
     *
     * @return array|null
     */
    public function getSchema()
    {
        $factory = app(\Nirjon\LaravelSeo\Services\SchemaFactory::class);
        return $factory->generate($this);
    }
}
