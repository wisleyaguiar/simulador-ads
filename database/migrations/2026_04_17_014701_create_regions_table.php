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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->bigInteger('total_population')->unsigned();
            $table->bigInteger('reachable_audience')->unsigned(); // 70-80% da população
            $table->decimal('avg_cpm', 8, 2);           // R$
            $table->decimal('confidence_score', 3, 2)->default(0.70); // 0.00 a 1.00
            $table->timestamps(); // Controle de atualização semestral/anual
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
