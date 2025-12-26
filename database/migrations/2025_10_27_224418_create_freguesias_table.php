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
        // Cria a tabela 'freguesias'
        Schema::create('freguesias', function (Blueprint $table) {
            
            // Chave primária automática
            $table->id(); 
            
            // Nome da freguesia (obrigatório, máx 100 caracteres)
            $table->string('nome', 100); 
            
            // Coluna para guardar o ID do Concelho (Chave Estrangeira)
            // Deve ser do mesmo tipo que o id da tabela concelhos (unsignedBigInteger)
            $table->unsignedBigInteger('concelho_id'); 
            
            // Código administrativo opcional
            $table->string('codigo', 10)->nullable(); 
            
            // Datas de criação e atualização automáticas
            $table->timestamps(); 

            // --- RELAÇÕES E ÍNDICES ---

            // Define a regra de Chave Estrangeira:
            // Ligas a coluna 'concelho_id' desta tabela ao 'id' da tabela 'concelhos'.
            // 'onDelete cascade': Se apagares um Concelho, as Freguesias associadas são apagadas automaticamente.
            $table->foreign('concelho_id')
                  ->references('id')->on('concelhos')
                  ->onDelete('cascade'); 

            // Cria um índice para que as pesquisas por Concelho sejam rápidas
            $table->index('concelho_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Apaga a tabela 'freguesias'
        Schema::dropIfExists('freguesias');
    }
};