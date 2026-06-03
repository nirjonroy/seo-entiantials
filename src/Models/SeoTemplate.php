<?php

namespace Nirjon\LaravelSeo\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTemplate extends Model
{
    protected $table = 'nirjon_seo_templates';

    protected $fillable = [
        'name',
        'title_structure',
        'slug_structure',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'meta_image',
        'author',
        'publisher',
        'copyright',
        'site_name',
        'logo_image',
        'primary_color',
        'accent_color',
        'background_color',
        'text_color',
        'font_family',
        'container_width',
        'custom_css',
        'header_css',
        'header_js',
        'is_active',
    ];

    public function bundles()
    {
        return $this->belongsToMany(SeoKeywordBundle::class, 'nirjon_seo_template_bundles', 'template_id', 'bundle_id');
    }

    public function generatedPages()
    {
        return $this->hasMany(SeoGeneratedPage::class, 'template_id');
    }
}
