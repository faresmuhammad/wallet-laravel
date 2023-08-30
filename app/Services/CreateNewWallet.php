<?php

namespace App\Services;

use App\Http\Requests\StoreOrUpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;

class CreateNewWallet{

    public function create(StoreOrUpdateWalletRequest $request): WalletResource
    {
        $wallet = Wallet::create($request->validated());
        return new WalletResource($wallet);
    }

}
