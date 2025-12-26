<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Cria a tabela).
     */
    public function up(): void
    {
        // Inicia a criação da tabela 'concelhos'
        Schema::create('concelhos', function (Blueprint $table) {
            
            // Cria a chave primária automática (ID numérico sequencial)
            $table->id(); 
            
            // Define o nome do concelho (limite de 100 caracteres).
            // O 'unique()' impede que existam dois concelhos com o mesmo nome.
            $table->string('nome', 100)->unique(); 
            
            // Campo opcional ('nullable') para códigos administrativos (ex: código DICO).
            $table->string('codigo', 10)->nullable(); 
            
            // Cria automaticamente as colunas de auditoria:
            // 'created_at' (data de criação) e 'updated_at' (data de atualização)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations (Reverte a operação).
     */
    public function down(): void
    {
        // Apaga a tabela 'concelhos' se ela existir na base de dados
        Schema::dropIfExists('concelhos');
    }
};