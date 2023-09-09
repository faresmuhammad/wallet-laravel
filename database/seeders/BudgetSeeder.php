<?php

namespace Database\Seeders;

use App\Enums\BudgetType;
use App\Models\Budget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $master = Budget::factory()->create([
           'type' => BudgetType::Master->value,
           'master_id' => null
        ]);

        Budget::factory()->create([
           'type' => BudgetType::Repeatable->value,
           'master_id' => $master->id
        ]);
    }
}
