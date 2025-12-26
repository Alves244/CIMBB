<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Criação dos detalhes do inquérito escolar).
     */
    public function up(): void
    {
        Schema::create('inquerito_agrupamento_registos', function (Blueprint $table) {
            // ID único do registo de linha
            $table->id();

            // Liga esta linha ao inquérito pai
            // cascadeOnDelete: Se o inquérito for apagado, estas linhas desaparecem automaticamente.
            $table->foreignId('inquerito_id')
                  ->constrained('inquerito_agrupamentos')
                  ->cascadeOnDelete();

            // Dados de caracterização do grupo de alunos
            $table->string('nacionalidade'); 
            $table->string('ano_letivo', 9); // Ex: "2024/2025"

            // Onde os alunos residem (crucial para estatística por território)
            $table->foreignId('concelho_id')
                  ->constrained('concelhos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Ciclo de estudos (ex: 1º Ciclo, Secundário, Profissional)
            $table->string('nivel_ensino');

            // A contagem de alunos que partilham estas mesmas características
            $table->unsignedInteger('numero_alunos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_agrupamento_registos');
    }
};