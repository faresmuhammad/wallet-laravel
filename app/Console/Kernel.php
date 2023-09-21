<?php

namespace App\Console;

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Models\Budget;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        /**
         * Create a copy of the master budgets to be performed on each month independently
         */
        $schedule->call(function () {
            $masterBudgets = Budget::active()->isMaster()->get();
            foreach ($masterBudgets as $budget) {
                if (!$budget->end_at || $budget->end_at > now()) {
                    $newBudget = Budget::create([
                        'name' => $budget->name,
                        'target_amount' => $budget->target_amount,
                        'current_amount' => $budget->current_amount,
                        'period' => $budget->period,
                        'status' => BudgetStatus::Active->value,
                        'start_at' => now(),
                        'end_at' => now()->lastOfMonth(),
                        'type' => BudgetType::Repeatable->value,
                        'user_id' => $budget->user_id,
                    ]);
                    $newBudget->master()->associate($budget);
                    $newBudget->save();
                }
            }
        })->monthlyOn();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
