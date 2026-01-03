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
        Schema::create('inquerito_periodos', function (Blueprint $table) {
            // Cria a tabela 'inquerito_periodos'
            $table->id();
            $table->enum('tipo', ['freguesia', 'agrupamento']);
            $table->unsignedInteger('ano');
            $table->dateTime('abre_em');
            $table->dateTime('fecha_em');
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Garante que não existam duplicados para o mesmo tipo e ano
            $table->unique(['tipo', 'ano']);
            $table->index('abre_em');
            $table->index('fecha_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_periodos');
    }
};
