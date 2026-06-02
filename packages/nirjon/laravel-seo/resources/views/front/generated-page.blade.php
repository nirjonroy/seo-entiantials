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

        $logo = optional($template)->logo_image;
        if ($logo && ! \Illuminate\Support\Str::startsWith($logo, ['http://', 'https://', '/'])) {
            $logo = asset('storage/' . ltrim($logo, '/'));
        }

        $primaryColor = optional($template)->primary_color ?: '#111827';
        $accentColor = optional($template)->accent_color ?: '#2563eb';
        $backgroundColor = optional($template)->background_color ?: '#f8fafc';
        $textColor = optional($template)->text_color ?: '#1f2937';
        $fontFamily = optional($template)->font_family ?: 'Inter, Arial, sans-serif';
        $containerWidth = optional($template)->container_width ?: '960px';
        $siteName = optional($template)->site_name ?: config('seo.fallbacks.site_name', config('app.name'));
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
    <style>
        :root {
            --pf-primary: {{ $primaryColor }};
            --pf-accent: {{ $accentColor }};
            --pf-bg: {{ $backgroundColor }};
            --pf-text: {{ $textColor }};
            --pf-width: {{ $containerWidth }};
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--pf-bg);
            color: var(--pf-text);
            font-family: {!! json_encode($fontFamily) !!};
            line-height: 1.75;
        }
        .pf-page { min-height: 100vh; padding: 32px 18px 56px; }
        .pf-shell { max-width: var(--pf-width); margin: 0 auto; }
        .pf-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 0 28px;
        }
        .pf-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--pf-primary);
            font-weight: 800;
            text-decoration: none;
        }
        .pf-logo { max-height: 44px; max-width: 180px; object-fit: contain; }
        .pf-card {
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .pf-hero {
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            padding: 42px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.10), rgba(255, 255, 255, 0));
        }
        .pf-eyebrow {
            margin: 0 0 10px;
            color: var(--pf-accent);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .pf-title {
            margin: 0;
            color: var(--pf-primary);
            font-size: clamp(32px, 5vw, 58px);
            line-height: 1.05;
            letter-spacing: 0;
        }
        .pf-content {
            padding: 38px 42px 46px;
            font-size: 18px;
        }
        .pf-content :where(h2, h3, h4) {
            color: var(--pf-primary);
            line-height: 1.25;
            margin: 1.7em 0 0.65em;
        }
        .pf-content p { margin: 0 0 1.15em; }
        .pf-content a { color: var(--pf-accent); }
        .pf-content img, .pf-content iframe, .pf-content video {
            max-width: 100%;
            border-radius: 8px;
        }
        .pf-content blockquote {
            margin: 1.5em 0;
            border-left: 4px solid var(--pf-accent);
            padding: 0.5em 0 0.5em 1em;
            color: #475569;
            background: #f8fafc;
        }
        @media (max-width: 640px) {
            .pf-page { padding: 18px 12px 36px; }
            .pf-header { align-items: flex-start; flex-direction: column; }
            .pf-hero, .pf-content { padding: 26px 20px; }
            .pf-content { font-size: 16px; }
        }
        {!! optional($template)->custom_css !!}
    </style>
</head>
<body>
    <main class="pf-page">
        <div class="pf-shell">
            <header class="pf-header">
                <a href="{{ url('/') }}" class="pf-brand">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="pf-logo">
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>
            </header>
            <article class="pf-card">
                <section class="pf-hero">
                    <p class="pf-eyebrow">{{ $siteName }}</p>
                    <h1 class="pf-title">{{ $title }}</h1>
                </section>
                <section class="pf-content">
                    {!! $content !!}
                </section>
            </article>
        </div>
    </main>
</body>
</html>
