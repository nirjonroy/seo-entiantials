<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
}
