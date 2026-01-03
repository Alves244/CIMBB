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
        Schema::create('ticket_suportes', function (Blueprint $table) {

            // Definição das colunas da tabela 'ticket_suportes'
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->unsignedBigInteger('utilizador_id');
            $table->string('assunto', 200);
            $table->text('descricao');
            $table->enum('estado', ['aberto', 'em_processamento', 'resolvido', 'fechado'])->default('aberto');
            $table->text('resposta_admin')->nullable();
            $table->dateTime('data_resposta')->nullable();
            $table->unsignedBigInteger('administrador_id')->nullable();
            $table->string('anexo', 255)->nullable();
            $table->enum('categoria', ['duvida', 'erro', 'sugestao', 'outro'])->default('duvida');
            $table->timestamps();

            // Chave estrangeira para utilizador_id -> users.id
            $table->foreign('utilizador_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Chave estrangeira para administrador_id -> users.id    
            $table->foreign('administrador_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // Índices para otimização de consultas
            $table->index('estado');
            $table->index('created_at');
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_suportes');
    }
};