<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CafeOwnerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('api/cafe-owners', CafeOwnerController::class);
Route::apiResource('api/categories', CategoryController::class);
Route::apiResource('api/products', ProductController::class);
Route::get('/', function () {
    return view('welcome');
});
// Owners management page (homepage)
// Route::get('/', [CafeOwnerController::class, 'index']);
// Route::get('/owners/{id}', [CafeOwnerController::class, 'show']);

// Categories management within an owner
Route::get('/owners/{id}/categories', [CategoryController::class, 'index']);
Route::post('/owners/{id}/categories', [CategoryController::class, 'store']);
Route::put('/owners/{id}/categories/{category_id}', [CategoryController::class, 'update']);
Route::delete('/owners/{id}/categories/{category_id}', [CategoryController::class, 'destroy']);

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');
