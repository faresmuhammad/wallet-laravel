<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $service)
    {
    }

    public function index()
    {
        return WalletResource::collection(auth()->user()->wallets);
    }

    public function show(Wallet $wallet)
    {
        return apiResponse('Wallet found!', $this->service->showWalletResource($wallet));
    }

    public function store(Request $request)
    {
        $wallet = $this->service->createWallet($request);
        return apiResponse('Wallet Created Successfully!', new WalletResource($wallet), status: 201);
    }

    public function update(Request $request, Wallet $wallet)
    {
        $walletResource = new WalletResource($this->service->updateWallet($request, $wallet));
        return apiResponse('Wallet Updated Successfully!', $walletResource);
    }

    public function destroy(Wallet $wallet)
    {
        $wallet->delete();
        return apiResponse('Wallet deleted!');
    }
}
