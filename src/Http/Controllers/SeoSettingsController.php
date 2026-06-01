<?php

namespace Nirjon\LaravelSeo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nirjon\LaravelSeo\Models\SeoSetting;

class SeoSettingsController extends Controller
{
    private array $modules = [
        'meta' => 'Meta Tags',
        'sitemaps' => 'Sitemap',
        'schema' => 'Schema',
        'redirections' => 'Redirects',
        'image_seo' => 'Image SEO',
        'minify_html' => 'Minification',
    ];

    public function index()
    {
        $settings = SeoSetting::whereIn('key', $this->settingKeys())->pluck('value', 'key')->all();
        $modules = $this->modules;
        $moduleValues = [];

        foreach ($modules as $key => $label) {
            $settingKey = $this->settingKey($key);
            $moduleValues[$key] = array_key_exists($settingKey, $settings)
                ? filter_var($settings[$settingKey], FILTER_VALIDATE_BOOLEAN)
                : (bool) config("seo.modules.{$key}", false);
        }

        return view('seo::admin.settings', compact('modules', 'moduleValues'));
    }

    public function update(Request $request)
    {
        $enabledModules = $request->input('modules', []);

        foreach ($this->modules as $key => $label) {
            SeoSetting::updateOrCreate(
                ['key' => $this->settingKey($key)],
                ['value' => in_array($key, $enabledModules, true) ? '1' : '0']
            );
        }

        return redirect('seo-admin/settings')->with('status', 'SEO settings saved successfully.');
    }

    private function settingKey(string $module): string
    {
        return "modules.{$module}";
    }

    private function settingKeys(): array
    {
        return array_map(fn ($key) => $this->settingKey($key), array_keys($this->modules));
    }
}
