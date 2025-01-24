<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\PayRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Http\Resources\TransferResource;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\Transfer;
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

    public function pay(Wallet $wallet, PayRequest $request): Record
    {
        if (isset($request->currency) && $wallet->currency !== $request->currency) {
            throw new HttpResponseException(
                apiResponse("Error while pay process.", [], ["Can't pay with a different currency!"], status: 403)
            );
        }
        DB::beginTransaction();
        $record = $wallet->records()->create(
            $request->except('labels') +
            [
                'type' => RecordType::Expense,
            ]
        );
        $record->labels()->sync($request->labels);
        $wallet->update([
            'balance' => $wallet->balance - $record->amount,
        ]);
        DB::commit();
        return $record;
    }

    /**
     * @throws \Throwable
     */
    public function topup(Wallet $wallet, Request $request): Record
    {
        if (isset($request->currency) && $wallet->currency !== $request->currency) {
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
        $record->labels()->sync($request->labels);
        $wallet->update([
            'balance' => $wallet->balance + $record->amount,
        ]);

        DB::commit();
        return $record;

    }

    /**
     * @throws \Throwable
     */
    public function transfer(TransferRecordRequest $request): Transfer
    {
        DB::beginTransaction();

        $senderWallet = Wallet::find($request->sender_wallet);
        $receiverWallet = Wallet::find($request->receiver_wallet);

        throw_if($senderWallet->currency != $receiverWallet->currency, new HttpResponseException(
            apiResponse("Error while transfer process.", [], ["Can't transfer to a different currency!"], status: 403)
        ));


        $record = Record::create([
            'name' => $request->name ?? 'No Name',
            'amount' => $request->amount,
            'date' => $request->date ?? now(),
            'type' => RecordType::Transfer
        ]);

        $record->labels()->sync($request->labels);
        $transfer = $record->transfer()->create([
            'amount' => $request->amount,
            'sender_wallet' => $request->sender_wallet,
            'receiver_wallet' => $request->receiver_wallet,
        ]);
        $senderWallet->update([
            'balance' => $senderWallet->balance - $record->amount,
        ]);
        $receiverWallet->update([
            'balance' => $receiverWallet->balance + $record->amount,
        ]);
        //todo: update balance per date
        DB::commit();
        return $transfer;
    }


}
