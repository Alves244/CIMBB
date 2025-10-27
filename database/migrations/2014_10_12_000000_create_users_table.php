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
        Schema::create('users', function (Blueprint $table) { // Nome da tabela é 'users'
            $table->id();
            $table->string('nome'); // Renomeado de 'name' para 'nome' [cite: 32, 317]
            $table->string('email')->unique(); // [cite: 33, 317]
            $table->timestamp('email_verified_at')->nullable(); // Padrão Laravel, pode ser útil
            $table->string('password'); // Corresponde a password_hash [cite: 36, 317]
            $table->rememberToken(); // Padrão Laravel para "lembrar-me"

            // --- Colunas Adicionadas ---
            $table->enum('perfil', ['freguesia', 'cimbb', 'admin'])->default('freguesia'); // [cite: 34, 317]
            $table->unsignedBigInteger('freguesia_id')->nullable(); // [cite: 35, 317] Permite NULL porque admin/cimbb podem não ter freguesia
            $table->string('telemovel', 20)->nullable(); // [cite: 37, 317]
            // --------------------------

            $table->timestamps(); // Cria created_at e updated_at (substitui data_criacao [cite: 38, 317])

            // Chave estrangeira para freguesia_id (IMPORTANTE: Adiciona isto DEPOIS de criares a migration das freguesias)
            // Vamos deixar comentado por agora e adicionar mais tarde ou numa migration separada
            // $table->foreign('freguesia_id')->references('id')->on('freguesias')->onDelete('set null');
            // $table->index('freguesia_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};