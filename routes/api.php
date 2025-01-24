<?php

use App\Http\Controllers\API\V1\Auth\ForgotPasswordApiController;
use App\Http\Controllers\API\V1\Auth\LoginApiController;
use App\Http\Controllers\API\V1\Auth\RegisterApiController;
use App\Http\Controllers\API\V1\Auth\ResetPasswordApiController;
use App\Http\Controllers\API\V1\RecordController;
use App\Http\Controllers\API\V1\WalletController;
use App\Http\Controllers\API\V1\CategoryLabelController;
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

    Route::controller(WalletController::class)->group(function () {
        Route::get('/wallets', 'index');
        Route::get('/wallets/{wallet}', 'show');
        Route::post('/wallets', 'store');
        Route::put('/wallets/{wallet}', 'update');
        Route::delete('/wallets/{wallet}', 'destroy');
    });

    Route::controller(RecordController::class)->group(function () {
        Route::get('/records/{wallet}', 'index');
        Route::post('/pay/{wallet}', 'pay');
        Route::post('/topup/{wallet}', 'topup');
        Route::post('/transfer', 'transfer');
        Route::put('/update-record/{record}', 'updateRecord');
        Route::put('/update-transfer/{record}', 'updateTransfer');
        Route::delete('/delete-record/{record}', 'delete');
    });

    Route::controller(CategoryLabelController::class)->group(function () {
        Route::get('/categories', 'categories');
        Route::get('/labels', 'labels');
        Route::post('/categories', 'storeCategory');
        Route::post('/labels', 'storeLabel');
        Route::put('/categories/{category}', 'updateCategory');
        Route::put('/labels/{label}', 'updateLabel');
        Route::delete('/categories/{category}', 'destroyCategory');
        Route::delete('/labels/{label}', 'destroyLabel');
    });
});

Route::middleware('guest')->group(function () {

    Route::post('/register', [RegisterApiController::class, 'register']);
    Route::post('/login', [LoginApiController::class, 'login']);
    Route::post('/forgot-password', [ForgotPasswordApiController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [ResetPasswordApiController::class, 'reset']);

});

