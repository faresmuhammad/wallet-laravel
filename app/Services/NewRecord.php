<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Resources\RecordResource;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewRecord
{

    public function pay(Wallet $wallet, Request $request): JsonResponse
    {
        //get the proper rule to apply the record on
        //update the rule's wallet balance
        $record = Record::create([
            'amount' => $request->amount,
            'name' => $request->name,
//            'type' => RecordType::Expense
        ]);
        $wallet->update([
            'balance' => $wallet->balance - $record->amount,
        ]);
        return apiResponse('Record created!', $record, status: 201);
    }

    public function topup(?int $walletId, Request $request): JsonResponse
    {
        //todo: update balance per date
        DB::beginTransaction();
        if ($walletId) {
            $wallet = Wallet::find($walletId);
            $record = Record::create([
                'amount' => $request->amount,
                'name' => $request->name ?? 'No Name',
                'category_id' => $request->category_id,
                'strategy_id' => $wallet->strategy->id,
                'date' => now(),
                'type' => RecordType::Income
            ]);
            $wallet->update([
                'balance' => $wallet->balance + $record->amount,
            ]);
            DB::commit();
            return apiResponse('Record created!', new RecordResource($record), status: 201);
        }
        $activeStrategy = Strategy::isActive()->first();

        if (is_null($activeStrategy)) return apiResponse('Strategy not activated!', status: 404);

        $record = Record::create([
            'amount' => $request->amount,
            'name' => $request->name ?? 'No Name',
            'category_id' => $request->category_id,
            'strategy_id' => $activeStrategy->id,
            'date' => now(),
            'type' => RecordType::Income
        ]);
        $this->calculateUponRules($request->amount, $activeStrategy->rules);
        DB::commit();
        return apiResponse('Record created!', new RecordResource($record), status: 201);

    }

    /**
     * @param float $amount
     * @param $rules
     * @return void
     */
    private function calculateUponRules(float $amount, $rules): void
    {
        foreach ($rules as $rule) {
            $calculatedAmount = $amount * $rule->ratio;
            $rule->wallet()->update([
                'balance' => $rule->wallet->balance + $calculatedAmount,
            ]);
        }

    }


}
