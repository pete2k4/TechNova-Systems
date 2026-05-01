<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'index'])->name('home');

Route::get('/home', [HelloWorldController::class, 'home']);
Route::get('/helloWorld', [HelloWorldController::class, 'helloWorld']);

Route::prefix('marketplace')->group(function (): void {
    Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/category/{slug}', [MarketplaceController::class, 'category'])->name('marketplace.category');
    Route::get('/product/{slug}', [MarketplaceController::class, 'show'])->name('marketplace.product');
    Route::post('/cart/add/{productId}', [MarketplaceController::class, 'addToCart'])->name('marketplace.addToCart');
    Route::get('/cart', [MarketplaceController::class, 'cart'])->name('marketplace.cart');
    Route::post('/cart/remove/{productId}', [MarketplaceController::class, 'removeFromCart'])->name('marketplace.removeFromCart');
    Route::post('/cart/clear', [MarketplaceController::class, 'clearCart'])->name('marketplace.clearCart');
});

Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.show-checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::view('/checkout/payment-placeholder/{orderId}', 'checkout.payment-placeholder')
    ->whereNumber('orderId')
    ->name('checkout.payment-placeholder');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('dashboard');
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');

    Route::get('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create', [App\Http\Controllers\Admin\DiscountController::class, 'create'])->name('discounts.create');
    Route::post('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'store'])->name('discounts.store');
    Route::post('/discounts/{discount}/apply', [App\Http\Controllers\Admin\DiscountController::class, 'apply'])->name('discounts.apply');
    Route::post('/discounts/run-schedule', [App\Http\Controllers\Admin\DiscountController::class, 'runSchedule'])->name('discounts.run-schedule');
});
