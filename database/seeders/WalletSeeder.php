<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wallet::factory()->create([
            'name' => 'Cash',
            'color' => '#009912',
            'initial_balance' => 1000.0,
            'user_id' => 1
        ]);
        Wallet::factory()->create([
            'name' => 'Savings',
            'color' => '#009912',
            'initial_balance' => 500.0,
            'user_id' => 1
        ]);
    }
}
