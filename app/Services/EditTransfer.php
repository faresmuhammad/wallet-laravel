<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\TransferResource;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Record;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class EditTransfer
{


    /**
     * @throws \Throwable
     */
    public function editTransferRecord(Record $record, TransferRecordRequest $request): Record
    {
        throw_if($record->type != RecordType::Transfer, new HttpResponseException(
            apiResponse("Record Type Error.", [], ["This record is not a transfer."], status: 403)
        ));
        $amountChanged = $request->amount != $record->amount;
        $walletsChanged = $request->sender_wallet != $record->transfer->sender_wallet || $request->receiver_wallet != $record->transfer->receiver_wallet;
        throw_if(!$amountChanged && !$walletsChanged, new HttpResponseException(
            apiResponse("You didn't modify amount or wallets.", status: 204)
        ));

        DB::beginTransaction();

        $oldSenderWallet = Wallet::find($record->transfer->sender_wallet);
        $oldReceiverWallet = Wallet::find($record->transfer->receiver_wallet);


        //Return the wallets' balances to its original balance before the transfer
        $oldSenderWallet->update(['balance' => $this->originalBalance($oldSenderWallet->balance, $record->amount, SenderOrReceiver::Sender)]);
        $oldReceiverWallet->update(['balance' => $this->originalBalance($oldReceiverWallet->balance, $record->amount, SenderOrReceiver::Receiver)]);

        $newSenderWallet = Wallet::find($request->sender_wallet);
        $newReceiverWallet = Wallet::find($request->receiver_wallet);

        throw_if($newSenderWallet->currency != $newReceiverWallet->currency, new HttpResponseException(
            apiResponse("Error while transfer process.", [], ["Can't transfer to a different currency!"], status: 403)
        ));

        $newSenderWallet->update(['balance' => $newSenderWallet->balance - $request->amount]);
        $newReceiverWallet->update(['balance' => $newReceiverWallet->balance + $request->amount]);

        $record->update([
            'name' => $request->name ?? $record->name,
            'amount' => $request->amount ?? $record->amount,
            'date' => $request->date ?? $record->date,

        ]);
        $record->labels()->sync($request->labels);
        //todo: update balance per date


        $record->transfer->update(
            [
                'name' => $request->name ?? $record->name,
                'amount' => $request->amount ?? $record->amount,
                'date' => $request->date ?? $record->date,
                'sender_wallet' => $newSenderWallet->id,
                'receiver_wallet' => $newReceiverWallet->id,
            ]
        );

        DB::commit();

        return $record;
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
}

enum SenderOrReceiver
{
    case Sender;
    case Receiver;
}
