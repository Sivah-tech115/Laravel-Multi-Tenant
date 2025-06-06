<?php

declare(strict_types=1);

use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Website\WebsiteController;
use App\Jobs\SeedTenantLang;
use Illuminate\Support\Facades\View;
/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    /*
    Route::get('/', function () {
        dd(tenant()->toArray());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
    */


    Route::get('/', [WebsiteController::class, 'index'])->name('tanant.website');
    Route::get('product-category/{categorySlug}/{subcategorySlug2?}/{subcategorySlug3?}', [WebsiteController::class, 'categoryProducts'])->name('category.product');

    Route::get('product/{productSlug}', [WebsiteController::class, 'singleProduct'])->name('single.product');

    Route::get('/dashboard', [ProductImportController::class, 'showForm'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/product-categories', [WebsiteController::class, 'AllCategories'])->name('categories.all');

    Route::view('/privacy-policy', 'tenant.welcome')->name('welcome');

    Route::get('/privacy-policy', function () {
        return view('app.website.pages.privacy-policy');
    })->name('privacy.policy');


    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::group(['middleware' => ['role:admin']], function () {
            Route::resource('users', UserController::class);
            Route::get('/import', [ProductImportController::class, 'showForm'])->name('import.form');
            Route::post('/import', [ProductImportController::class, 'import'])->name('import.submit');
            Route::post('/import/reset/{id}', [ProductImportController::class, 'import_reset'])->name('import.reset');
            Route::get('/merchants', [AdminController::class, 'showMerchant'])->name('merchants.all');
            Route::post('merchants/{id}/upload-image', [AdminController::class, 'uploadImage'])->name('merchant.uploadImage');
            Route::get('/offers', [AdminController::class, 'AllOffers'])->name('products.index');
            Route::get('/offerss', [AdminController::class, 'AllOfferss'])->name('products.ajax');
        });
    });

    require __DIR__ . '/tenant-auth.php';
});
