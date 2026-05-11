@php
    $siteName = config('seo.fallbacks.site_name', env('APP_NAME', 'Laravel'));
    $title = config('seo.fallbacks.default_title', 'Welcome');
    $description = config('seo.fallbacks.default_description', 'Default description');

    if (isset($model) && method_exists($model, 'getSeoTitle')) {
        $title = $model->getSeoTitle();
        $description = (isset($model) && method_exists($model, 'getSeoDescription')) ? $model->getSeoDescription() : $description;
    }
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:site_name" content="{{ $siteName }}">

<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">

@foreach(config('seo.meta', []) as $key => $value)
    <meta name="{{ $key }}" content="{{ $value }}">
@endforeach

{!! $schemas ?? '' !!}
