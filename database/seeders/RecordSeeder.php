<?php

namespace Database\Seeders;

use App\Imports\RecordsImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        ds(ini_get('memory_limit'))->label('memory limit');
        ini_set('memory_limit', '2048M');
        Excel::import(new RecordsImport(), base_path() . '/App Files/expenses.xlsx');
        ini_set('memory_limit', '128M');
    }
}
