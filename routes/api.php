<?php

use App\Http\Controllers\API\V1\Auth\ForgotPasswordApiController;
use App\Http\Controllers\API\V1\Auth\LoginApiController;
use App\Http\Controllers\API\V1\Auth\RegisterApiController;
use App\Http\Controllers\API\V1\Auth\ResetPasswordApiController;
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

    Route::apiResource('/wallets', \App\Http\Controllers\API\V1\WalletController::class);
    Route::prefix('/wallets/{wallet}')->group(function () {
        Route::get('/records', [\App\Http\Controllers\API\V1\RecordController::class, 'index']);
        Route::post('/pay', [\App\Http\Controllers\API\V1\RecordController::class, 'pay']);
        Route::post('/topup', [\App\Http\Controllers\API\V1\RecordController::class, 'topup']);
        Route::post('/transfer', [\App\Http\Controllers\API\V1\RecordController::class, 'transfer']);
    });

    Route::put('/records/{record}/update-record',[\App\Http\Controllers\API\V1\RecordController::class,'updateRecord']);
    Route::put('/records/{record}/update-transfer',[\App\Http\Controllers\API\V1\RecordController::class,'updateTransfer']);

    Route::prefix('/budgets')->group(function (){
        Route::get('/',[\App\Http\Controllers\API\V1\BudgetController::class,'index']);
        Route::get('/{budget}',[\App\Http\Controllers\API\V1\BudgetController::class,'view']);
        Route::post('/new',[\App\Http\Controllers\API\V1\BudgetController::class,'store']);
        Route::put('/{budget}/update',[\App\Http\Controllers\API\V1\BudgetController::class,'update']);
    });
});

Route::middleware('guest')->group(function () {

    Route::post('/register', [RegisterApiController::class, 'register']);
    Route::post('/login', [LoginApiController::class, 'login']);
    Route::post('/forgot-password', [ForgotPasswordApiController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [ResetPasswordApiController::class, 'reset']);

});


Route::middleware('auth:sanctum')->group(function () {
});
