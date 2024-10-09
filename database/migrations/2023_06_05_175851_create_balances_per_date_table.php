<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('balance_per_dates', function (Blueprint $table) {
            $table->id();
            $table->double('value');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('balance_id');
            $table->date('date');

            $table->foreign('wallet_id')->references('id')
                ->on('wallets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('balance_id')->references('id')
                ->on('balances')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances_per_date');
    }
};
