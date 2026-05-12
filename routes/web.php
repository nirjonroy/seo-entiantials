<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Services\BreadcrumbService;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (BreadcrumbService $breadcrumbs) {
    
    $breadcrumbs->add('Products', url('/products'))
                ->add('Laptop', url('/products/laptop'))
                ->add('MacBook Pro');

    return view('welcome');
});

Route::get('/{slug}', [\Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController::class, 'show']);
