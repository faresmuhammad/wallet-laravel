<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'target_amount' => $this->faker->randomFloat(),
            'current_amount' => $this->faker->randomFloat(),
            'period' => 'Month',
            'status' => 'Active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now(),
            'type' => $this->faker->word(),
            'master_id' => $this->faker->randomNumber(),
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
