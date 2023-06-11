<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/', function (){
    return redirect('/wallets');
})->name('home');

Route::middleware('auth')->group(function (){

    Route::get('/wallets', [WalletController::class, 'index']);
    Route::get('/wallets/create', [WalletController::class, 'create']);
    Route::post('/wallets/store', [WalletController::class, 'store']);
    Route::get('/wallets/{wallet}/edit', [WalletController::class, 'edit']);
    Route::put('/wallets/{wallet}', [WalletController::class, 'update']);
    Route::delete('/wallets/{wallet}', [WalletController::class, 'delete']);
    Route::get('/wallets/{wallet}/balances', [WalletController::class, 'balances']);
    Route::get('/wallets/{wallet}/balances/{balance}/edit', [WalletController::class, 'editBalance']);
    Route::put('/wallets/{wallet}/balances/{balance}', [WalletController::class, 'updateBalance']);

    Route::get('/records/{wallet}', [RecordController::class, 'index'])->name('records');
    Route::get('/records/{wallet}/{record}/edit', [RecordController::class, 'edit'])->name('editRecord');
    Route::put('/records/{wallet}/{record}', [RecordController::class, 'update'])->name('updateRecord');
    Route::delete('/records/{wallet}/{record}/delete', [RecordController::class, 'delete'])->name('deleteRecord');
    Route::get('/records/{wallet}/newrecord', [RecordController::class, 'newRecord'])->name('newrecord');
    Route::post('/records/{wallet}/pay', [RecordController::class, 'pay'])->name('pay');
    Route::post('/records/{wallet}/topup', [RecordController::class, 'topup'])->name('topup');
    Route::post('/records/{wallet}/transfer', [RecordController::class, 'transfer'])->name('transfer');
});

Route::resource('/currencies', CurrencyController::class)->except(['show']);


Route::get('/categories', [CategoryController::class,'index']);

