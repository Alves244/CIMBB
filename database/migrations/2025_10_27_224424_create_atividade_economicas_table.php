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
        Schema::create('atividade_economicas', function (Blueprint $table) {

            // Definição das colunas da tabela 'atividade_economicas'
            $table->id();
            $table->unsignedBigInteger('familia_id');
            $table->enum('tipo', ['conta_propria', 'conta_outrem']);
            $table->unsignedBigInteger('setor_id'); 
            $table->text('descricao')->nullable(); 
            $table->timestamps(); // Usa created_at para data_registo e updated_at para data_ultima_atualizacao

            // Chave estrangeira para familia_id -> familias.id
            $table->foreign('familia_id')
                  ->references('id')->on('familias')
                  ->onDelete('cascade');

            // Chave estrangeira para setor_id -> setor_atividades.id 
            $table->foreign('setor_id')
                  ->references('id')->on('setor_atividades')
                  ->onDelete('restrict');

            // Índices para otimização de consultas
            $table->index('familia_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividade_economicas');
    }
};