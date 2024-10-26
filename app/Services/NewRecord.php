<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\RecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Budget;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\StrategyRule;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewRecord
{

    public function pay(Wallet $wallet, RecordRequest $request): JsonResponse
    {
        $record = $wallet->records()->create($request->safe()->except('type') + ['type' => RecordType::Expense]);
        $wallet->update([
            'balance' => $wallet->balance - $record->amount,
        ]);
        return apiResponse(true, 'Record created!', $record);
    }

    public function topup(?Wallet $wallet, RecordRequest $request): JsonResponse
    {
        DB::beginTransaction();
        //add the amount directly to wallet balance
        if ($wallet) {
        }
        $activeStrategy = Strategy::isActive()->first();
        Record::create($request->validated());
        $this->calculateUponRules($request->amount, $activeStrategy->rules);
        DB::commit();
        return apiResponse(true, 'Record created!');
    }

    /**
     * @param float $amount
     * @param StrategyRule[] $rules
     * @return void
     */
    private function calculateUponRules(float $amount, $rules)
    {
        foreach ($rules as $rule) {
            $calculatedAmount = $amount * $rule->ratio;
            $rule->wallet()->update([
                'balance' => $rule->wallet->balance + $calculatedAmount,
            ]);
        }

    }

    private function record(Wallet $wallet, RecordType $type, RecordRequest $request): JsonResponse
    {
        /*
         *
         */
        DB::transaction(function () use ($type, $wallet, $request) {
            $balance = Balance::find($request->balance_id);
            $record = new Record(
                $request->validated()
                + ['balance_before' => $balance->value]
            );
            switch ($type) {
                case RecordType::Expense:
                    $record->type = RecordType::Expense->name;
                    $balance->update([
                        'value' => $balance->value - $record->amount
                    ]);
                    $this->triggerBudget($record);
                    break;

                case RecordType::Income:
                    $record->type = RecordType::Income->name;
                    $balance->update([
                        'value' => $balance->value + $record->amount
                    ]);
                    break;

                default:
                    throw new Exception('Record type is not available');
            }

            $record->balance_after = $balance->value;
            $record->save();

            BalancePerDate::updateOrCreate(
            //Fields to search for
                ['date' => today()],
                //Fields to update
                [
                    'value' => $balance->value,
                    'wallet_id' => $wallet->id,
                    'balance_id' => $balance->id
                ]
            );
        });
        return new JsonResponse([
            'status' => 'Successful',
            'message' => 'Your record is successfully inserted'
        ], 201);
    }


    /**
     * @throws Exception
     */
    private function triggerBudget(Record $record): void
    {
        if ($record->type != RecordType::Expense)
            throw new Exception('Budget only can be triggered by expense records');

        $budget = Budget::linkedWith($record->wallet, $record->category)->first();
        if ($budget) {
            $budget->update([
                'current_amount' => $budget->current_amount + $record->amount
            ]);
            $record->budget()->associate($budget);
            $record->save();
        }
    }
}
