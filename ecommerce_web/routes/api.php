<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('categories')->group(function () {
    
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('categories.show');
    
    Route::middleware('admin')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});

Route::prefix('products')->group(function () {
   
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
   
    Route::middleware('admin')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});


// Order API routes - Support both session and token authentication
Route::middleware(['auth'])->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
    Route::post('/', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::put('/{order}', [OrderController::class, 'update'])->name('api.orders.update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('api.orders.destroy');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/statistics/overview', [OrderController::class, 'statistics'])->name('api.orders.statistics');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('api.orders.update-status');
    });
});
