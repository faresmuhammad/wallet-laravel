<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\LabelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteRecordTest extends TestCase
{
    use RefreshDatabase;



    public function test_delete_expense_record()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $payResponse = $this->actingAs($user)->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
        ]);
        $this->assertEquals(50, $wallet->fresh()->balance);

        $response = $this->actingAs($user)->delete('/api/delete-record/' . $payResponse->json('data')['id']);
        $this->assertEquals(100, $wallet->fresh()->balance);
    }

    public function test_delete_income_record()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);

        $payResponse = $this->actingAs($user)->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'date' => now(),
        ]);
        $this->assertEquals(150, $wallet->fresh()->balance);

        $response = $this->actingAs($user)->delete('/api/delete-record/' . $payResponse->json('data')['id']);
        $this->assertEquals(100, $wallet->fresh()->balance);
    }

    public function test_delete_transfer_record()
    {
        $user = User::factory()->create();
        $wallet1 = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $wallet2 = Wallet::factory()->create(['balance' => 100,'user_id' => $user->id]);
        $transferResponse = $this->actingAs($user)->post('/api/transfer/', [
            'amount' => 50,
            'name' => 'Transfer',
            'date' => now(),
            'sender_wallet' => $wallet1->id,
            'receiver_wallet' => $wallet2->id,
        ]);
        $this->assertEquals(50, $wallet1->fresh()->balance);
        $this->assertEquals(150, $wallet2->fresh()->balance);

        $response = $this->actingAs($user)->delete('/api/delete-record/' . $transferResponse->json('data')['id']);
        $this->assertEquals(100, $wallet1->fresh()->balance);
        $this->assertEquals(100, $wallet2->fresh()->balance);
    }
}
