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
        // Cria a tabela para guardar variáveis globais do sistema
        Schema::create('configuracao_sistemas', function (Blueprint $table) {
            
            // ID único do registo
            $table->id(); 

            // Nome da configuração (ex: 'NOME_APP'). 
            // 'unique' garante que não há duas chaves iguais.
            $table->string('chave', 100)->unique(); 

            // O conteúdo da definição em si
            $table->text('valor'); 

            // Explicação para o administrador saber o que esta chave controla
            $table->text('descricao')->nullable(); 

            // Define como o sistema deve ler o dado (Texto, Número, Sim/Não ou Lista)
            $table->enum('tipo', ['string', 'int', 'boolean', 'json'])->default('string'); 

            // Regista automaticamente a data da última alteração
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); 
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_sistemas');
    }
};