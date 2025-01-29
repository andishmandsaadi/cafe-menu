<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CafeOwnerController;
use App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Owners management page (homepage)
Route::get('/owners/{id}/categories', [CategoryController::class, 'index']);
Route::post('/owners/{id}/categories', [CategoryController::class, 'store']);
Route::put('/owners/{id}/categories/{category_id}', [CategoryController::class, 'update']);
Route::delete('/owners/{id}/categories/{category_id}', [CategoryController::class, 'destroy']);

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');