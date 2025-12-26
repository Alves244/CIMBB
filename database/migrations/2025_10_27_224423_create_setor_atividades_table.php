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
        // Cria a tabela 'setor_atividades'
        Schema::create('setor_atividades', function (Blueprint $table) {
            
            // ID único do setor (Chave Primária)
            $table->id(); 
            
            // Nome do setor (Ex: "Restauração"). 
            // 'unique' impede nomes duplicados na lista.
            $table->string('nome', 100)->unique(); 
            
            // Campo de texto longo para detalhar o que o setor abrange (opcional)
            $table->text('descricao')->nullable(); 
            
            // Define se o setor aparece nas opções de escolha (predefinido como Ativo)
            $table->boolean('ativo')->default(true); 
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        // Remove a tabela
        Schema::dropIfExists('setor_atividades');
    }
};