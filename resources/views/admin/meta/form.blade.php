@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
@endpush

@section(config('seo.section', 'content'))
    <main class="min-h-screen bg-slate-100 px-6 py-10 text-slate-900">
        <div class="mx-auto max-w-5xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Use a relative path like <code>/about-us</code> or paste a full URL.</p>
                </div>

                <a href="{{ route('seo.meta') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Meta Tags</a>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold">Please fix the following errors:</div>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white px-6 py-6 shadow-sm">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="space-y-6">
                    <div>
                        <label for="url_path" class="block text-sm font-medium text-slate-700">URL Path <span class="text-red-600">*</span></label>
                        <input id="url_path" name="url_path" type="text" required value="{{ old('url_path', $meta->url_path) }}" placeholder="/about-us" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(old('is_active', $meta->is_active ?? true))>
                        Active
                    </label>

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700">Meta Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $meta->title) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Meta Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $meta->description) }}</textarea>
                    </div>

                    <div>
                        <label for="keywords" class="block text-sm font-medium text-slate-700">Meta Keywords</label>
                        <textarea id="keywords" name="keywords" rows="3" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keywords', $meta->keywords) }}</textarea>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="author" class="block text-sm font-medium text-slate-700">Author</label>
                            <input id="author" name="author" type="text" value="{{ old('author', $meta->author) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="publisher" class="block text-sm font-medium text-slate-700">Publisher</label>
                            <input id="publisher" name="publisher" type="text" value="{{ old('publisher', $meta->publisher) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="copyright" class="block text-sm font-medium text-slate-700">Copyright</label>
                            <input id="copyright" name="copyright" type="text" value="{{ old('copyright', $meta->copyright) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-slate-700">Site Name</label>
                            <input id="site_name" name="site_name" type="text" value="{{ old('site_name', $meta->site_name) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="robots_tag" class="block text-sm font-medium text-slate-700">Robots</label>
                            <input id="robots_tag" name="robots_tag" type="text" value="{{ old('robots_tag', $meta->robots_tag ?: 'index, follow') }}" placeholder="index, follow" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="canonical_url" class="block text-sm font-medium text-slate-700">Canonical URL</label>
                            <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url', $meta->canonical_url) }}" placeholder="https://example.com/page" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Open Graph</h2>
                        <div class="mt-4 grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="og_title" class="block text-sm font-medium text-slate-700">OG Title</label>
                                <input id="og_title" name="og_title" type="text" value="{{ old('og_title', $meta->og_title) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="og_image" class="block text-sm font-medium text-slate-700">OG Image</label>
                                <input id="og_image" name="og_image" type="file" accept="image/*" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @if($meta->og_image)
                                    <div class="mt-3 flex items-center gap-3 rounded-md border border-slate-200 bg-white p-3">
                                        <img src="{{ $meta->og_image }}" alt="Current OG image" class="h-16 w-24 rounded object-cover">
                                        <div class="min-w-0 text-xs text-slate-600">
                                            <div class="font-medium text-slate-700">Current image</div>
                                            <div class="truncate">{{ $meta->og_image }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="og_description" class="block text-sm font-medium text-slate-700">OG Description</label>
                            <textarea id="og_description" name="og_description" rows="3" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('og_description', $meta->og_description) }}</textarea>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Twitter Card</h2>
                        <div class="mt-4 grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="twitter_title" class="block text-sm font-medium text-slate-700">Twitter Title</label>
                                <input id="twitter_title" name="twitter_title" type="text" value="{{ old('twitter_title', $meta->twitter_title) }}" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="twitter_image" class="block text-sm font-medium text-slate-700">Twitter Image</label>
                                <input id="twitter_image" name="twitter_image" type="file" accept="image/*" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @if($meta->twitter_image)
                                    <div class="mt-3 flex items-center gap-3 rounded-md border border-slate-200 bg-white p-3">
                                        <img src="{{ $meta->twitter_image }}" alt="Current Twitter image" class="h-16 w-24 rounded object-cover">
                                        <div class="min-w-0 text-xs text-slate-600">
                                            <div class="font-medium text-slate-700">Current image</div>
                                            <div class="truncate">{{ $meta->twitter_image }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="twitter_description" class="block text-sm font-medium text-slate-700">Twitter Description</label>
                            <textarea id="twitter_description" name="twitter_description" rows="3" class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('twitter_description', $meta->twitter_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('seo.meta') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        {{ $method === 'POST' ? 'Create' : 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
