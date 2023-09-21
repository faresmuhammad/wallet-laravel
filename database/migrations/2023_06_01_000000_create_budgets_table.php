<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name',30);
            $table->double('target_amount');
            $table->double('current_amount')->default(0);
            $table->string('period');
            $table->string('status');
            $table->date('start_at');
            $table->date('end_at');
            $table->string('type');
            $table->unsignedBigInteger('master_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('master_id')->references('id')
                ->on('budgets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('user_id')->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
