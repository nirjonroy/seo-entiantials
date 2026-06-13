@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
@endpush

@section(config('seo.section', 'content'))
    @php
        $storedSitemapFilename = null;
        $storedSitemapUrlsPerFile = null;
        $storedSitemapChildPattern = null;
        $fallbackGeneratedPages = null;
        $fallbackGeneratedPageCount = null;

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('nirjon_seo_settings')) {
                $storedSitemapFilename = \Nirjon\LaravelSeo\Models\SeoSetting::where('key', 'sitemap.filename')->value('value');
                $storedSitemapUrlsPerFile = \Nirjon\LaravelSeo\Models\SeoSetting::where('key', 'sitemap.urls_per_file')->value('value');
                $storedSitemapChildPattern = \Nirjon\LaravelSeo\Models\SeoSetting::where('key', 'sitemap.child_pattern')->value('value');
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('nirjon_seo_generated_pages')) {
                $fallbackGeneratedPageCount = \Nirjon\LaravelSeo\Models\SeoGeneratedPage::count();
                $fallbackGeneratedPages = \Nirjon\LaravelSeo\Models\SeoGeneratedPage::query()
                    ->latest('id')
                    ->select(['id', 'url_slug', 'final_title', 'updated_at'])
                    ->take(100)
                    ->get();
            }
        } catch (\Throwable $exception) {
            $storedSitemapFilename = null;
            $storedSitemapUrlsPerFile = null;
            $storedSitemapChildPattern = null;
            $fallbackGeneratedPages = null;
            $fallbackGeneratedPageCount = null;
        }

        $sitemapFilename = $sitemapFilename ?? $sitemapFileName ?? $storedSitemapFilename ?? config('seo.sitemap.filename', 'sitemap.xml');
        $sitemapFilename = \Illuminate\Support\Str::slug(pathinfo($sitemapFilename, PATHINFO_FILENAME) ?: 'sitemap') . '.xml';
        $sitemapUrlsPerFile = (int) ($sitemapUrlsPerFile ?? $storedSitemapUrlsPerFile ?? config('seo.sitemap.urls_per_file', 1000));
        $sitemapUrlsPerFile = max(1, min($sitemapUrlsPerFile, 50000));
        $sitemapChildPattern = $sitemapChildPattern ?? $storedSitemapChildPattern ?? config('seo.sitemap.child_pattern', '{base}-{page}.xml');
        $sitemapChildPattern = str_contains($sitemapChildPattern, '{page}') ? $sitemapChildPattern : '{base}-{page}.xml';
        $sitemapUrl = $sitemapUrl ?? url($sitemapFilename);
        $generatedPageCount = $generatedPageCount ?? $fallbackGeneratedPageCount ?? 0;
        $generatedPages = $generatedPages ?? $fallbackGeneratedPages ?? collect();
        $sitemapPageFilenames = $sitemapPageFilenames ?? [];
        if (empty($sitemapPageFilenames)) {
            $totalSitemapUrls = $generatedPageCount + 1;
            $pageCount = (int) ceil($totalSitemapUrls / $sitemapUrlsPerFile);
            $baseName = pathinfo($sitemapFilename, PATHINFO_FILENAME);
            $sitemapPageFilenames = $pageCount > 1
                ? array_map(
                    fn ($page) => \Illuminate\Support\Str::slug(pathinfo(str_replace(['{base}', '{page}'], [$baseName, (string) $page], $sitemapChildPattern), PATHINFO_FILENAME) ?: "{$baseName}-{$page}") . '.xml',
                    range(1, $pageCount)
                )
                : [$sitemapFilename];
        }
    @endphp

    <main class="min-h-screen bg-slate-100 px-6 py-10 text-slate-900">
        <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">SEO Settings</h1>
                <p class="mt-1 text-sm text-slate-600">Manage package modules from the database-backed settings panel.</p>
            </div>
            <div class="flex gap-2">
                @foreach($moduleLinks as $label => $url)
                    <a href="{{ $url }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('seo.settings.update') }}" class="rounded-lg border border-slate-200 bg-white shadow-sm">
            @csrf

            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-semibold">Module Toggles</h2>
                <p class="mt-1 text-sm text-slate-500">Turn SEO features on or off without editing config files.</p>
            </div>

            <div class="divide-y divide-slate-200">
                @foreach($modules as $key => $label)
                    <label for="module-{{ $key }}" class="flex cursor-pointer items-center justify-between gap-6 px-6 py-5 hover:bg-slate-50">
                        <div>
                            <div class="text-sm font-medium text-slate-900">{{ $label }}</div>
                            <div class="mt-1 text-sm text-slate-500">Config key: <code>seo.modules.{{ $key }}</code></div>
                        </div>

                        <input
                            id="module-{{ $key }}"
                            name="modules[]"
                            value="{{ $key }}"
                            type="checkbox"
                            class="peer sr-only"
                            @checked($moduleValues[$key] ?? false)
                        >
                        <span class="relative h-6 w-11 rounded-full bg-slate-300 transition after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-5 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2"></span>
                    </label>
                @endforeach
            </div>

            <div class="border-t border-slate-200 px-6 py-5">
                <div class="mb-4">
                    <h2 class="text-base font-semibold">Dynamic Sitemap</h2>
                    <p class="mt-1 text-sm text-slate-500">Generate a public XML sitemap from PageForge generated pages and configured sitemap models.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="sitemap_filename" class="block text-sm font-medium text-slate-700">Sitemap File Name</label>
                        <input
                            id="sitemap_filename"
                            name="sitemap_filename"
                            value="{{ $sitemapFilename }}"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="seo-entiantials.xml"
                        >
                        <p class="mt-1 text-xs text-slate-500">Use a file name like <code>seo-entiantials.xml</code>. The package will sanitize the final name.</p>
                    </div>

                    <div>
                        <label for="sitemap_urls_per_file" class="block text-sm font-medium text-slate-700">URLs Per Sitemap File</label>
                        <input
                            id="sitemap_urls_per_file"
                            name="sitemap_urls_per_file"
                            value="{{ $sitemapUrlsPerFile }}"
                            type="number"
                            min="1"
                            max="50000"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <p class="mt-1 text-xs text-slate-500">When URL count is greater than this number, the package creates numbered sitemap files.</p>
                    </div>

                    <div>
                        <label for="sitemap_child_pattern" class="block text-sm font-medium text-slate-700">Child Sitemap Name Pattern</label>
                        <input
                            id="sitemap_child_pattern"
                            name="sitemap_child_pattern"
                            value="{{ $sitemapChildPattern }}"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{base}-{page}.xml"
                        >
                        <p class="mt-1 text-xs text-slate-500">Use <code>{base}</code> and <code>{page}</code>. Example: <code>{base}-{page}.xml</code> creates <code>seo-essentials-2.xml</code>.</p>
                    </div>

                    <div class="rounded-md border border-blue-100 bg-blue-50 p-4">
                        <div class="text-sm font-semibold text-blue-900">Current Sitemap URL</div>
                        <a href="{{ $sitemapUrl }}" target="_blank" rel="noopener" class="mt-2 block break-all text-sm font-medium text-blue-700 hover:underline">{{ $sitemapUrl }}</a>
                        <p class="mt-2 text-xs text-blue-700">The default <code>{{ url('sitemap.xml') }}</code> URL also remains available.</p>
                    </div>
                </div>

                <div class="mt-4 rounded-md border border-slate-200 bg-white p-4">
                    <div class="text-sm font-semibold text-slate-900">Generated Sitemap Files</div>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ count($sitemapPageFilenames) > 1 ? $sitemapFilename . ' is the sitemap index. These child files contain the URLs.' : 'The sitemap currently fits in one file.' }}
                    </p>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach($sitemapPageFilenames as $filename)
                            <a href="{{ url($filename) }}" target="_blank" rel="noopener" class="break-all rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                {{ url($filename) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 rounded-md border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">PageForge Generated Pages</h3>
                            <p class="text-sm text-slate-500">{{ $generatedPageCount }} generated page(s) will be included in the XML sitemap.</p>
                        </div>
                        <a href="{{ route('seo.generator') }}" class="text-sm font-semibold text-blue-700 hover:underline">Manage PageForge Pages</a>
                    </div>

                    @if($generatedPages->isNotEmpty())
                        <div class="mt-4 max-h-72 overflow-y-auto rounded-md border border-slate-200 bg-white">
                            <table class="min-w-full text-left text-sm">
                                <thead class="sticky top-0 bg-slate-100 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2">SL</th>
                                        <th class="px-3 py-2">Title</th>
                                        <th class="px-3 py-2">URL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($generatedPages as $page)
                                        <tr>
                                            <td class="px-3 py-2 text-slate-500">{{ $loop->iteration }}</td>
                                            <td class="px-3 py-2 font-medium text-slate-900">{{ $page->final_title }}</td>
                                            <td class="px-3 py-2">
                                                <a href="{{ url($page->url_slug) }}" target="_blank" rel="noopener" class="break-all text-blue-700 hover:underline">{{ url($page->url_slug) }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($generatedPageCount > $generatedPages->count())
                            <p class="mt-2 text-xs text-slate-500">Showing latest {{ $generatedPages->count() }} generated pages in this panel. All {{ $generatedPageCount }} generated pages are included in the XML output.</p>
                        @endif
                    @else
                        <div class="mt-4 rounded-md border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500">
                            No PageForge generated pages found yet.
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save
                </button>
            </div>
        </form>
        </div>
    </main>
@endsection
