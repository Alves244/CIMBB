<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Criação da tabela de inquéritos escolares).
     */
    public function up(): void
    {
        Schema::create('inquerito_agrupamentos', function (Blueprint $table) {
            // ID único do inquérito
            $table->id();

            // Relacionamento com o Agrupamento (Escola)
            $table->foreignId('agrupamento_id')
                  ->constrained('agrupamentos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Identifica que utilizador (Diretor/Administrativo) submeteu os dados
            $table->foreignId('utilizador_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Ano letivo ou civil a que os dados dizem respeito
            $table->unsignedInteger('ano_referencia');

            // Metadados de controlo:
            // total_registos: ex. número de turmas ou formulários individuais
            // total_alunos: total acumulado de alunos imigrantes no agrupamento
            $table->unsignedInteger('total_registos');
            $table->unsignedInteger('total_alunos');

            // Data e hora da submissão final (para controlo de prazos)
            $table->timestamp('submetido_em')->nullable();

            // Timestamps padrão do Laravel (created_at, updated_at)
            $table->timestamps();

            // REGRA DE INTEGRIDADE: Impede que o mesmo agrupamento envie 
            // dois inquéritos para o mesmo ano.
            $table->unique(['agrupamento_id', 'ano_referencia']);
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_agrupamentos');
    }
};