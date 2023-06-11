<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'name' => 'US Dollar',
            'pfx_symbol' => '$',
            'unit_name' => 'Dollar',
            'cent_name' => 'Cent',
            'symbol_name' => 'USD'
        ]);
        Currency::create([
            'name' => 'Egyptian Pound',
            'sfx_symbol' => 'L.E',
            'unit_name' => 'Pound',
            'cent_name' => 'Qirsh',
            'symbol_name' => 'EGP'
        ]);
    }
}
