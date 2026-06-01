<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $table = 'nirjon_seo_settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
