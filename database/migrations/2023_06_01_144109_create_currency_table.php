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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name',30);
            $table->string('pfx_symbol',5)->default('');
            $table->string('sfx_symbol',5)->default('');
            $table->string('unit_name',30);
            $table->string('cent_name',30)->default('');
            $table->string('symbol_name',30);
            $table->smallInteger('scale')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency');
    }
};
