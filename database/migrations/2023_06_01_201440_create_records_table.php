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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->text('description')->nullable();
            $table->string('type');
            $table->double('balance_before')->default(0);
            $table->double('balance_after')->default(0);
            $table->dateTime('date')->default(now());
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('balance_id');
            $table->unsignedBigInteger('budget_id')->nullable();


            $table->foreign('wallet_id')->references('id')
                ->on('wallets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('currency_id')->references('id')
                ->on('currencies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('category_id')->references('id')
                ->on('categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('balance_id')->references('id')
                ->on('balances')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('budget_id')->references('id')
                ->on('budgets')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
