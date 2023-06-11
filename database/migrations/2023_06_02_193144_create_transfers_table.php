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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_balance');
            $table->unsignedBigInteger('sender_wallet');
            $table->unsignedBigInteger('receiver_balance');
            $table->unsignedBigInteger('receiver_wallet');
            $table->double('amount');
            $table->unsignedBigInteger('record_id')->unique();
            $table->timestamps();

            $table->foreign('record_id')->references('id')
                ->on('records')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
