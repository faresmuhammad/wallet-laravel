<?php

namespace Tests\Feature;

use App\Http\Requests\PayRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NewRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NewRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_call_subtract_the_amount_from_wallet_balance()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $response = $this->actingAs($user)->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
        ]);

        $this->assertEquals(50, $wallet->fresh()->balance);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_topup_call_add_the_amount_to_wallet_balance()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $response = $this->actingAs($user)->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'date' => now(),
        ]);
        $this->assertEquals(150, $wallet->fresh()->balance);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_a_record_with_different_currency_from_wallet()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $payResponse = $this->actingAs($user)->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
            'currency' => 'USD',
        ]);
        $payResponse->assertStatus(403);
        $topupResponse = $this->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'date' => now(),
            'currency' => 'EUR',
        ]);
        $topupResponse->assertStatus(403);
    }

    public function test_transfer_amount_from_wallet_to_another_with_the_same_currency()
    {
        $user = User::factory()->create();

        $wallet1 = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $wallet2 = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $response = $this->actingAs($user)->post('/api/transfer', [
            'amount' => 50,
            'sender_wallet' => $wallet1->id,
            'receiver_wallet' => $wallet2->id,
            'date' => now(),
        ]);
        $this->assertEquals(50, $wallet1->fresh()->balance);
        $this->assertEquals(150, $wallet2->fresh()->balance);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_transfer_amount_from_wallet_to_another_with_different_currency_throws_exception()
    {
        $user = User::factory()->create();

        $wallet1 = Wallet::factory()->create(['balance' => 100, 'currency' => 'USD','user_id' => $user->id]);
        $wallet2 = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $response = $this->actingAs($user)->post('/api/transfer', [
            'amount' => 50,
            'sender_wallet' => $wallet1->id,
            'receiver_wallet' => $wallet2->id,
            'date' => now(),
        ]);
        $response->assertStatus(403);
    }
}
