<?php

use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{id}/review', [ProductController::class, 'addReview'])->name('products.review');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{index}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{index}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');

Route::get('/orders/track', [OrderController::class, 'trackOrders'])->name('orders.track');
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Routes cho Admin - Chỉ admin mới có thể truy cập
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [AdminController::class, 'index'])->name('index');
    // TODO: Add admin routes for users, products, and orders if needed.
});


Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Profile routes được chuyển sang routes/settings.php
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
