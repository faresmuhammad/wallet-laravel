<?php

namespace App\Imports;

use App\Enums\RecordType;
use App\Http\Requests\PayRequest;
use App\Models\Record;
use App\Models\Wallet;
use App\Services\NewRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class RecordsImport implements ToModel
{

    public function model(array $row)
    {
        $newRecord = new NewRecord();
        $wallet = Wallet::find(1);
        if ($row[2] == 'Expense') {
            $request = PayRequest::create('/api/pay','POST', [
                'amount' => $row[1],
                'name' => $row[0],
                'category_id' => $row[7],
                'date' => $row[4] == '' ? now() : $row[4],
            ]);
            $newRecord->pay($wallet, $request);
        } elseif ($row[2] == 'Income') {
            if ($row[5] === 'USD'){
                $request = Request::create('/api/topup/2', 'POST', [
                    'amount' => $row[1],
                    'name' => $row[0],
                    'category_id' => $row[7],
                    'date' => $row[4] == '' ? now() : $row[4],
                    'currency' => 'USD',
                ]);
                $newRecord->topup(Wallet::find(2), $request);
            }
            else{
                $request = Request::create('/api/topup/1', 'POST', [
                    'amount' => $row[1],
                    'name' => $row[0],
                    'category_id' => $row[7],
                    'date' => $row[4] == '' ? now() : $row[4],
                ]);
                $newRecord->topup($wallet, $request);
            }
        }

    }
}
