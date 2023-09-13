<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\RecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Budget;
use App\Models\Record;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewRecord
{

    public function pay(Wallet $wallet, RecordRequest $request): JsonResponse
    {
        return $this->record($wallet, RecordType::Expense, $request);
    }

    public function topup(Wallet $wallet, RecordRequest $request): JsonResponse
    {
        return $this->record($wallet, RecordType::Income, $request);
    }


    private function record(Wallet $wallet, RecordType $type, RecordRequest $request): JsonResponse
    {
        /*
         * save the record
         * update balance
         * update balance per date
         * set balance after of the record
         *
         * ** Budget Updates ** works only on pay method
         * check the selected category and the selected wallet if they are linked with a budget
         * perform budget calculations
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
        }
    }
}
