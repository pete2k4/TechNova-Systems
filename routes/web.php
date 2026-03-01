<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MarketplaceController;

Route::get('/', [MarketplaceController::class, 'index'])->name('home');

Route::get('/home', [HelloWorldController::class, 'home']);
Route::get('/helloWorld', [HelloWorldController::class, 'helloWorld']);

Route::prefix('marketplace')->group(function () {
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
