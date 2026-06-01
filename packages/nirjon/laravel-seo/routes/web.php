<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Http\Controllers\PageGeneratorController;
use Nirjon\LaravelSeo\Http\Controllers\SeoSettingsController;

Route::middleware(['web'])->prefix('admin/seo-admin')->group(function () {
    Route::get('generator', [PageGeneratorController::class, 'index'])->name('seo.generator');
    Route::get('generator/api-pages', [PageGeneratorController::class, 'apiGetPages'])->name('seo.generator.apiPages');
    Route::post('generator/api-generate', [PageGeneratorController::class, 'apiGenerate'])->name('seo.generator.apiGenerate');
    Route::delete('generator/api-pages/{page}', [PageGeneratorController::class, 'apiDeletePage'])->name('seo.generator.apiDeletePage');

    Route::get('settings', [SeoSettingsController::class, 'index'])->name('seo.settings');
    Route::post('settings', [SeoSettingsController::class, 'update'])->name('seo.settings.update');
});
