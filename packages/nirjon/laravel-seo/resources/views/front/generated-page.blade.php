<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->final_title }}</title>
    <x-seo::tags :title="$page->meta_title ?: $page->final_title" :description="$page->meta_description" :keywords="$page->meta_keywords" :canonical="url('/' . $page->url_slug)" :ogTitle="$page->final_title" :ogDescription="$page->meta_description" />
</head>
<body>
    <h1>{{ $page->final_title }}</h1>
    <div>
        {!! $page->final_content !!}
    </div>
</body>
</html>
