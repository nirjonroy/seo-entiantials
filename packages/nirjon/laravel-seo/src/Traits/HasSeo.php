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
}
