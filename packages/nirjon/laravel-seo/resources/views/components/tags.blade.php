@php
    $siteName = config('seo.fallbacks.site_name', env('APP_NAME', 'Laravel'));
    $title = config('seo.fallbacks.default_title', 'Welcome');
    $description = config('seo.fallbacks.default_description', 'Default description');

    if (isset($model) && method_exists($model, 'getSeoTitle')) {
        $title = $model->getSeoTitle();
        $description = (isset($model) && method_exists($model, 'getSeoDescription')) ? $model->getSeoDescription() : $description;
    }

    $currentPage = request()->get('page', 1);
    $prevUrl = $currentPage > 1 ? request()->fullUrlWithQuery(['page' => $currentPage - 1]) : null;
    $nextUrl = request()->has('page') ? request()->fullUrlWithQuery(['page' => $currentPage + 1]) : null;
    $currentLocale = app()->getLocale();
    $currentUrl = url()->current();
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:site_name" content="{{ $siteName }}">

<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">

@if($prevUrl) <link rel="prev" href="{{ $prevUrl }}"> @endif
@if($nextUrl) <link rel="next" href="{{ $nextUrl }}"> @endif
<link rel="alternate" hreflang="{{ $currentLocale }}" href="{{ $currentUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $currentUrl }}">

@foreach(config('seo.meta', []) as $key => $value)
    <meta name="{{ $key }}" content="{{ $value }}">
@endforeach

{!! $schemas ?? '' !!}

@if($google = config('seo.verifications.google')) <meta name="google-site-verification" content="{{ $google }}"> @endif
@if($bing = config('seo.verifications.bing')) <meta name="msvalidate.01" content="{{ $bing }}"> @endif
@if($yandex = config('seo.verifications.yandex')) <meta name="yandex-verification" content="{{ $yandex }}"> @endif
@if($pinterest = config('seo.verifications.pinterest')) <meta name="p:domain_verify" content="{{ $pinterest }}"> @endif
@if($baidu = config('seo.verifications.baidu')) <meta name="baidu-site-verification" content="{{ $baidu }}"> @endif

{!! config('seo.scripts.head') !!}

@php
    $globalSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                'name' => config('seo.organization.name', env('APP_NAME')),
                'url' => config('seo.organization.url', env('APP_URL')),
            ],
            [
                '@type' => 'Organization',
                'name' => config('seo.organization.name', env('APP_NAME')),
                'url' => config('seo.organization.url', env('APP_URL')),
                'logo' => config('seo.organization.logo', ''),
                'sameAs' => config('seo.organization.social_profiles', [])
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
    {!! json_encode($globalSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@if(isset($model) && method_exists($model, 'getSchema') && $model->getSchema())
    <script type="application/ld+json">
        {!! json_encode($model->getSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

