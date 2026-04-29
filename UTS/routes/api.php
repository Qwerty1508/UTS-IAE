<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| UserService API Routes (UTS - IAE)
|--------------------------------------------------------------------------
*/

/**
 * 1. PROVIDER: Get User by ID
 * Method: GET
 * URL: /api/users/{id}
 */
Route::get('/users/{id}', [UserController::class, 'show']);

/**
 * 2. CONSUMER: Get User with Orders (Call OrderService)
 * Method: GET
 * URL: /api/users/{id}/orders
 */
Route::get('/users/{id}/orders', [UserController::class, 'showWithOrders']);
