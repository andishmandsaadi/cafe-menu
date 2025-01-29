<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CafeOwnerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('cafe-owners', CafeOwnerController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::post('cafe-owners/{id}/assign-categories', [CafeOwnerController::class, 'assignCategories']);
Route::delete('cafe-owners/{ownerId}/unassign-category/{categoryId}', [CafeOwnerController::class, 'unassignCategory']);
Route::post('categories/{id}', [CategoryController::class, 'update']);
Route::post('products/{id}', [ProductController::class, 'update']);
Route::post('cafe-owners/{ownerId}/assign-products', [CafeOwnerController::class, 'assignProducts']);
Route::delete('cafe-owners/{ownerId}/unassign-product/{categoryId}/{productId}', [CafeOwnerController::class, 'unassignProduct']);
Route::get('categories/{categoryId}/owner/{ownerId}/products', [CategoryController::class, 'getProductsByCategory']);
