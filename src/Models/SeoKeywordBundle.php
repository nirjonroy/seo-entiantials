<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoKeywordBundle extends Model
{
    protected $table = 'nirjon_seo_keyword_bundles';

    protected $guarded = [];

    public function keywords()
    {
        return $this->hasMany(SeoKeyword::class, 'bundle_id');
    }
}
