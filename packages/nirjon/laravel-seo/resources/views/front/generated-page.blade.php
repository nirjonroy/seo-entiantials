<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $parseSpintax = function ($value) {
            $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            while (preg_match('/\{([^{}]+)\}/', $value)) {
                $value = preg_replace_callback('/\{([^{}]+)\}/', function ($matches) {
                    $parts = array_values(array_filter(array_map('trim', explode('|', $matches[1])), 'strlen'));

                    return $parts[0] ?? '';
                }, $value);
            }

            return $value;
        };

        $template = $page->template;
        $title = $parseSpintax($page->final_title);
        $content = $parseSpintax($page->final_content);
        $metaTitle = $parseSpintax($page->meta_title ?: $title);
        $metaDescription = $parseSpintax($page->meta_description);
        $metaKeywords = $parseSpintax($page->meta_keywords);
        $image = $page->featured_image ?: optional($template)->featured_image ?: optional($template)->meta_image ?: config('seo.defaults.default_image');

        if ($image && ! \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])) {
            $image = asset('storage/' . ltrim($image, '/'));
        }
    @endphp
    <x-seo::tags
        :title="$metaTitle"
        :description="$metaDescription"
        :keywords="$metaKeywords"
        :canonical="url('/' . $page->url_slug)"
        :ogTitle="$title"
        :ogDescription="$metaDescription"
        :image="$image"
        :author="optional($template)->author"
        :publisher="optional($template)->publisher"
        :copyright="optional($template)->copyright"
        :siteName="optional($template)->site_name"
        robots="index, follow"
    />
</head>
<body>
    <h1>{{ $title }}</h1>
    <div>
        {!! $content !!}
    </div>
</body>
</html>
