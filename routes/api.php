<?php

use App\Http\Controllers\API\V1\Auth\ForgotPasswordApiController;
use App\Http\Controllers\API\V1\Auth\LoginApiController;
use App\Http\Controllers\API\V1\Auth\RegisterApiController;
use App\Http\Controllers\API\V1\Auth\ResetPasswordApiController;
use App\Http\Controllers\API\V1\StrategyController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(StrategyController::class)->group(function () {
        Route::post('/strategies', 'store');
        Route::put('/strategies/{strategy}', 'updateName');
        Route::post('/strategies/{strategy}/activate', 'activateStrategy');
    });

});

Route::middleware('guest')->group(function () {

    Route::post('/register', [RegisterApiController::class, 'register']);
    Route::post('/login', [LoginApiController::class, 'login']);
    Route::post('/forgot-password', [ForgotPasswordApiController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [ResetPasswordApiController::class, 'reset']);

});

