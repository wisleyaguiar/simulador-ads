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
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('budget', 10, 2);
            $table->string('payment_type')->nullable();          // pré-paga / pós-paga
            $table->integer('campaign_days')->unsigned();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->foreignId('segment_id')->constrained()->onDelete('cascade');
            $table->string('goal');                               // tráfego / leads / conversões
            $table->string('maturity_level');                     // iniciante / intermediário / avançado
            $table->tinyInteger('campaign_month')->unsigned();    // 1-12 (mês sazonal)
            $table->json('results_json');                         // cálculos finais para histórico
            $table->timestamp('created_at')->useCurrent();

            // Índices de performance
            $table->index('user_id');
            $table->index('region_id');
            $table->index('segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};
