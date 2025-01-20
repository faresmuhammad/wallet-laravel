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
        Schema::create('label_record_pivot', function (Blueprint $table) {
            $table->foreignId('label_id')->constrained('labels')->cascadeOnUpdate()->noActionOnDelete();
            $table->foreignId('record_id')->constrained('records')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_record_pivot');
    }
};
