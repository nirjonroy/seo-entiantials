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
        'image_seo'    => true,
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
        'models' => [
        \App\Models\TestProduct::class, // 
    ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Static SEO Files
    |--------------------------------------------------------------------------
    |
    | Content defaults for generated text files.
    |
    */
    'files' => [
        'robots_txt' => "User-agent: *\nAllow: /\nSitemap: " . env('APP_URL') . "/sitemap.xml",
        'llms_txt' => "Title: My Awesome Site\nDescription: Data for LLMs and AI crawlers.\n",
        'security_txt' => "Contact: mailto:admin@example.com\nExpires: 2027-01-01T00:00:00.000Z\n",
    ],

    /*
    |--------------------------------------------------------------------------
    | Local Business Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Local Business Schema.
    |
    */
    'local_business' => [
        'name' => 'Placeholder Business Name',
        'image' => 'https://example.com/image.jpg',
        'telephone' => '+1-555-555-5555',
        'priceRange' => '$$',
        'address' => [
            'streetAddress' => '123 Main St',
            'addressLocality' => 'Anytown',
            'postalCode' => '12345',
            'addressCountry' => 'US',
        ],
        'geo' => [
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
        ],
    ],
];
