<?php

namespace App\Services;

use App\Http\Requests\TransferRecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Record;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class EditTransfer
{


    public function editTransferRecord(Record $record, TransferRecordRequest $request)
    {
        /*
         * check amount changed
         * check wallets changed
         * get old sender and receiver balances
         * get new sender and receiver wallets
         * get new balances
         * update new balances
         * update balance per date for both balances
         * update record
         * update transfer
         */
        DB::beginTransaction();
        $amountChanged = $request->amount != $record->amount;
        $walletsChanged = $request->sender_wallet != $record->transfer->sender_wallet || $request->receiver_wallet != $record->transfer->receiver_wallet;
        if (!$amountChanged && !$walletsChanged)
            return new JsonResponse([], 200);


        $oldSenderBalance = Balance::find($record->transfer->sender_balance);
        $oldReceiverBalance = Balance::find($record->transfer->receiver_balance);

        $oldSenderBalance->update(['value' => $this->originalBalance($oldSenderBalance->value, $record->amount, SenderOrReceiver::Sender)]);
        $oldReceiverBalance->update(['value' => $this->originalBalance($oldReceiverBalance->value, $record->amount, SenderOrReceiver::Receiver)]);

        $newSenderWallet = Wallet::find($request->sender_wallet);
        $newReceiverWallet = Wallet::find($request->receiver_wallet);

        $newSenderBalance = $newSenderWallet->balances()->where('currency_id', $request->currency_id)->first();
        $newReceiverBalance = $newReceiverWallet->balances()->where('currency_id', $request->currency_id)->first();


        $newSenderBalance->update(['value' => $newSenderBalance->value - $request->amount]);
        $newReceiverBalance->update(['value' => $newReceiverBalance->value + $request->amount]);


        $this->updateBalancePerDate($record, $newSenderWallet, $newSenderBalance);
        $this->updateBalancePerDate($record, $newReceiverWallet, $newReceiverBalance);


        $this->updateRecord(
            $record,
            $newSenderWallet->id,
            $newSenderBalance->id,
            $request->amount,
            $request->date ?: $record->date
        );


        $record->transfer->update(
            $request->except(['sender_wallet', 'receiver_wallet', 'currency_id']) +
            [
                'sender_wallet' => $newSenderWallet->id,
                'receiver_wallet' => $newReceiverWallet->id,
                'sender_balance' => $newSenderBalance->id,
                'receiver_balance' => $newReceiverBalance->id,
            ]
        );

        DB::commit();

        return new JsonResponse([
            'status' => 'Successful',
            'message' => 'Your transfer record has been updated'
        ]);
    }

    private function originalBalance(float $currentBalance, float $amount, SenderOrReceiver $state): float
    {
        if ($state === SenderOrReceiver::Sender)
            return $currentBalance + $amount;
        else
            return $currentBalance - $amount;
    }

    private function updateBalancePerDate(
        Record  $record,
        Wallet  $wallet,
        Balance $balance
    ): void
    {
        BalancePerDate::updateOrCreate(
            [
                'date' => $record->date,
                'wallet_id' => $wallet->id,
                'balance_id' => $balance->id
            ],
            ['value' => $balance->value]
        );
    }

    private function updateRecord(Record $record, int $wallet, int $balance, float $amount, Carbon $date): void
    {
        $record->update([
            'amount' => $amount,
            'type' => 'Transfer',
            'balance_id' => $balance,
            'wallet_id' => $wallet,
            'currency_id' => $record->currency_id,
            'date' => $date
        ]);
    }
}

enum SenderOrReceiver
{
    case Sender;
    case Receiver;
}
