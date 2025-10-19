<?php

use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SwipeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Get a paginated list of user recommendations
    Route::get('/users/recommendations', [RecommendationController::class, 'index']);

    // Submit a like or nope action
    Route::post('/swipes', [SwipeController::class, 'store']);

    // Get the list of users the current user has liked
    Route::get('/users/liked', [UserController::class, 'liked']);
});
