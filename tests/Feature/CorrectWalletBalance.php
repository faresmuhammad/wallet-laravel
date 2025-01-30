<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorrectWalletBalance extends TestCase
{
    use RefreshDatabase;

    public function test_correct_wallet_balance_with_actual_balance_less_than_the_current_updates_the_balance_and_create_expense_record(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100]);
        $response = $this->actingAs($user)->post('/api/wallets/correct/' . $wallet->id, [
            'actual_balance' => 90
        ]);

        $this->assertEquals(90, $wallet->fresh()->balance);
        $this->assertDatabaseHas('records', [
            'name' => 'Error Amount',
            'amount' => 10,
            'wallet_id' => $wallet->id,
            'currency' => 'EGP',
            'type' => 'Expense',
        ]);
    }

    public function test_correct_wallet_balance_with_actual_balance_more_than_the_current_updates_the_balance_and_create_income_record(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100]);
        $response = $this->actingAs($user)->post('/api/wallets/correct/' . $wallet->id, [
            'actual_balance' => 110
        ]);

        $this->assertEquals(110, $wallet->fresh()->balance);
        $this->assertDatabaseHas('records', [
            'name' => 'Error Amount',
            'amount' => 10,
            'wallet_id' => $wallet->id,
            'currency' => 'EGP',
            'type' => 'Income',
        ]);
    }

    public function test_correct_wallet_balance_with_positive_error_amount(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100]);
        $response = $this->actingAs($user)->post('/api/wallets/correct/' . $wallet->id, [
            'error_amount' => 10
        ]);
        $this->assertEquals(90, $wallet->fresh()->balance);
        $this->assertDatabaseHas('records', [
            'name' => 'Error Amount',
            'amount' => 10,
            'wallet_id' => $wallet->id,
            'currency' => 'EGP',
            'type' => 'Expense',
        ]);
    }

    public function test_correct_wallet_balance_with_negative_error_amount(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100]);
        $response = $this->actingAs($user)->post('/api/wallets/correct/' . $wallet->id, [
            'error_amount' => -10
        ]);
        $this->assertEquals(110, $wallet->fresh()->balance);
        $this->assertDatabaseHas('records', [
            'name' => 'Error Amount',
            'amount' => 10,
            'wallet_id' => $wallet->id,
            'currency' => 'EGP',
            'type' => 'Income',
        ]);
    }
}
