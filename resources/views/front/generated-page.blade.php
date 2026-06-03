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
        $mediaUrl = function ($path) {
            $path = (string) $path;

            if ($path === '' || \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])) {
                return $path;
            }

            return route('seo.media', ['path' => ltrim($path, '/')]);
        };

        $image = $page->featured_image ?: optional($template)->featured_image ?: optional($template)->meta_image ?: config('seo.defaults.default_image');
        $image = $mediaUrl($image);

        $logo = optional($template)->logo_image;
        $logo = $mediaUrl($logo);

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
<<<<<<< HEAD
        .pf-header { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 18px 0 28px; }
        .pf-brand { display: flex; align-items: center; gap: 12px; color: var(--pf-primary); font-weight: 800; text-decoration: none; }
        .pf-logo { max-height: 44px; max-width: 180px; object-fit: contain; }
        .pf-card { border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 8px; background: #fff; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .pf-hero { border-bottom: 1px solid rgba(15, 23, 42, 0.08); padding: 42px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.10), rgba(255, 255, 255, 0)); }
        .pf-eyebrow { margin: 0 0 10px; color: var(--pf-accent); font-size: 13px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .pf-title { margin: 0; color: var(--pf-primary); font-size: clamp(32px, 5vw, 58px); line-height: 1.05; letter-spacing: 0; }
        .pf-featured { display: block; width: 100%; max-height: 520px; object-fit: cover; border-top: 1px solid rgba(15, 23, 42, 0.08); }
        .pf-content { padding: 38px 42px 46px; font-size: 18px; }
        .pf-content :where(h2, h3, h4) { color: var(--pf-primary); line-height: 1.25; margin: 1.7em 0 0.65em; }
        .pf-content p { margin: 0 0 1.15em; }
        .pf-content a { color: var(--pf-accent); }
        .pf-content img, .pf-content iframe, .pf-content video { max-width: 100%; border-radius: 8px; }
        .pf-content blockquote { margin: 1.5em 0; border-left: 4px solid var(--pf-accent); padding: 0.5em 0 0.5em 1em; color: #475569; background: #f8fafc; }
        .pf-related { margin-top: 28px; border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 8px; background: #fff; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06); padding: 28px; }
        .pf-related-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
        .pf-related-kicker { margin: 0 0 4px; color: var(--pf-accent); font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .pf-related-title { margin: 0; color: var(--pf-primary); font-size: 24px; line-height: 1.2; }
        .pf-related-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .pf-related-card { display: flex; min-height: 132px; flex-direction: column; justify-content: space-between; border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 8px; padding: 18px; color: var(--pf-primary); text-decoration: none; transition: border-color 160ms ease, transform 160ms ease, box-shadow 160ms ease; }
        .pf-related-card:hover { border-color: var(--pf-accent); box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10); transform: translateY(-2px); }
        .pf-related-image { width: calc(100% + 36px); height: 118px; margin: -18px -18px 14px; object-fit: cover; border-bottom: 1px solid rgba(15, 23, 42, 0.08); border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .pf-related-card-title { margin: 0; font-size: 16px; font-weight: 800; line-height: 1.35; }
        .pf-related-card-link { color: var(--pf-accent); font-size: 13px; font-weight: 800; }
=======
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
        .pf-featured {
            display: block;
            width: 100%;
            max-height: 520px;
            object-fit: cover;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
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
        .pf-related {
            margin-top: 28px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
            padding: 28px;
        }
        .pf-related-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .pf-related-kicker {
            margin: 0 0 4px;
            color: var(--pf-accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .pf-related-title {
            margin: 0;
            color: var(--pf-primary);
            font-size: 24px;
            line-height: 1.2;
        }
        .pf-related-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
        .pf-related-card {
            display: flex;
            min-height: 132px;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 8px;
            padding: 18px;
            color: var(--pf-primary);
            text-decoration: none;
            transition: border-color 160ms ease, transform 160ms ease, box-shadow 160ms ease;
        }
        .pf-related-card:hover {
            border-color: var(--pf-accent);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
            transform: translateY(-2px);
        }
        .pf-related-image {
            width: calc(100% + 36px);
            height: 118px;
            margin: -18px -18px 14px;
            object-fit: cover;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .pf-related-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
        }
        .pf-related-card-link {
            color: var(--pf-accent);
            font-size: 13px;
            font-weight: 800;
        }
>>>>>>> 37a31d9cbbb998ccf1b629ce23cddc17edc982e4
        @media (max-width: 640px) {
            .pf-page { padding: 18px 12px 36px; }
            .pf-header { align-items: flex-start; flex-direction: column; }
            .pf-hero, .pf-content { padding: 26px 20px; }
            .pf-content { font-size: 16px; }
            .pf-related { padding: 22px 18px; }
            .pf-related-head { align-items: flex-start; flex-direction: column; }
            .pf-related-grid { grid-template-columns: 1fr; }
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
                @if($image)
                    <img src="{{ $image }}" alt="{{ $title }}" class="pf-featured">
                @endif
                <section class="pf-content">
                    {!! $content !!}
                </section>
            </article>
            @if(isset($relatedPages) && $relatedPages->isNotEmpty())
                <section class="pf-related" aria-labelledby="pf-related-title">
                    <div class="pf-related-head">
                        <div>
                            <p class="pf-related-kicker">More From {{ $siteName }}</p>
                            <h2 id="pf-related-title" class="pf-related-title">Related Pages</h2>
                        </div>
                    </div>
                    <div class="pf-related-grid">
                        @foreach($relatedPages as $relatedPage)
                            @php
                                $relatedImage = $mediaUrl($relatedPage->featured_image);
                            @endphp
                            <a href="{{ url('/' . $relatedPage->url_slug) }}" class="pf-related-card">
                                @if($relatedImage)
                                    <img src="{{ $relatedImage }}" alt="{{ $parseSpintax($relatedPage->final_title) }}" class="pf-related-image">
                                @endif
                                <p class="pf-related-card-title">{{ $parseSpintax($relatedPage->final_title) }}</p>
                                <span class="pf-related-card-link">Read page</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>
</body>
</html>
