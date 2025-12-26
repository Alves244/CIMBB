<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Criação da tabela).
     */
    public function up(): void
    {
        Schema::create('inquerito_freguesias', function (Blueprint $table) {
            // ID único do inquérito
            $table->id();

            // Identificação da Freguesia e do Utilizador que preenche
            $table->unsignedBigInteger('freguesia_id'); 
            $table->unsignedBigInteger('utilizador_id'); 
            
            // Ano de referência dos dados
            $table->integer('ano'); 

            // --- AVALIAÇÃO (Perguntas 20 a 24) ---
            // Nível de integração (Escala 1-5)
            $table->tinyInteger('escala_integracao')->nullable(); 
            
            // Textos descritivos sobre a situação na freguesia
            $table->text('aspectos_positivos')->nullable();
            $table->text('aspectos_negativos')->nullable();
            
            // Satisfação global da autarquia (Escala 1-5)
            $table->tinyInteger('satisfacao_global')->nullable(); 

            // Espaço para recomendações ou observações
            $table->text('sugestoes')->nullable();
            
            // Regista a data/hora da submissão
            $table->timestamps(); 

            // --- REGRAS E RELAÇÕES ---
            // Ligações às tabelas 'freguesias' e 'users'
            $table->foreign('freguesia_id')->references('id')->on('freguesias')->onDelete('cascade');
            $table->foreign('utilizador_id')->references('id')->on('users')->onDelete('cascade');
            
            // RESTRIÇÃO CRÍTICA: Impede que uma freguesia submeta mais do que um inquérito para o mesmo ano
            $table->unique(['freguesia_id', 'ano']);
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_freguesias');
    }
};