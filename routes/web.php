<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminContentController;
use App\Http\Controllers\AdminSectionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SitePageController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media.show');
Route::get('/product/{productSlug}', [ShopController::class, 'show'])->name('shop.product.show');
Route::get('/product/{product}/whatsapp', [ShopController::class, 'redirectToProductWhatsapp'])->name('shop.product.whatsapp');
Route::post('/cart/{product}', [ShopController::class, 'addToCart'])->name('shop.cart.add');
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart.index');
Route::get('/cart/checkout/whatsapp', [ShopController::class, 'redirectToCartWhatsapp'])->name('shop.cart.whatsapp');
Route::post('/cart/checkout/register', [CustomerAccountController::class, 'registerFromCart'])->name('shop.cart.register');
Route::patch('/cart/{product}', [ShopController::class, 'updateCart'])->name('shop.cart.update');
Route::delete('/cart/{product}', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login.php', [AuthController::class, 'showLogin'])->name('login.shortcut');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/login.php', [AuthController::class, 'login'])->name('login.attempt.shortcut');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/account/checkout', [CustomerAccountController::class, 'checkout'])->name('account.checkout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard.php', [DashboardController::class, 'index'])->name('dashboard.shortcut');
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/pages', [AdminContentController::class, 'pagesIndex'])->name('pages.index');
    Route::get('/pages/create', [AdminContentController::class, 'pagesCreate'])->name('pages.create');
    Route::get('/pages/{page}/preview', [AdminContentController::class, 'pagesPreview'])->name('pages.preview');
    Route::get('/pages/{page}/edit', [AdminContentController::class, 'pagesEdit'])->name('pages.edit');
    Route::get('/new-post', [AdminContentController::class, 'pagesCreate'])->name('pages.new-post');
    Route::post('/pages', [AdminContentController::class, 'pagesStore'])->name('pages.store');
    Route::put('/pages/{page}', [AdminContentController::class, 'pagesUpdate'])->name('pages.update');
    Route::post('/pages/bulk-action', [AdminContentController::class, 'pagesBulkAction'])->name('pages.bulk-action');
    Route::delete('/pages/{page}', [AdminContentController::class, 'pagesDestroy'])->name('pages.destroy');

    Route::get('/categories', [AdminContentController::class, 'categoriesIndex'])->name('categories.index');
    Route::get('/categories/create', [AdminContentController::class, 'categoriesCreate'])->name('categories.create');
    Route::post('/categories', [AdminContentController::class, 'categoriesStore'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminContentController::class, 'categoriesEdit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminContentController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminContentController::class, 'categoriesDestroy'])->name('categories.destroy');

    Route::get('/products', [AdminContentController::class, 'productsIndex'])->name('products.index');
    Route::get('/products/create', [AdminContentController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [AdminContentController::class, 'productsStore'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminContentController::class, 'productsEdit'])->name('products.edit');
    Route::put('/products/{product}', [AdminContentController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{product}', [AdminContentController::class, 'productsDestroy'])->name('products.destroy');

    Route::get('/admin/{section}', [AdminSectionController::class, 'show'])
        ->whereIn('section', [
            'categories',
            'sub-categories',
            'products',
            'orders',
            'invoices',
            'enquiries',
            'users',
            'homepage-content',
            'sliders',
            'pages',
            'services',
            'testimonials',
            'media',
            'menus',
            'settings',
            'profile',
        ])
        ->name('admin.section');
    Route::post('/admin/homepage-content', [AdminSectionController::class, 'updateHomepageContent'])
        ->name('admin.homepage-content.update');
    Route::post('/admin/testimonials', [AdminSectionController::class, 'updateTestimonials'])
        ->name('admin.testimonials.update');
    Route::post('/admin/menus', [AdminSectionController::class, 'updateMenus'])
        ->name('admin.menus.update');
    Route::post('/admin/settings', [AdminSectionController::class, 'updateSettings'])
        ->name('admin.settings.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout.php', [AuthController::class, 'logout'])->name('logout.shortcut');
});

Route::get('/{page:slug}', [SitePageController::class, 'show'])->name('site-pages.show');
