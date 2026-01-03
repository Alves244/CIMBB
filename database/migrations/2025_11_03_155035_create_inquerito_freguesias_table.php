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
        Schema::create('inquerito_freguesias', function (Blueprint $table) {
            // Definição das colunas da tabela 'inquerito_freguesias'
            $table->id();
            $table->unsignedBigInteger('freguesia_id');
            $table->unsignedBigInteger('utilizador_id');
            $table->integer('ano');

            // Pergunta 1: Satisfação com serviços públicos
            $table->tinyInteger('escala_integracao')->nullable();
            
            // Pergunta 2: Acesso a serviços de saúde
            $table->text('aspectos_positivos')->nullable();
            
            // Pergunta 3: Acesso a educação
            $table->text('aspectos_negativos')->nullable();
            
            // Pergunta 4: Infraestruturas e transportes
            $table->tinyInteger('satisfacao_global')->nullable();

            // Pergunta 5: Sugestões para melhorias
            $table->text('sugestoes')->nullable();
            
            // Data de preenchimento do inquérito
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('freguesia_id')->references('id')->on('freguesias')->onDelete('cascade');
            $table->foreign('utilizador_id')->references('id')->on('users')->onDelete('cascade');
            
            // Garantir que só há 1 inquérito por freguesia por ano
            $table->unique(['freguesia_id', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_freguesias');
    }
};