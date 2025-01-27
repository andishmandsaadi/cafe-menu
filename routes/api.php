<?php

use App\Http\Controllers\Api\CafeOwnerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('cafe-owners', CafeOwnerController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);