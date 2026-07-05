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
                    <p class="mt-1 text-sm text-slate-600">Use full URLs or relative paths. Example: <code>/old-page</code></p>
                </div>

                <a href="{{ route('seo.redirects') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Redirects</a>
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

            <form method="POST" action="{{ $action }}" class="rounded-lg border border-slate-200 bg-white px-6 py-6 shadow-sm">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="space-y-6">
                    <div>
                        <label for="source_url" class="block text-sm font-medium text-slate-700">Source URL <span class="text-red-600">*</span></label>
                        <input
                            id="source_url"
                            name="source_url"
                            type="text"
                            required
                            value="{{ old('source_url', $redirect->source_url) }}"
                            placeholder="https://example.com/old-page or /old-page"
                            class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="match_type" class="block text-sm font-medium text-slate-700">Match Type <span class="text-red-600">*</span></label>
                        <select
                            id="match_type"
                            name="match_type"
                            required
                            class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            @foreach($matchTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('match_type', $redirect->match_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input
                            type="checkbox"
                            name="ignore_case"
                            value="1"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            @checked(old('ignore_case', $redirect->ignore_case ?? true))
                        >
                        Ignore Case
                    </label>

                    <div>
                        <label for="destination_url" class="block text-sm font-medium text-slate-700">Destination URL</label>
                        <input
                            id="destination_url"
                            name="destination_url"
                            type="text"
                            value="{{ old('destination_url', $redirect->destination_url) }}"
                            placeholder="https://example.com/new-page or /new-page"
                            class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <p class="mt-2 text-xs text-slate-500">Destination is ignored for 410 and 451 responses.</p>
                    </div>

                    <fieldset>
                        <legend class="text-sm font-medium text-slate-700">Redirect Type <span class="text-red-600">*</span></legend>
                        <div class="mt-3 flex flex-wrap gap-4">
                            @foreach($redirectTypes as $code => $label)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input
                                        type="radio"
                                        name="redirect_type"
                                        value="{{ $code }}"
                                        class="border-slate-300 text-blue-600 focus:ring-blue-500"
                                        @checked((int) old('redirect_type', $redirect->redirect_type) === (int) $code)
                                    >
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></legend>
                        <div class="mt-3 flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="is_active" value="1" class="border-slate-300 text-blue-600 focus:ring-blue-500" @checked((int) old('is_active', $redirect->is_active ?? 1) === 1)>
                                Active
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="is_active" value="0" class="border-slate-300 text-blue-600 focus:ring-blue-500" @checked((int) old('is_active', $redirect->is_active ?? 1) === 0)>
                                Inactive
                            </label>
                        </div>
                    </fieldset>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('seo.redirects') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        {{ $method === 'POST' ? 'Create' : 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
