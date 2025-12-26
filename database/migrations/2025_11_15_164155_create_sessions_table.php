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
        Schema::create('sessions', function (Blueprint $table) {
            // ID único da sessão (string aleatória)
            $table->string('id')->primary();

            // ID do utilizador autenticado (permite saber quem está online)
            $table->foreignId('user_id')->nullable()->index();

            // Endereço IP do utilizador
            $table->string('ip_address', 45)->nullable();

            // Informações do navegador e sistema operativo
            $table->text('user_agent')->nullable();

            // Dados da sessão serializados (cesto de compras, inputs temporários, etc.)
            $table->longText('payload');

            // Timestamp da última interação (usado para expirar sessões inativas)
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};