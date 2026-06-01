@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
@endpush

@section(config('seo.section', 'content'))
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

            <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save
                </button>
            </div>
        </form>
        </div>
    </main>
@endsection
