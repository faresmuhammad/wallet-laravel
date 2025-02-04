<?php

namespace Database\Seeders;

use App\Enums\RecordType;
use App\Imports\RecordsImport;
use App\Models\Record;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        ini_set('memory_limit', '2048M');
//        Excel::import(new RecordsImport(), base_path() . '/App Files/expenses.csv');
//        ini_set('memory_limit', '128M');
        $egpWallet = Wallet::find(1);
        $usdWallet = Wallet::find(2);
        do {
            $record = Record::factory()->wallet($egpWallet)->amount(RecordType::Income)->create();
            $egpWallet->update(['balance' => $egpWallet->balance + $record->amount]);
        } while ($egpWallet->refresh()->balance < 50000);

        Record::factory()->afterCreating(function ($record) use ($egpWallet) {
            $egpWallet->update(['balance' => $egpWallet->balance - $record->amount]);
        })->count(150)->wallet($egpWallet)->amount(RecordType::Expense)->create();

        Record::factory()->afterCreating(function ($record) use ($usdWallet) {
            $usdWallet->update(['balance' => $usdWallet->balance + $record->amount]);
        })->amount(RecordType::Income)->wallet($usdWallet)->create();
    }
}
