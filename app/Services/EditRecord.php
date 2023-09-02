<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\RecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
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
         */
        DB::beginTransaction();
        $this->updateBalance(
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


    private function updatedValue(
        float $currentBalance, float $currentRecord,
        float $newRecord, RecordType $from, RecordType $to
    ): float
    {
        if ($from === RecordType::Expense) {
            if ($to === RecordType::Expense)
                return $currentBalance + $currentRecord - $newRecord;
            else
                return $currentBalance + $currentRecord + $newRecord;
        } else {
            if ($to === RecordType::Expense)
                return $currentBalance - $currentRecord - $newRecord;
            else
                return $currentBalance - $currentRecord + $newRecord;
        }
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


}

