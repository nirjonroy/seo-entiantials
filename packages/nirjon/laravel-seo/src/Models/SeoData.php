<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nirjon_seo_data';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the parent seoable model (e.g., Post, Page, Product).
     */
    public function seoable()
    {
        return $this->morphTo();
    }
}
