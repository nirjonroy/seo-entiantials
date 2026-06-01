<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class Seo404Log extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nirjon_seo_404_logs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
