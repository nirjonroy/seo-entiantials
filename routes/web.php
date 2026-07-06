<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController;
use Nirjon\LaravelSeo\Http\Controllers\PageGeneratorController;
use Nirjon\LaravelSeo\Http\Controllers\SeoMetaController;
use Nirjon\LaravelSeo\Http\Controllers\SeoRedirectController;
use Nirjon\LaravelSeo\Http\Controllers\SeoSettingsController;
use Nirjon\LaravelSeo\Http\Middleware\EnsureSeoAdminAuthenticated;

$adminMiddleware = array_values(array_unique(array_merge(
    (array) config('seo.admin.middleware', ['web', 'auth']),
    [EnsureSeoAdminAuthenticated::class]
)));

Route::middleware($adminMiddleware)->prefix('admin/seo-admin')->group(function () {
    Route::get('generator', [PageGeneratorController::class, 'index'])->name('seo.generator');
    Route::get('generator/api-pages', [PageGeneratorController::class, 'apiGetPages'])->name('seo.generator.apiPages');
    Route::get('generator/api-pages/{id}', [PageGeneratorController::class, 'apiShowPage'])->name('seo.generator.apiShowPage');
    Route::post('generator/api-generate', [PageGeneratorController::class, 'apiGenerate'])->name('seo.generator.apiGenerate');
    Route::delete('generator/api-pages/{id}', [PageGeneratorController::class, 'destroy'])->name('seo.generator.pages.destroy');

    Route::get('settings', [SeoSettingsController::class, 'index'])->name('seo.settings');
    Route::post('settings', [SeoSettingsController::class, 'update'])->name('seo.settings.update');

    Route::get('meta-tags', [SeoMetaController::class, 'index'])->name('seo.meta');
    Route::get('meta-tags/create', [SeoMetaController::class, 'create'])->name('seo.meta.create');
    Route::post('meta-tags', [SeoMetaController::class, 'store'])->name('seo.meta.store');
    Route::get('meta-tags/{meta}/edit', [SeoMetaController::class, 'edit'])->name('seo.meta.edit');
    Route::put('meta-tags/{meta}', [SeoMetaController::class, 'update'])->name('seo.meta.update');
    Route::patch('meta-tags/{meta}/status', [SeoMetaController::class, 'status'])->name('seo.meta.status');
    Route::delete('meta-tags/{meta}', [SeoMetaController::class, 'destroy'])->name('seo.meta.destroy');

    Route::get('redirects', [SeoRedirectController::class, 'index'])->name('seo.redirects');
    Route::get('redirects/create', [SeoRedirectController::class, 'create'])->name('seo.redirects.create');
    Route::post('redirects', [SeoRedirectController::class, 'store'])->name('seo.redirects.store');
    Route::get('redirects/{redirect}/edit', [SeoRedirectController::class, 'edit'])->name('seo.redirects.edit');
    Route::put('redirects/{redirect}', [SeoRedirectController::class, 'update'])->name('seo.redirects.update');
    Route::patch('redirects/{redirect}/status', [SeoRedirectController::class, 'status'])->name('seo.redirects.status');
    Route::delete('redirects/{redirect}', [SeoRedirectController::class, 'destroy'])->name('seo.redirects.destroy');
});

Route::middleware(['web'])->get('seo-media/{path}', [GeneratedPageController::class, 'media'])
    ->where('path', '.*')
    ->name('seo.media');
