<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /*
  type BudgetType
  master_id bigint [ref: > Budget.id]
  user_id bigint [ref: > User.id]
     */
    /*


enum BudgetType{
  Master
  Repeatable
}
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name',30)->unique();
            $table->double('target_amount');
            $table->double('current_amount')->default(0);
            $table->enum('period',['One Time','Week','Month','Year']);
            $table->enum('status',['Active','Finished','Not Started']);
            $table->date('start_at');
            $table->date('end_at');
            $table->enum('type',['Master','Repeatable']);
            $table->unsignedBigInteger('master_id');
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
