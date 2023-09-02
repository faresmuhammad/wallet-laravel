<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name'=>'Fares Muhammad',
            'password' => Hash::make('123456'),
            'email' => 'fares.muhammad210@gmail.com'
        ]);

        $this->call([
            CurrencySeeder::class,
            WalletSeeder::class,
            CategorySeeder::class
        ]);
    }
}
