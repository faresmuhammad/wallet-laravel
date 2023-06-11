<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->double('value');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')
                ->on('wallets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('currency_id')->references('id')
                ->on('currencies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
