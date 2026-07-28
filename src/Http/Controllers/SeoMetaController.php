<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Nirjon\LaravelSeo\Models\SeoMeta;

class SeoMetaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $metas = SeoMeta::query()
            ->where('seoable_type', 'url')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('url_path', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('seo::admin.meta.index', compact('metas', 'search'));
    }

    public function create()
    {
        return view('seo::admin.meta.form', [
            'meta' => new SeoMeta(['robots_tag' => 'index, follow', 'is_active' => true]),
            'title' => 'Add Meta Tags',
            'action' => route('seo.meta.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['url_path'] = $this->normalizePath($data['url_path']);
        $data = $this->storeUploadedImages($request, $data);

        $this->ensureUniquePath($data['url_path']);

        SeoMeta::create(array_merge($data, [
            'seoable_type' => 'url',
            'seoable_id' => 0,
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('seo.meta')->with('status', 'Meta tags created successfully.');
    }

    public function edit(SeoMeta $meta)
    {
        abort_unless($meta->seoable_type === 'url', 404);

        return view('seo::admin.meta.form', [
            'meta' => $meta,
            'title' => 'Edit Meta Tags',
            'action' => route('seo.meta.update', $meta),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, SeoMeta $meta)
    {
        abort_unless($meta->seoable_type === 'url', 404);

        $data = $this->validated($request);
        $data['url_path'] = $this->normalizePath($data['url_path']);
        $data = $this->storeUploadedImages($request, $data, $meta);

        $this->ensureUniquePath($data['url_path'], $meta->id);

        $meta->update(array_merge($data, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('seo.meta')->with('status', 'Meta tags updated successfully.');
    }

    public function status(SeoMeta $meta)
    {
        abort_unless($meta->seoable_type === 'url', 404);

        $meta->update(['is_active' => ! (bool) $meta->is_active]);

        return redirect()->route('seo.meta')->with('status', 'Meta tag status updated.');
    }

    public function destroy(SeoMeta $meta)
    {
        abort_unless($meta->seoable_type === 'url', 404);

        $meta->delete();

        return redirect()->route('seo.meta')->with('status', 'Meta tags deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'url_path' => ['required', 'string', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:255'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'robots_tag' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string'],
            'twitter_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function storeUploadedImages(Request $request, array $data, ?SeoMeta $meta = null): array
    {
        foreach (['og_image', 'twitter_image'] as $field) {
            if (! $request->hasFile($field)) {
                unset($data[$field]);
                continue;
            }

            if ($meta && $meta->{$field}) {
                $this->deleteStoredImage($meta->{$field});
            }

            $path = $request->file($field)->store('seo-meta-images', 'public');
            $data[$field] = Storage::disk('public')->url($path);
        }

        return $data;
    }

    private function deleteStoredImage(string $url): void
    {
        $storagePrefix = '/storage/';

        if (! str_contains($url, $storagePrefix)) {
            return;
        }

        $path = Str::after($url, $storagePrefix);

        if (str_starts_with($path, 'seo-meta-images/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);
        $parsedPath = parse_url($path, PHP_URL_PATH);
        $path = is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : $path;
        $path = '/' . ltrim($path, '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');

        return Str::limit($path, 2048, '');
    }

    private function ensureUniquePath(string $path, ?int $ignoreId = null): void
    {
        $exists = SeoMeta::query()
            ->where('seoable_type', 'url')
            ->where('url_path', $path)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'url_path' => 'Meta tags already exist for this URL path.',
            ]);
        }
    }
}
