<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoGeneratedPage extends Model
{
    protected $guarded = [];

    public function template()
    {
        return $this->belongsTo(SeoTemplate::class, 'template_id');
    }
}
