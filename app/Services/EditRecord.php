<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\UpdateRecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordUpdatedResource;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Budget;
use App\Models\Record;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EditRecord
{

    public function editRecord(Record $record, UpdateRecordRequest $request): JsonResponse
    {
        /*
         * update the related wallet balance
         * update the record
         * todo: update balance per date
         * ** Budget Updates ** -> todo
         * check the updated record category and wallet
         * update the value if the updated record is still an expense record
         * perform the budget calculations if it is changed to expense
         */
        DB::beginTransaction();
        $this->updateBalance(
            $record,
            $request->amount,
            from: $record->type,
            to: $request->type ? RecordType::from($request->type) : $record->type
        );

        $record->update($request->validated());

        //todo: update balance per date
        DB::commit();

        return apiResponse('Record Updated Successfully', new RecordUpdatedResource($record));
    }


    private function updateBalance(
        Record     $record,
        float      $newAmount,
        RecordType $from,
        RecordType $to,
    ): void
    {
        $wallet = $record->relatedWallet;
        $wallet->update([
            'balance' => $this->updatedValue(
                currentBalance: $wallet->balance,
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

