<?php

namespace Nirjon\LaravelSeo\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Nirjon\LaravelSeo\Models\SeoMeta;

trait HasSeo
{
    /**
     * Get the SEO metadata associated with the model.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Update or create the SEO metadata for the model.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|SeoMeta
     */
    public function updateSeo(array $data)
    {
        return $this->seo()->updateOrCreate([], $data);
    }
}
