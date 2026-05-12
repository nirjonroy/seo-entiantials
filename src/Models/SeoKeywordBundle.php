<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoKeywordBundle extends Model
{
    protected $guarded = [];

    public function keywords()
    {
        return $this->hasMany(SeoKeyword::class, 'bundle_id');
    }
}
