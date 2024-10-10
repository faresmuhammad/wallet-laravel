<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Strategy;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StrategyAndRuleTest extends TestCase
{
    use RefreshDatabase;


    public function test_a_strategy_can_be_created(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/strategies', [
            'name' => 'test',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);
    }

    public function test_a_strategy_name_can_be_updated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $strategy = Strategy::create([
            'name' => 'test',
            'user_id' => $user->id,
        ]);

        $updatedName = 'test updated';
        $this->put("/api/strategies/{$strategy->id}", [
            'name' => $updatedName,
        ]);

        $this->assertDatabaseHas('strategies', [
            'name' => $updatedName,
        ]);
    }

    public function test_that_activating_strategy_will_create_wallets_depending_on_rules(): void
    {
        $user = User::factory()->create();
        $currency = Currency::create([
            'name' => 'US Dollar',
            'pfx_symbol' => '$',
            'unit_name' => 'Dollar',
            'cent_name' => 'Cent',
            'symbol_name' => 'USD'
        ]);
        $this->actingAs($user);
        $strategy = Strategy::create([
            'name' => 'test',
            'user_id' => $user->id,
        ]);

        $ruleLiabilities = $strategy->rules()->create([
            'name' => 'Liabilities',
            'initial_balance' => 1000,
            'ratio' => 0.3,
            'include_to_stats' => true,
            'currency_id' => $currency->id,
        ]);
        $ruleSpending = $strategy->rules()->create([
            'name' => 'Spending',
            'initial_balance' => 1000,
            'ratio' => 0.3,
            'include_to_stats' => true,
            'currency_id' => $currency->id,
        ]);
        $ruleSaving = $strategy->rules()->create([
            'name' => 'Saving',
            'initial_balance' => 1000,
            'ratio' => 0.3,
            'include_to_stats' => true,
            'currency_id' => $currency->id,
        ]);

        $this->post("/api/strategies/{$strategy->id}/activate", []);

        $this->assertDatabaseHas('wallets', ['name' => 'Liabilities']);
        $this->assertDatabaseHas('wallets', ['name' => 'Spending']);
        $this->assertDatabaseHas('wallets', ['name' => 'Saving']);
        $this->assertDatabaseHas('strategies', ['name' => 'test', 'activated' => true]);
    }

    public function test_that_activating_strategy_without_rules_will_return_error(): void
    {
        $user = User::factory()->create();
        $currency = Currency::create([
            'name' => 'US Dollar',
            'pfx_symbol' => '$',
            'unit_name' => 'Dollar',
            'cent_name' => 'Cent',
            'symbol_name' => 'USD'
        ]);
        $this->actingAs($user);
        $strategy = Strategy::create([
            'name' => 'test',
            'user_id' => $user->id,
        ]);


        $response = $this->post("/api/strategies/{$strategy->id}/activate", []);

        $this->assertDatabaseHas('strategies', ['name' => 'test', 'activated' => false]);
//        $response->assertJson(['message' => 'Can not activate without rules']);
        $response->assertJsonFragment(['message' => 'Can not activate without rules']);
    }
}
