<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Nirjon\LaravelSeo\Models\SeoRedirect;

class SeoRedirectController extends Controller
{
    private array $matchTypes = [
        'exact' => 'Exact Match',
        'starts_with' => 'Starts With',
        'contains' => 'Contains',
    ];

    private array $redirectTypes = [
        301 => '301 Permanent Move',
        302 => '302 Temporary Move',
        307 => '307 Temporary Redirect',
        410 => '410 Content Deleted',
        451 => '451 Unavailable For Legal Reasons',
    ];

    public function index(Request $request)
    {
        $redirects = SeoRedirect::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('source_url', 'like', "%{$search}%")
                        ->orWhere('destination_url', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('seo::admin.redirects.index', [
            'redirects' => $redirects,
            'matchTypes' => $this->matchTypes,
            'redirectTypes' => $this->redirectTypes,
        ]);
    }

    public function create()
    {
        return view('seo::admin.redirects.form', [
            'redirect' => new SeoRedirect([
                'match_type' => 'exact',
                'redirect_type' => 301,
                'ignore_case' => true,
                'is_active' => true,
            ]),
            'matchTypes' => $this->matchTypes,
            'redirectTypes' => $this->redirectTypes,
            'method' => 'POST',
            'action' => route('seo.redirects.store'),
            'title' => 'Add Redirect',
        ]);
    }

    public function store(Request $request)
    {
        SeoRedirect::create($this->validatedData($request));

        return redirect()->route('seo.redirects')->with('status', 'Redirect created successfully.');
    }

    public function edit(SeoRedirect $redirect)
    {
        return view('seo::admin.redirects.form', [
            'redirect' => $redirect,
            'matchTypes' => $this->matchTypes,
            'redirectTypes' => $this->redirectTypes,
            'method' => 'PUT',
            'action' => route('seo.redirects.update', $redirect),
            'title' => 'Edit Redirect',
        ]);
    }

    public function update(Request $request, SeoRedirect $redirect)
    {
        $redirect->update($this->validatedData($request));

        return redirect()->route('seo.redirects')->with('status', 'Redirect updated successfully.');
    }

    public function destroy(SeoRedirect $redirect)
    {
        $redirect->delete();

        return redirect()->route('seo.redirects')->with('status', 'Redirect deleted successfully.');
    }

    public function export()
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'source_url',
            'destination_url',
            'redirect_type',
            'match_type',
            'ignore_case',
            'is_active',
        ]);

        SeoRedirect::query()
            ->orderBy('id')
            ->each(function (SeoRedirect $redirect) use ($handle) {
                fputcsv($handle, [
                    $redirect->source_url,
                    $redirect->destination_url,
                    $redirect->redirect_type,
                    $redirect->match_type,
                    $redirect->ignore_case ? 1 : 0,
                    $redirect->is_active ? 1 : 0,
                ]);
            });

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="seo-redirects-' . now()->format('Y-m-d-His') . '.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'redirect_file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $handle = fopen($request->file('redirect_file')->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('status', 'Could not read the uploaded redirect CSV file.');
        }

        $headers = $this->normalizeCsvHeaders(fgetcsv($handle) ?: []);

        if (! in_array('source_url', $headers, true) || ! in_array('destination_url', $headers, true)) {
            fclose($handle);

            return back()->with('status', 'Import failed. CSV must include source_url and destination_url columns.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, array_slice($row, 0, count($headers)));
            $sourceUrl = trim((string) Arr::get($data, 'source_url', ''));

            if ($sourceUrl === '') {
                $skipped++;
                continue;
            }

            $redirectType = (int) (Arr::get($data, 'redirect_type') ?: 301);
            $matchType = (string) (Arr::get($data, 'match_type') ?: 'exact');

            if (! isset($this->redirectTypes[$redirectType]) || ! isset($this->matchTypes[$matchType])) {
                $skipped++;
                continue;
            }

            $payload = [
                'destination_url' => trim((string) Arr::get($data, 'destination_url', '')) ?: null,
                'redirect_type' => $redirectType,
                'match_type' => $matchType,
                'ignore_case' => $this->csvBoolean(Arr::get($data, 'ignore_case'), true),
                'is_active' => $this->csvBoolean(Arr::get($data, 'is_active'), true),
            ];

            if (in_array($redirectType, [410, 451], true)) {
                $payload['destination_url'] = null;
            }

            $redirect = SeoRedirect::query()->firstOrNew(['source_url' => $sourceUrl]);
            $redirect->fill($payload);
            $redirect->exists ? $updated++ : $created++;
            $redirect->save();
        }

        fclose($handle);

        return redirect()
            ->route('seo.redirects')
            ->with('status', "Redirect import complete. Created: {$created}. Updated: {$updated}. Skipped: {$skipped}.");
    }

    public function status(SeoRedirect $redirect)
    {
        $redirect->update(['is_active' => ! $redirect->is_active]);

        return redirect()->route('seo.redirects')->with('status', 'Redirect status updated successfully.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'source_url' => ['required', 'string', 'max:2048'],
            'destination_url' => ['nullable', 'string', 'max:2048'],
            'match_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->matchTypes))],
            'redirect_type' => ['required', 'integer', 'in:' . implode(',', array_keys($this->redirectTypes))],
            'ignore_case' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['ignore_case'] = $request->boolean('ignore_case');
        $data['is_active'] = $request->boolean('is_active');

        if (in_array((int) $data['redirect_type'], [410, 451], true)) {
            $data['destination_url'] = null;
        }

        return $data;
    }

    private function normalizeCsvHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower(trim((string) $header));
            $header = ltrim($header, "\xEF\xBB\xBF");
            $header = str_replace([' ', '-'], '_', $header);

            return preg_replace('/[^a-z0-9_]/', '', $header) ?: '';
        }, $headers);
    }

    private function csvBoolean($value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on', 'active'], true);
    }
}
