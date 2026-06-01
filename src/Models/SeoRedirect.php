<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nirjon_seo_redirects';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
