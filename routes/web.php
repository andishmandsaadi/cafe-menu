<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CafeOwnerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Owners management page (homepage)
Route::get('/owners/{id}/categories', [CategoryController::class, 'index']);
Route::post('/owners/{id}/categories', [CategoryController::class, 'store']);
Route::put('/owners/{id}/categories/{category_id}', [CategoryController::class, 'update']);
Route::delete('/owners/{id}/categories/{category_id}', [CategoryController::class, 'destroy']);


// Serve images directly from the public directory
Route::get('/categories/{filename}', function ($filename) {
    $path = public_path('categories/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path);
})->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');

Route::get('/products/{filename}', function ($filename) {
    $path = public_path('products/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path);
})->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');


// Home page listing all cafe owners
Route::get('/', [HomeController::class, 'index'])->name('home');

// Show a specific cafe owner's categories
Route::get('/{username}', [CafeOwnerController::class, 'showUsers'])->name('cafe.show');

// Show products in a specific category
Route::get('/{username}/{categorySlug}', [CategoryController::class, 'showCategory'])->name('category.show');

// Show product details
Route::get('/{username}/{categorySlug}/{productSlug}', [ProductController::class, 'showProduct'])->name('product.show');

Route::get('/{any}', function () {
    return view('layouts.admin');
})->where('any', '.*');