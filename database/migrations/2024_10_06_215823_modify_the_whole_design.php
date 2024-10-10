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

        /**
         * Dropping color, include_to_stats columns and change initial_balance column to balance
         * Adding currency_id column for Many-to-One relationship
         */
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['color','initial_balance','include_to_stats']);
            $table->addColumn('double', 'balance')->default(0);
            $table->foreignId('currency_id')->references('id')->on('currencies');
        });

        /**
         * Dropping balance_before, balance_after columns
         * Changing description to name
         * Dropping foreign keys of wallet, currency and balance
         */
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn(['description','balance_before','balance_after']);
            $table->addColumn('text','name');
            $table->dropConstrainedForeignId('wallet_id');
            $table->dropConstrainedForeignId('currency_id');
            $table->dropConstrainedForeignId('balance_id');
        });

        /**
         * Dropping sender_balance and receiver_balance columns
         */
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['sender_balance','receiver_balance']);
        });

        Schema::table('balance_per_dates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('balance_id');
        });
        /**
         * Creating Strategies Table
         */
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('activated')->default(false);
            $table->foreignId('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        /**
         * Creating Rules Table
         */
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('initial_balance')->default(0);
            $table->float('ratio');
            $table->boolean('include_to_stats')->default(true);
            $table->foreignId('strategy_id')->references('id')->on('strategies');
            $table->foreignId('currency_id')->references('id')->on('currencies');
            $table->timestamps();
        });

        /**
         * Add strategy_id column for Many-to-One relationship
         */
        Schema::table('wallets', function (Blueprint $table) {
           $table->foreignId('strategy_id')->references('id')->on('strategies');
        });

        /**
         * Add strategy_id column for Many-to-One relationship
         */
        Schema::table('records', function (Blueprint $table) {
           $table->foreignId('strategy_id')->references('id')->on('strategies');
        });


        /**
         * Dropping balances table after dropping its foreign keys
         */
        Schema::table('balances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wallet_id');
            $table->dropConstrainedForeignId('currency_id');
        });
        Schema::dropIfExists('balances');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
