<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', auth()->user()->id)->orderBy('created_at', 'ASC')->get();
        $walletsWitBalance = $wallets->map(function (Wallet $wallet) {
            return [
                'wallet' => $wallet,
                'balance' => $wallet->balances->sortByDesc('updated_at')->first()
            ];
        });
//        dd($walletsWitBalance,$wallets);
        return view('wallet.index')->with('walletsWitBalance', $walletsWitBalance);
    }

    public function create()
    {
        return view('wallet.create');
    }

    public function store()
    {
        $wallet = Wallet::create([
            'name' => \request('name'),
            'color' => \request('color'),
            'initial_balance' => \request('initial-balance'),
            'include_to_stats' => \request('include-stats') ?? false,
//            'user_id' => auth()->user()->id
        ]);
        return redirect('/wallets');
    }

    public function edit(Wallet $wallet)
    {
        return view('wallet.edit')->with('wallet', $wallet);
    }

    public function update(Wallet $wallet)
    {
        $wallet->update([
            'name' => \request('name'),
            'color' => \request('color'),
            'initial_balance' => \request('initial-balance'),
            'include_to_stats' => \request('include-stats') ?? false,
        ]);
        return redirect('/wallets');
    }

    public function delete(Wallet $wallet)
    {
        $wallet->delete();
        return redirect('/wallets')->with('message', "$wallet->name is deleted successfully");
    }

    //For Demonstration
    public function balances(Wallet $wallet)
    {
        $balances = $wallet->balances;
        return view('wallet.balances', compact('balances'));
    }

    //For Demonstration
    public function editBalance(Wallet $wallet, Balance $balance)
    {
        return view('wallet.edit_balance', compact('balance'));
    }

    //For Demonstration
    public function updateBalance(Wallet $wallet, Balance $balance)
    {
        $balance->update([
            'value' => \request('value')
        ]);
        return redirect("/wallets/$wallet->id/balances");
    }
}
