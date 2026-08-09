<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoGeneratedPage extends Model
{
    protected $table = 'nirjon_seo_generated_pages';

    protected $guarded = [];

    protected $casts = [
        'replacement_values' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(SeoTemplate::class, 'template_id');
    }
}
