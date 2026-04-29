<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ProductService API Routes (UTS - IAE)
|--------------------------------------------------------------------------
*/

/**
 * 1. PROVIDER: Get Product by ID
 * Method: GET
 * URL: /api/products/{id}
 */
Route::get('/products/{id}', [ProductController::class, 'show']);

/**
 * 2. CONSUMER: Get Product with Owner (Call UserService)
 * Method: GET
 * URL: /api/products/{id}/owner
 */
Route::get('/products/{id}/owner', [ProductController::class, 'showWithOwner']);
