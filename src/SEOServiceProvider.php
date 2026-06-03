<?php

namespace Nirjon\LaravelSeo;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\View\Components\SeoTags;
use Nirjon\LaravelSeo\View\Components\Breadcrumbs;
use Nirjon\LaravelSeo\Http\Controllers\SitemapController;
use Nirjon\LaravelSeo\Http\Controllers\SeoFilesController;

class SEOServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/seo.php', 'seo'
        );

        $this->app->singleton(\Nirjon\LaravelSeo\Services\BreadcrumbService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'seo');
        $this->loadSettingsFromDatabase();

        Blade::component('seo::tags', SeoTags::class);
        Blade::component('seo::breadcrumbs', Breadcrumbs::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Route::get('/sitemap.xml', [SitemapController::class, 'index']);
        Route::get('/robots.txt', [SeoFilesController::class, 'robots']);
        Route::get('/llms.txt', [SeoFilesController::class, 'llms']);
        Route::get('/.well-known/security.txt', [SeoFilesController::class, 'security']);

        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->prependMiddleware(\Nirjon\LaravelSeo\Http\Middleware\HandleSeoRedirects::class);

        // Register the SEO Redirect Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\SeoRedirectMiddleware::class);

        // Register the SEO 404 Monitor Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\Seo404MonitorMiddleware::class);

        // Render PageForge generated pages only after the host app returns 404.
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\GeneratedPageFallbackMiddleware::class);

        // Register the Auto Image SEO Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\AutoImageSeoMiddleware::class);

        // Register the Link Control Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\LinkControlMiddleware::class);

        // Register the Feed SEO Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\FeedSeoMiddleware::class);

        // Register the HTML Minifier Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\HtmlMinifierMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Nirjon\LaravelSeo\Console\Commands\InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/config/seo.php' => config_path('seo.php'),
            ], 'seo-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/seo'),
            ], 'seo-views');
        }
        Blade::directive('htmlSitemap', function () {
        return "<?php echo app(\Nirjon\LaravelSeo\Services\SitemapService::class)->generateHtml(); ?>";
    });
    }

    /**
     * Override module config values from database settings when available.
     */
    protected function loadSettingsFromDatabase(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('nirjon_seo_settings')) {
                return;
            }

            $settings = \Nirjon\LaravelSeo\Models\SeoSetting::where('key', 'like', 'modules.%')
                ->pluck('value', 'key');

            foreach ($settings as $key => $value) {
                $module = substr($key, strlen('modules.'));

                if ($module !== '') {
                    config(["seo.modules.{$module}" => filter_var($value, FILTER_VALIDATE_BOOLEAN)]);
                }
            }
        } catch (\Throwable $exception) {
            return;
        }
    }
}
