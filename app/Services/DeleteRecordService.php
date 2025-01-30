<?php

namespace App\Services;

use App\Models\Record;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeleteRecordService
{

    public function deleteExpenseRecord(Record $record): JsonResponse
    {
        DB::beginTransaction();
        $wallet = $record->wallet;
        $wallet->update([
            'balance' => $wallet->balance + $record->amount
        ]);
        $record->labels()->detach();
        $record->delete();
        DB::commit();
        return apiResponse("Record deleted successfully");
    }

    public function deleteIncomeRecord(Record $record): JsonResponse
    {
        //if regular income, remove the amount from the wallet then delete the record
        //if strategy income, remove each wallet amount
        DB::beginTransaction();
        $wallet = $record->wallet;
        $wallet->update([
            'balance' => $wallet->balance - $record->amount
        ]);
        $record->labels()->detach();
        $record->delete();
        DB::commit();
        return apiResponse("Record deleted successfully");
    }

    public function deleteTransferRecord(Record $record): JsonResponse
    {
        //return each wallet to its original amount before the transfer
        DB::beginTransaction();
        $transfer = $record->transfer;
        $transfer->senderWallet->update([
            'balance' => $transfer->senderWallet->balance + $record->amount
        ]);
        $transfer->receiverWallet->update([
            'balance' => $transfer->receiverWallet->balance - $record->amount
        ]);

        $record->labels()->detach();
        $record->delete();
        DB::commit();
        return apiResponse("Record deleted successfully");
    }
}
