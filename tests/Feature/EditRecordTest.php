<?php

namespace Tests\Feature;

use App\Enums\RecordType;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRecordTest extends TestCase
{

    use RefreshDatabase;

    public function test_update_record_from_expense_to_expense()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $payResponse = $this->actingAs($user)->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
        ]);
        $this->assertEquals(50, $wallet->fresh()->balance);
        $this->assertEquals(201, $payResponse->getStatusCode());

        $response = $this->actingAs($user)->put('/api/update-record/' . $payResponse->json('data')['id'], [
            'amount' => 60,
            'name' => 'Test Pay Updated',
            'date' => now(),
            'type' => RecordType::Expense->value,
        ]);
        $this->assertEquals(40, $wallet->fresh()->balance);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_record_from_expense_to_income()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $payResponse = $this->actingAs($user)->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
        ]);
        $this->assertEquals(50, $wallet->fresh()->balance);
        $this->assertEquals(201, $payResponse->getStatusCode());

        $response = $this->actingAs($user)->put('/api/update-record/' . $payResponse->json('data')['id'], [
            'amount' => 60,
            'name' => 'Test Pay Updated',
            'date' => now(),
            'type' => RecordType::Income->value,
        ]);
        $this->assertEquals(160, $wallet->fresh()->balance);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_record_from_income_to_income()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $topupResponse = $this->actingAs($user)->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'date' => now(),
        ]);
        $this->assertEquals(150, $wallet->fresh()->balance);
        $this->assertEquals(201, $topupResponse->getStatusCode());

        $response = $this->actingAs($user)->put('/api/update-record/' . $topupResponse->json('data')['id'], [
            'amount' => 60,
            'name' => 'Test Topup Updated',
            'date' => now(),
            'type' => RecordType::Income->value,
        ]);
        $this->assertEquals(160, $wallet->fresh()->balance);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_record_from_income_to_expense()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $topupResponse = $this->actingAs($user)->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'date' => now(),
        ]);
        $this->assertEquals(150, $wallet->fresh()->balance);
        $this->assertEquals(201, $topupResponse->getStatusCode());

        $response = $this->actingAs($user)->put('/api/update-record/' . $topupResponse->json('data')['id'], [
            'amount' => 60,
            'name' => 'Test Topup Updated',
            'date' => now(),
            'type' => RecordType::Expense->value,
        ]);
        $this->assertEquals(40, $wallet->fresh()->balance);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_call_edit_transfer_on_non_transfer_record_throws_an_exception()
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $wallet2 = Wallet::factory()->create(['balance' => 100]);
        $record = $wallet->records()->create([
            'amount' => 50,
            'name' => 'Test Pay',
            'date' => now(),
            'type' => RecordType::Expense->value,
        ]);

        $updateTransferResponse = $this->actingAs($user)->put('/api/update-transfer/' . $record->id, [
            'amount' => 60,
            'name' => 'Test Pay Updated',
            'date' => now(),
            'sender_wallet' => $wallet->id,
            'receiver_wallet' => $wallet2->id,
        ]);
        $updateTransferResponse->assertStatus(403);
        $updateTransferResponse->assertJson(['errors' => ['This record is not a transfer.']]);
    }

    public function test_update_transfer_without_any_modifications_throws_an_exception()
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
        $transfer = Transfer::find($response->json('data')['id']);
        $updateTransferResponse = $this->actingAs($user)->put('/api/update-transfer/' . $transfer->record->id, [
            'amount' => 50,
            'name' => 'Test Transfer Updated',
            'date' => now(),
            'sender_wallet' => $wallet1->id,
            'receiver_wallet' => $wallet2->id,
        ]);
        $updateTransferResponse->assertStatus(204);
    }

    public function test_update_transfer_between_wallets_works_properly()
    {
        $user = User::factory()->create();

        $wallet1 = Wallet::factory()->create(['balance' => 100]);
        $wallet2 = Wallet::factory()->create(['balance' => 100]);
        $response = $this->actingAs($user)->post('/api/transfer', [
            'amount' => 50,
            'sender_wallet' => $wallet1->id,
            'receiver_wallet' => $wallet2->id,
            'date' => now(),
        ]);
        $this->assertEquals(50, $wallet1->fresh()->balance);
        $this->assertEquals(150, $wallet2->fresh()->balance);

        $transfer = Transfer::find($response->json('data')['id']);
        $updateTransferResponse = $this->actingAs($user)->put('/api/update-transfer/' . $transfer->record->id, [
            'amount' => 60,
            'name' => 'Test Transfer Updated',
            'date' => now(),
            'sender_wallet' => $wallet2->id,
            'receiver_wallet' => $wallet1->id,
        ]);
        $this->assertEquals(160, $wallet1->fresh()->balance);
        $this->assertEquals(40, $wallet2->fresh()->balance);
    }
}
