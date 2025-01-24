<?php

namespace App\Services;

use App\Http\Resources\RecordResource;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletService
{

    public function showWalletResource(Wallet $wallet): array
    {
        return [
            'wallet' => new WalletResource($wallet),
            'records' => RecordResource::collection($wallet->records)
        ];
    }

    public function createWallet(Request $request): Wallet
    {
        return auth()->user()->wallets()->create([
            'name' => $request->name,
            'balance' => $request->balance,
            'currency' => $request->currency
        ]);
    }

    public function updateWallet(Request $request, Wallet $wallet): Wallet
    {
        $wallet->update([
            'name' => $request->name ?? $wallet->name,
            'balance' => $request->balance ?? $wallet->balance,
            'currency' => $request->currency ?? $wallet->currency
        ]);
        return $wallet;
    }

}
