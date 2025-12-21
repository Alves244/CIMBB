<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquerito_agrupamento_registos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquerito_id')->constrained('inquerito_agrupamentos')->cascadeOnDelete();
            $table->string('nacionalidade');
            $table->string('ano_letivo', 9);
            $table->foreignId('concelho_id')->constrained('concelhos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nivel_ensino');
            $table->unsignedInteger('numero_alunos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquerito_agrupamento_registos');
    }
};
