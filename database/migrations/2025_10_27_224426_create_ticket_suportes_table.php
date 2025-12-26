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
        Schema::create('ticket_suportes', function (Blueprint $table) {
            // ID único do ticket
            $table->id(); 

            // Código de referência único (ex: TICKET-2025-001)
            $table->string('codigo', 20)->unique(); 

            // ID do utilizador que abriu o ticket (Freguesia/Escola)
            $table->unsignedBigInteger('utilizador_id'); 

            // Assunto e corpo da mensagem
            $table->string('assunto', 200); 
            $table->text('descricao'); 

            // Estado do pedido (Controla o fluxo de trabalho)
            $table->enum('estado', ['aberto', 'em_processamento', 'resolvido', 'fechado'])->default('aberto'); 

            // Campos para a resposta do Administrador da CIMBB
            $table->text('resposta_admin')->nullable(); 
            $table->dateTime('data_resposta')->nullable(); 
            $table->unsignedBigInteger('administrador_id')->nullable(); 

            // Caminho para ficheiro em anexo (ex: print de um erro)
            $table->string('anexo', 255)->nullable(); 

            // Classificação do problema
            $table->enum('categoria', ['duvida', 'erro', 'sugestao', 'outro'])->default('duvida'); 

            // Datas de criação (data_criacao) e última atualização
            $table->timestamps(); 

            // --- RELAÇÕES ---

            // Se o utilizador for apagado, os seus tickets desaparecem (cascade)
            $table->foreign('utilizador_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Se o administrador for apagado, o ticket mantém-se mas sem responsável (set null)
            $table->foreign('administrador_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // --- PERFORMANCE ---
            // Índices para listagens rápidas no painel de controlo
            $table->index('estado');
            $table->index('created_at'); 
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_suportes');
    }
};