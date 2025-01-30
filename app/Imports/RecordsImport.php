<?php

namespace App\Imports;

use App\Enums\RecordType;
use App\Http\Requests\PayRequest;
use App\Models\Label;
use App\Models\Record;
use App\Models\Wallet;
use App\Services\NewRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;

class RecordsImport implements ToModel
{

    public function model(array $row)
    {
        $newRecord = new NewRecord();
        $wallet = Wallet::find(1);
        if ($row[2] == 'Expense') {
            $request = PayRequest::create('/api/pay', 'POST', [
                'amount' => $row[1],
                'name' => $row[0],
                'category_id' => $row[7],
                'date' => $row[4] == '' ? now() : $row[4],
            ]);
            $record = $newRecord->pay($wallet, $request);
            $this->attachLabelsToRecord($row, $record);
        } elseif ($row[2] == 'Income') {
            if ($row[5] === 'USD') {
                $request = Request::create('/api/topup/2', 'POST', [
                    'amount' => $row[1],
                    'name' => $row[0],
                    'category_id' => $row[7],
                    'date' => $row[4] == '' ? now() : $row[4],
                    'currency' => 'USD',
                ]);
                $record = $newRecord->topup(Wallet::find(2), $request);
                $this->attachLabelsToRecord($row, $record);

            } else {
                $request = Request::create('/api/topup/1', 'POST', [
                    'amount' => $row[1],
                    'name' => $row[0],
                    'category_id' => $row[7],
                    'date' => $row[4] == '' ? now() : $row[4],
                ]);
                $record = $newRecord->topup($wallet, $request);
                $this->attachLabelsToRecord($row, $record);

            }
        }

    }

    /**
     * @param array $row
     * @param Record $record
     * @return void
     */
    public function attachLabelsToRecord(array $row, Record $record): void
    {
        if ($row[3] != '') {
            $label = Label::firstOrCreate(['name' => $row[3]]);
            $record->labels()->attach($label);
        }
        if ($row[6] != '' && $row[3] != $row[6]) {
            $label = Label::firstOrCreate(['name' => $row[6]]);
            $record->labels()->attach($label);
        }
    }
}
