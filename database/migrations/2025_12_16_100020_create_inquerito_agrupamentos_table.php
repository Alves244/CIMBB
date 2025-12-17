<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquerito_agrupamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agrupamento_id')->constrained('agrupamentos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('utilizador_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('ano_referencia');
            $table->unsignedInteger('total_registos');
            $table->unsignedInteger('total_alunos');
            $table->timestamp('submetido_em')->nullable();
            $table->timestamps();

            $table->unique(['agrupamento_id', 'ano_referencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquerito_agrupamentos');
    }
};
