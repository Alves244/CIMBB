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
        // Cria a tabela de histórico de atividades
        Schema::create('log_acessos', function (Blueprint $table) {
            
            // ID único do registo de log
            $table->id(); 

            // ID do utilizador que realizou a ação (Liga à tabela 'users')
            $table->unsignedBigInteger('utilizador_id'); 

            // Nome da ação (ex: 'LOGIN', 'CRIAR_FAMILIA', 'EXPORTAR_PDF')
            $table->string('acao', 100); 

            // Data e hora exata da ocorrência (Preenchido automaticamente)
            $table->timestamp('data_hora')->useCurrent(); 

            // Endereço IP de onde veio o pedido (Suporta IPv4 e IPv6)
            $table->string('ip', 45)->nullable(); 

            // Detalhes extras sobre a ação (ex: "Exportou dados do concelho de Castelo Branco")
            $table->text('descricao')->nullable(); 

            // --- RELAÇÕES ---
            // Define que o log pertence a um utilizador da tabela 'users'
            $table->foreign('utilizador_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('log_acessos');
    }
};