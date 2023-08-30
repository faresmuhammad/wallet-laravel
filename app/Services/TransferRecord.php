<?php

namespace App\Services;

use App\Http\Requests\TransferRecordRequest;
use App\Models\BalancePerDate;
use App\Models\Record;
use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransferRecord
{


    public function transfer(Wallet $wallet, TransferRecordRequest $request)
    {
//        DB::transaction(function () use ($wallet, $request) {
        DB::beginTransaction();
        //Get Sender & Receiver Wallets
        $senderWallet = Wallet::find($request->sender_wallet);
        $receiverWallet = Wallet::find($request->receiver_wallet);

        //Get Sender & Receiver Balances according to the currency
        $senderBalance = $senderWallet->balances()->where('currency_id', $request->currency)->first();
        $receiverBalance = $receiverWallet->balances()->where('currency_id', $request->currency)->first();

        if (!$receiverBalance)
            return new JsonResponse([
                'message' => 'Both balances don\'t match in currency'
            ], 406);
        if ($request->amount > $senderBalance->value)
            return new JsonResponse([
                'message' => 'This amount can\'t be transferred from sender'
            ], 406);

        //Create a new record
        $record = new Record(
            $request->validated()->safe()->only(['amount', 'currency_id', 'date']) +
            [
                'type' => 'Transfer',
                'balance_id' => $senderBalance->id,
                'wallet_id' => $senderWallet->id,
                'balance_before' => $senderBalance->value
            ]
        );

        //Update balances
        $senderBalance->update([
            'value' => $senderBalance->value - $record->amount
        ]);
        $receiverBalance->update([
            'value' => $receiverBalance->value + $record->amount
        ]);

        //set balance after and save the record
        $record->balance_after = $senderBalance->value;
        $record->save();

        //Update balance per date
        BalancePerDate::updateOrCreate(
            [
                'date' => today(),
                'wallet_id' => $senderWallet->id,
                'balance_id' => $senderBalance->id
            ],
            ['value' => $senderBalance->value]
        );

        //Create a transfer entry
        $transfer = Transfer::create(
            $request->validated() +
            [
                'sender_balance' => $senderBalance->id,
                'receiver_balance' => $receiverBalance->id,
                'record_id' => $record->id
            ]
        );
        DB::commit();
//        });
        return new JsonResponse([
            'status' => 'Successful',
            'transfer_summary' => $transfer
        ]);
    }
}
