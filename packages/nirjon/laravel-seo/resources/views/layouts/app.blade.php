<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('seo.defaults.site_name', config('app.name', 'Laravel')) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('seo_styles')
</head>
<body>
    @yield(config('seo.section', 'content'))
    @stack('seo_scripts')
</body>
</html>
