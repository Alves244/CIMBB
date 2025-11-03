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
            $table->id();
            $table->unsignedBigInteger('freguesia_id'); // A que freguesia responde
            $table->unsignedBigInteger('utilizador_id'); // Quem respondeu
            $table->integer('ano'); // Ano do inquérito

            // Pergunta 20: Escala de integração
            $table->tinyInteger('escala_integracao')->nullable(); // Valor de 1 a 5
            
            // Pergunta 21: Aspectos positivos
            $table->text('aspectos_positivos')->nullable();
            
            // Pergunta 22: Aspectos negativos
            $table->text('aspectos_negativos')->nullable();
            
            // Pergunta 23: Nível de satisfação global
            $table->tinyInteger('satisfacao_global')->nullable(); // Valor de 1 a 5

            // Pergunta 24: Sugestões
            $table->text('sugestoes')->nullable();
            
            $table->timestamps(); // Data de preenchimento

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