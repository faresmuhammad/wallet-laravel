<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\PayRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Http\Resources\TransferResource;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\Wallet;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * This service class is responsible for creating a new record and handling related parts
 * main methods are pay, topup and transfer
 * helper method is updateBalancesUponRules to update wallet balance upon strategy rules
 */
class NewRecord
{

    public function pay(Wallet $wallet, PayRequest $request): JsonResponse
    {
        if (isset($request->currency) && $wallet->currency !== $request->currency) {
            throw new HttpResponseException(
                apiResponse("Error while pay process.", [], ["Can't pay with a different currency!"], status: 403)
            );
        }
        DB::beginTransaction();
        $record = $wallet->records()->create(
            $request->all() +
            [
                'type' => RecordType::Expense,
            ]
        );
        $wallet->update([
            'balance' => $wallet->balance - $record->amount,
        ]);
        DB::commit();
        return apiResponse('Record created!', new RecordResource($record), status: 201);
    }

    /**
     * @throws \Throwable
     */
    public function topup(Wallet $wallet, Request $request): JsonResponse
    {
        if (isset($request->currency) && $wallet->currency !== $request->currency ) {
            throw new HttpResponseException(
                apiResponse("Error while topup process.", [], ["Can't topup with a different currency!"], status: 403)
            );
        }
        //todo: update balance per date
        DB::beginTransaction();
        $record = $wallet->records()->create([
            'amount' => $request->amount,
            'name' => $request->name ?? 'No Name',
            'category_id' => $request->category_id,
            'date' => $request->date ?? now(),
            'type' => RecordType::Income
        ]);
        $wallet->update([
            'balance' => $wallet->balance + $record->amount,
        ]);

        DB::commit();
        return apiResponse('Record created!', new RecordResource($record), status: 201);

    }

    /**
     * @throws \Throwable
     */
    public function transfer(TransferRecordRequest $request): JsonResponse
    {
        DB::beginTransaction();
        $validated = $request->validated();

        $senderWallet = Wallet::find($validated['sender_wallet']);
        $receiverWallet = Wallet::find($validated['receiver_wallet']);

        throw_if($senderWallet->currency_id != $receiverWallet->currency_id, new HttpResponseException(
            apiResponse("Error while transfer process.", [], ["Can't transfer to a different currency!"], status: 403)
        ));


        $record = Record::create([
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'date' => $validated['date'] ?? now(),
            'type' => RecordType::Transfer
        ]);
        $transfer = $record->transfer()->create([
            'amount' => $validated['amount'],
            'sender_wallet' => $validated['sender_wallet'],
            'receiver_wallet' => $validated['receiver_wallet'],
        ]);
        $senderWallet->update([
            'balance' => $senderWallet->balance - $record->amount,
        ]);
        $receiverWallet->update([
            'balance' => $receiverWallet->balance + $record->amount,
        ]);
        //todo: update balance per date
        DB::commit();
        return apiResponse("Transfer success!", new TransferResource($transfer), status: 201);
    }


}
