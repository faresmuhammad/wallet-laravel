<?php

namespace Tests\Feature;

use App\Http\Requests\PayRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NewRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;

class NewRecordTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        $this->seed();
    }

    public function test_pay_call_subtract_the_amount_from_wallet_balance()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $request = $this->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'category_id' => 1,
            'date' => now(),
        ]);

        $this->assertEquals(50, $wallet->fresh()->balance);
        $this->assertEquals(201, $request->getStatusCode());
    }

    public function test_topup_call_add_the_amount_to_wallet_balance()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $request = $this->post('/api/topup/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Topup',
            'category_id' => 1,
            'date' => now(),
        ]);
        $this->assertEquals(150, $wallet->fresh()->balance);
        $this->assertEquals(201, $request->getStatusCode());
    }

    public function test_create_a_record_with_different_currency_from_wallet()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $payRequest = $this->post('/api/pay/' . $wallet->id, [
            'amount' => 50,
            'name' => 'Test Pay',
            'category_id' => 1,
            'date' => now(),
            'currency' => 'USD',
        ]);
        $this->expectExceptionMessage("Error while pay process.");
//        dsd($payRequest);
//        $topupRequest = $this->post('/api/topup/' . $wallet->id, [
//            'amount' => 50,
//            'name' => 'Test Topup',
//            'category_id' => 1,
//            'date' => now(),
//            'currency' => 'EUR',
//        ]);
//        $this->expectException(HttpResponseException::class);
//        $payRequest->ass(403);
//        $topupRequest->assertStatus(403);
    }
}
