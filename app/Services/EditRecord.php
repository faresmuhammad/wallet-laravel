<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\RecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Budget;
use App\Models\Record;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EditRecord
{

    public function editRecord(Record $record, RecordRequest $request): JsonResponse
    {
        /*
         * update the balance
         * update the record
         * update balance per date
         * ** Budget Updates **
         * check the updated record category and wallet
         * update the value if the updated record is still an expense record
         * perform the budget calculations if it is changed to expense
         */
        DB::beginTransaction();
        $this->updateBalance(
            $record,
            $request->amount,
            $record->type,
            RecordType::from($request->type)
        );


        $this->updateBudget(
            $record,
            $request->amount,
            $record->type,
            RecordType::from($request->type)
        );
        $this->updateRecord($record, $request);
        $this->updateBalancePerDate($record);
        DB::commit();

        return new JsonResponse([
            'status' => 'Successful',
            'message' => 'Your Record has been updated successfully'
        ]);
    }


    private function updateRecord(
        Record        $record,
        RecordRequest $request,
    ): void
    {
        $record->update(
            $request->except(['balance_id', 'wallet_id', 'currency_id']) +
            [
                'balance_id' => $record->balance->id,
                'wallet_id' => $record->wallet->id,
                'currency_id' => $record->currency->id,
                'balance_after' => $record->balance->value
            ]
        );
    }

    private function updateBalance(
        Record     $record,
        float      $newAmount,
        RecordType $from,
        RecordType $to,
    ): void
    {
        $record->balance->update([
            'value' => $this->updatedValue(
                currentBalance: $record->balance->value,
                currentRecord: $record->amount,
                newRecord: $newAmount,
                from: $from, to: $to
            )
        ]);
    }


    private function performTypeChange(
        RecordType $from, RecordType $to,
        ?\Closure  $expenseToExpense,
        ?\Closure  $expenseToIncome,
        ?\Closure  $incomeToExpense,
        ?\Closure  $incomeToIncome,
    )
    {
        if ($from === RecordType::Expense) {
            if ($to === RecordType::Expense)
                return $expenseToExpense();
            else
                return $expenseToIncome();
        } else {
            if ($to === RecordType::Expense)
                return $incomeToExpense();
            else
                return $incomeToIncome();
        }
    }

    private function updatedValue(
        float $currentBalance, float $currentRecord,
        float $newRecord, RecordType $from, RecordType $to
    ): float
    {
        return $this->performTypeChange(
            $from, $to,
            fn() => $currentBalance + $currentRecord - $newRecord,
            fn() => $currentBalance + $currentRecord + $newRecord,
            fn() => $currentBalance - $currentRecord - $newRecord,
            fn() => $currentBalance - $currentRecord + $newRecord,

        );
    }

    private function updateBalancePerDate(
        Record $record
    ): void
    {
        BalancePerDate::updateOrCreate(
            [
                'date' => $record->date,
                'wallet_id' => $record->wallet_id,
                'balance_id' => $record->balance_id
            ],
            ['value' => $record->balance->value]
        );
    }


    private function updateBudget(Record $record, float $newAmount, RecordType $from, RecordType $to): void
    {
        //todo: support change in category
        if ($record->budget || (!$record->budget && $to == RecordType::Expense)) {
            $this->performTypeChange(
                $from, $to,
                function () use ($record, $newAmount) {
                    $record->budget->update([
                        'current_amount' => $record->budget->current_amount - $record->amount + $newAmount
                    ]);
                },
                function () use ($record, $newAmount) {
                    $record->budget()->update([
                        'current_amount' => $record->budget->current_amount - $record->amount
                    ]);
                    $record->budget()->disassociate();
                },
                function () use ($record, $newAmount) {
                    //todo: get budget id to associate
                    $budget = Budget::linkedWith($record->wallet, $record->category)->first();
                    $budget->update([
                        'current_amount' => $budget->current_amount + $newAmount
                    ]);
                    $record->budget()->associate($budget);
                },
                null
            );
        }
    }
}

