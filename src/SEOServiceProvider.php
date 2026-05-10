<?php

namespace Nirjon\LaravelSeo;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\View\Components\SeoTags;
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

        Blade::component('seo::tags', SeoTags::class);

        Route::get('/sitemap.xml', [SitemapController::class, 'index']);
        Route::get('/robots.txt', [SeoFilesController::class, 'robots']);
        Route::get('/llms.txt', [SeoFilesController::class, 'llms']);
        Route::get('/.well-known/security.txt', [SeoFilesController::class, 'security']);

        // Register the SEO Redirect Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\SeoRedirectMiddleware::class);

        // Register the SEO 404 Monitor Middleware
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Nirjon\LaravelSeo\Http\Middleware\Seo404MonitorMiddleware::class);

        if ($this->app->runningInConsole()) {
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
}
