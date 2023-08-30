<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrUpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use App\Services\CreateNewWallet;
use App\Services\DeleteWallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = Wallet::where('user_id',auth()->id())->with('balances')->get();
        return WalletResource::collection($wallets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrUpdateWalletRequest $request, CreateNewWallet $service)
    {
       return $service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $wallet = Wallet::find($id);
        return new WalletResource($wallet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOrUpdateWalletRequest $request, $id)
    {
        $wallet = Wallet::find($id);
        $wallet->update($request->validated());
        return new WalletResource($wallet);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id,DeleteWallet $service)
    {
        $wallet = Wallet::find($id);
        return $service->delete($wallet);
    }
}
