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

class NewRecord
{

    public function pay(Wallet $wallet, PayRequest $request): JsonResponse
    {
        //get the proper rule to apply the record on
        //update the rule's wallet balance
        DB::beginTransaction();
        $record = $wallet->records()->create(
            $request->validated() +
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

    public function topup(?int $walletId, Request $request): JsonResponse
    {
        //todo: update balance per date
        DB::beginTransaction();
        if ($walletId) {
            $wallet = Wallet::find($walletId);
            $record = $wallet->records()->create([
                'amount' => $request->amount,
                'name' => $request->name ?? 'No Name',
                'category_id' => $request->category_id,
                'strategy_id' => $wallet->strategy->id,
                'date' => $request->date ?? now(),
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
            'amount' => $request->amount,
            'name' => $request->name ?? 'No Name',
            'category_id' => $request->category_id,
            'strategy_id' => $activeStrategy->id,
            'date' => $request->date ?? now(),
            'type' => RecordType::Income
        ]);
        $this->updateBalancesUponRules($request->amount, $activeStrategy->rules);
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

        $activeStrategy = Strategy::isActive()->first();
        throw_if(is_null($activeStrategy), new HttpResponseException(
            apiResponse("Strategy Error", [], ["Strategy not activated or there is no strategy found!"], status: 404)
        ));
        $record = Record::create([
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'date' => $validated['date'] ?? now(),
            'strategy_id' => $activeStrategy->id,
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
