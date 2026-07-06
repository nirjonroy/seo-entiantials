@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
@endpush

@section(config('seo.section', 'content'))
    <main class="min-h-screen bg-slate-100 px-6 py-10 text-slate-900">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Meta Tags</h1>
                    <p class="mt-1 text-sm text-slate-600">Manage SEO metadata for any public URL path in the host application.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('seo.settings') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">SEO Settings</a>
                    <a href="{{ route('seo.meta.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Add Meta Tags</a>
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <form method="GET" action="{{ route('seo.meta') }}" class="flex flex-wrap gap-3">
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search path, title, or description"
                            class="min-w-0 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Search</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-6 py-3">SL</th>
                                <th class="px-6 py-3">Path</th>
                                <th class="px-6 py-3">Title</th>
                                <th class="px-6 py-3">Robots</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($metas as $meta)
                                <tr class="align-top">
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $metas->firstItem() + $loop->index }}</td>
                                    <td class="max-w-xs break-words px-6 py-4 font-medium text-slate-900">{{ $meta->url_path }}</td>
                                    <td class="max-w-sm break-words px-6 py-4 text-slate-700">{{ $meta->title ?: 'No title' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $meta->robots_tag ?: 'index, follow' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <form method="POST" action="{{ route('seo.meta.status', $meta) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full px-3 py-1 text-xs font-semibold {{ $meta->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $meta->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ url($meta->url_path) }}" target="_blank" rel="noopener" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                            <a href="{{ route('seo.meta.edit', $meta) }}" class="font-medium text-amber-600 hover:text-amber-800">Edit</a>
                                            <form method="POST" action="{{ route('seo.meta.destroy', $meta) }}" onsubmit="return confirm('Delete these meta tags?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">No URL meta tags found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($metas->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $metas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
