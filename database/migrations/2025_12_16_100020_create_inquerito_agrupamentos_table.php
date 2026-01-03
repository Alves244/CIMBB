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
        // Cria a tabela 'inquerito_agrupamentos'
        Schema::create('inquerito_agrupamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agrupamento_id')->constrained('agrupamentos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('utilizador_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('ano_referencia');
            $table->unsignedInteger('total_registos');
            $table->unsignedInteger('total_alunos');
            $table->timestamp('submetido_em')->nullable();
            $table->timestamps();

            // Garante que não existam duplicados para o mesmo agrupamento e ano de referência
            $table->unique(['agrupamento_id', 'ano_referencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_agrupamentos');
    }
};
