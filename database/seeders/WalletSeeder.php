<?php

namespace Database\Seeders;

use App\Models\User;
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
        Wallet::create([
            'name' => 'EGP',
            'balance' => 19464.21,
            'user_id' => User::first()->id,
            'currency' => 'EGP'
        ]);
        Wallet::create([
            'name' => 'USD',
            'balance' => 100.0,
            'user_id' => User::first()->id,
            'currency' => 'USD'
        ]);
    }
}
