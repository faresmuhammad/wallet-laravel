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
            $table->string('name')->nullable();
            $table->string('type');
            $table->double('balance_before')->default(0);
            $table->double('balance_after')->default(0);
            $table->dateTime('date')->default(now());
            $table->foreignId('wallet_id');
            $table->foreignId('category_id')->nullable();
            $table->timestamps();
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
