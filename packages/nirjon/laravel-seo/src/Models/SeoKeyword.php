<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    protected $guarded = [];

    public function bundle()
    {
        return $this->belongsTo(SeoKeywordBundle::class, 'bundle_id');
    }
}
