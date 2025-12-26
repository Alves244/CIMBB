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
        Schema::create('ticket_mensagens', function (Blueprint $table) {
            // ID único da mensagem
            $table->id();

            // Liga a mensagem ao Ticket pai
            // 'cascade' apaga as mensagens se o ticket for removido
            $table->foreignId('ticket_id')
                ->constrained('ticket_suportes')
                ->onDelete('cascade');

            // Identifica quem escreveu (Utilizador ou Admin)
            $table->foreignId('autor_id')
                ->constrained('users')
                ->onDelete('cascade');

            // O conteúdo da resposta ou dúvida
            $table->text('mensagem');

            // Regista a data e hora do envio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_mensagens');
    }
};