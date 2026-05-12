<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->final_title }}</title>
    <x-seo::tags />
</head>
<body>
    <h1>{{ $page->final_title }}</h1>
    <div>
        {!! $page->final_content !!}
    </div>
</body>
</html>
