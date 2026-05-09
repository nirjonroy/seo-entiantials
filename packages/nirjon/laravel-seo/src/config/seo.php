<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Modules
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable specific modules within the package.
    | Set a module to 'true' to enable it, or 'false' to disable it.
    |
    */
    'modules' => [
        'meta'         => true,
        'sitemaps'     => true,
        'redirections' => true,
        'schema'       => true,
        'local_seo'    => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default SEO Values
    |--------------------------------------------------------------------------
    |
    | These values are used as fallbacks when specific SEO data is not
    | provided for a given page or entity.
    |
    */
    'defaults' => [
        'site_name'       => env('APP_NAME', 'My Site'),
        'title_separator' => '|',
        'author'          => '',
        'publisher'       => '',
        'copyright'       => '',
        'default_image'   => '', // Provide a URL or asset path to a default Open Graph image
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for generating XML and HTML sitemaps.
    |
    */
    'sitemap' => [
        'enable_xml' => true,
        'enable_html' => true,
        'exclude_urls' => ['/admin/*', '/login', '/register'],
        'change_frequency' => 'weekly',
        'default_priority' => '0.8',
        
        // Add your Eloquent model classes here (e.g., \App\Models\Product::class, \App\Models\Service::class)
        // to automatically include them in the sitemap.
        'models' => [],
    ],
];
