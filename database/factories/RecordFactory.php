<?php

namespace Database\Factories;

use App\Enums\RecordType;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */
class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $wallet = Wallet::all()->random();
        return [
            'name' => $this->faker->words(2, true),
            'amount' => $this->faker->numberBetween(10, 500),
            'type' => $this->faker->randomElement([RecordType::Expense, RecordType::Income]),
            'currency' => $wallet->currency,
            'date' => $this->faker->dateTimeBetween('-3 months'),
            'wallet_id' => $wallet->id,
        ];
    }

    public function currency($currency): RecordFactory|Factory
    {
        return $this->state(['currency' => $currency, 'wallet_id' => Wallet::where('currency', $currency)->first()?->id]);
    }

    public function wallet(Wallet $wallet): RecordFactory|Factory
    {
        return $this->state(['wallet_id' => $wallet->id, 'currency' => $wallet->currency]);
    }

    public function type(RecordType $type): RecordFactory|Factory
    {
        return $this->state(['type' => $type]);
    }

    public function amount(RecordType $type): RecordFactory|Factory
    {
        return $this->sequence(function (Sequence $sequence) use ($type) {
            return [
                'amount' => $type == RecordType::Income ? $this->faker->numberBetween(3000, 6000) : $this->faker->numberBetween(20, 300),
                'type' => $type
            ];
        });
    }
}
