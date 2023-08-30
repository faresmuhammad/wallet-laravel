<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Http\Requests\NewRecordRequest;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Record;
use App\Models\Wallet;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class NewRecord
{

    public function pay(Wallet $wallet, NewRecordRequest $request)
    {
        return $this->record($wallet, RecordType::Expense, $request);
    }

    public function topup(Wallet $wallet, NewRecordRequest $request)
    {
        return $this->record($wallet, RecordType::Income, $request);
    }


    private function record(Wallet $wallet, RecordType $type, NewRecordRequest $request)
    {
        /*
         * save the record
         * update balance
         * update balance per date
         * set balance after of the record
         */
        DB::transaction(function () use ($type, $wallet, $request) {
            $balance = Balance::find($request->balance_id);
            $record = new Record(
                $request->validated()->safe()->except(['balance_before']
                    + ['balance_before' => $balance->value])
            );
            switch ($type) {
                case RecordType::Expense:
                    $balance->update([
                        'value' => $balance->value - $record->amount
                    ]);
                    break;

                case RecordType::Income:
                    $balance->update([
                        'value' => $balance->value + $record->amount
                    ]);
                    break;

                default:
                    return new JsonResponse([
                        'message' => 'Record type isn\'t available'
                    ]);
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

            return new JsonResponse([
                'message' => 'I guess it is successful'
            ]);
        });
        return new JsonResponse([
            'status' => 'Successful',
            'message' => 'Your record is successfully inserted'
        ], 201);
    }


}
