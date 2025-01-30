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
        Schema::create('label_statistic_pivot', function (Blueprint $table) {
            $table->foreignId('statistic_id')->constrained('statistics');
            $table->foreignId('label_id')->constrained('labels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_statistic_pivot');
    }
};
