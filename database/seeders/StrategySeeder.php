<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Strategy;
use App\Models\StrategyRule;
use App\Models\User;
use App\Services\StrategyService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StrategySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $strategy = Strategy::create([
            'name' => "30/30/30/10 Strategy",
            'user_id' => User::first()->id
        ]);

        $rule1 = StrategyRule::create([
            'name' => 'Liability',
            'initial_balance' => 2000.0,
            'ratio' => 0.3,
            'currency_id' => Currency::first()->id,
            'strategy_id' => $strategy->id,
        ]);
        $rule2 = StrategyRule::create([
            'name' => 'Saving',
            'initial_balance' => 2000.0,
            'ratio' => 0.3,
            'currency_id' => Currency::first()->id,
            'strategy_id' => $strategy->id,
        ]);
        $rule3 = StrategyRule::create([
            'name' => 'Spending',
            'initial_balance' => 2000.0,
            'ratio' => 0.3,
            'currency_id' => Currency::first()->id,
            'strategy_id' => $strategy->id,
        ]);
        $service = new StrategyService();
        $service->activateStrategy($strategy);
    }
}
