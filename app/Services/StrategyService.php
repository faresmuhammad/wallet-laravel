<?php

namespace App\Services;

use App\Models\Strategy;
use App\Models\StrategyRule;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrategyService
{

    public function createStrategy(Request $request): Strategy
    {
        return Strategy::create([
            'name' => $request->name,
            'user_id' => auth()->id()
        ]);
    }

    public function updateStrategyName(string $name, Strategy $strategy): Strategy
    {
        $strategy->update([
            'name' => $name,
        ]);
        return $strategy;
    }

    public function activateStrategy(Strategy $strategy)
    {
        //if there is no rules for the strategy return
        if ($strategy->rules()->count() == 0) return response()->json(['message' => 'Can not activate without rules']);
        DB::transaction(function () use ($strategy) {
            //get the rules
            $rules = $strategy->rules;
            //create wallets on top of rules
            foreach ($rules as $rule) {
                Wallet::create([
                    'strategy_id' => $strategy->id,
                    'name' => $rule->name,
                    'user_id' => auth()->id(),
                    'balance' => $rule->initial_balance,
                    'currency_id' => $rule->currency_id,
                ]);
            }
            //mark the strategy as activated
            $strategy->update([
                'activated' => true
            ]);
        });

    }

    public function migrateToStrategy()
    {
        //
    }

    public function setRule(Strategy $strategy, Request $request)
    {
        return $strategy->rules()->create([
            'name' => $request->name,
            'initial_balance' => $request->initial_balance,
            'ratio' => $request->ratio,
            'include_to_stats' => $request->include_to_stats,
        ]);
    }

    public function updateRule(StrategyRule $rule, Request $request)
    {
        //update the values
        //a request input that decide to update all associated wallets or just the future ones
    }

    public function removeRule(StrategyRule $rule)
    {
        //delete the associated wallets or not
    }
}
