<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTemplate extends Model
{
    protected $guarded = [];

    public function bundles()
    {
        return $this->belongsToMany(SeoKeywordBundle::class, 'seo_template_bundles', 'template_id', 'bundle_id');
    }

    public function generatedPages()
    {
        return $this->hasMany(SeoGeneratedPage::class, 'template_id');
    }
}
