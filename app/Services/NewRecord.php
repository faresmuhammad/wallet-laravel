<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Http\Resources\TransferResource;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\Wallet;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * This service class is responsible for creating a new record and handling related parts
 * main methods are pay, topup and transfer
 * helper method is updateBalancesUponRules to update wallet balance upon strategy rules
 */
class NewRecord
{

    public function pay(Wallet $wallet, array $data): JsonResponse
    {
        //get the proper rule to apply the record on
        //update the rule's wallet balance
        DB::beginTransaction();
        $record = $wallet->records()->create(
            $data +
            [
                'type' => RecordType::Expense,
                'strategy_id' => $wallet->strategy_id,
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
    public function topup(?int $walletId, array $data): JsonResponse
    {
        //todo: update balance per date
        DB::beginTransaction();
        if ($walletId) {
            $wallet = Wallet::find($walletId);
            $record = $wallet->records()->create([
                'amount' => $data['amount'],
                'name' => $data['name'] ?? 'No Name',
                'category_id' => $data['category_id'],
                'strategy_id' => $wallet->strategy->id,
                'date' => $data['date'] ?? now(),
                'type' => RecordType::Income
            ]);
            $wallet->update([
                'balance' => $wallet->balance + $record->amount,
            ]);
            DB::commit();
            return apiResponse('Record created!', new RecordResource($record), status: 201);
        }
        $activeStrategy = Strategy::isActive()->first();

        throw_if(is_null($activeStrategy), new HttpResponseException(
            apiResponse("Strategy Error", [], ["Strategy not activated or there is no strategy found!"], status: 404)
        ));
        $record = Record::create([
            'amount' => $data['amount'],
            'name' => $data['name'] ?? 'No Name',
            'category_id' => $data['category_id'],
            'strategy_id' => $activeStrategy->id,
            'date' => $data['date'] ?? now(),
            'type' => RecordType::Income
        ]);
        $this->updateBalancesUponRules($data['amount'], $activeStrategy->rules);
        DB::commit();
        return apiResponse('Record created!', new RecordResource($record), status: 201);

    }

    /**
     * @throws \Throwable
     */
    public function transfer(array $data): JsonResponse
    {
        DB::beginTransaction();

        $senderWallet = Wallet::find($data['sender_wallet']);
        $receiverWallet = Wallet::find($data['receiver_wallet']);

        throw_if($senderWallet->currency_id != $receiverWallet->currency_id, new HttpResponseException(
            apiResponse("Error while transfer process.", [], ["Can't transfer to a different currency!"], status: 403)
        ));

        $activeStrategy = Strategy::isActive()->first();
        throw_if(is_null($activeStrategy), new HttpResponseException(
            apiResponse("Strategy Error", [], ["Strategy not activated or there is no strategy found!"], status: 404)
        ));
        $record = Record::create([
            'name' => $data['name'] ?? 'No Name',
            'amount' => $data['amount'],
            'date' => $data['date'] ?? now(),
            'strategy_id' => $activeStrategy->id,
            'type' => RecordType::Transfer
        ]);
        $transfer = $record->transfer()->create([
            'amount' => $data['amount'],
            'sender_wallet' => $data['sender_wallet'],
            'receiver_wallet' => $data['receiver_wallet'],
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

    /**
     * @param float $amount
     * @param $rules
     * @return void
     */
    private function updateBalancesUponRules(float $amount, $rules): void
    {
        foreach ($rules as $rule) {
            $calculatedAmount = $amount * $rule->ratio;
            $rule->wallet()->update([
                'balance' => $rule->wallet->balance + $calculatedAmount,
            ]);
        }

    }


}
