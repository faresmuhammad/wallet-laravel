<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Resources\RecordResource;
use App\Http\Resources\WalletResource;
use App\Models\Record;
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

    public function correctBalance(Wallet $wallet, Request $request): Wallet
    {
        if ($request->actual_balance) {
            if ($wallet->balance == $request->actual_balance) return $wallet;
            $amount = $wallet->balance - $request->actual_balance;
            $wallet->records()->create([
                'name' => 'Error Amount',
                'amount' => abs($amount),
                'currency' => $wallet->currency,
                'type' => $amount > 0 ? RecordType::Expense : RecordType::Income,
                'date' => now(),
            ]);
            $wallet->update(['balance' => $request->actual_balance]);

        }
        if ($request->error_amount) {
            if ($request->error_amount == 0) return $wallet;
            $wallet->update(['balance' => $wallet->balance - $request->error_amount]);
            $record = $wallet->records()->create([
                'name' => 'Error Amount',
                'amount' => abs($request->error_amount),
                'currency' => $wallet->currency,
                'type' => $request->error_amount > 0 ? RecordType::Expense : RecordType::Income,
                'date' => now(),
            ]);
            $record->labels()->firstOrCreate([
                'name' => 'Error',
            ]);
        }
        return $wallet;
    }
}
