<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     * Este método é chamado quando o comando 'php artisan migrate' é executado.
     * Ele cria a tabela 'users' e define as suas colunas.
     */
    public function up(): void
    {
        // Cria a tabela 'users' utilizando o construtor de esquema (Schema Builder).
        Schema::create('users', function (Blueprint $table) {
            // Cria uma coluna 'id' auto-incrementável (Primary Key / BigInteger).
            $table->id();

            // Coluna para o nome do utilizador (VARCHAR).
            $table->string('nome');

            // Coluna para o e-mail (VARCHAR).
            // O método ->unique() adiciona um índice único na base de dados, impedindo
            // que dois utilizadores se registem com o mesmo endereço de e-mail.
            $table->string('email')->unique();

            // Coluna para registar a data/hora da verificação de e-mail (TIMESTAMP).
            // O método ->nullable() permite que este campo fique vazio (NULL) caso o utilizador
            // ainda não tenha verificado a conta.
            $table->timestamp('email_verified_at')->nullable();

            // Coluna para armazenar a password encriptada (VARCHAR).
            $table->string('password');

            // Cria uma coluna 'remember_token' (VARCHAR de 100 caracteres).
            // Utilizada pelo sistema de autenticação para manter a sessão "Lembrar-me" ativa.
            $table->rememberToken();

            // Coluna do tipo ENUM que restringe os valores possíveis para o campo 'perfil'.
            // Define os papéis de acesso: 'freguesia', 'cimbb' (técnico) ou 'admin'.
            // Define 'freguesia' como o valor predeterminado se nenhum for especificado.
            $table->enum('perfil', ['freguesia', 'cimbb', 'admin'])->default('freguesia');

            // Coluna para a chave estrangeira da Freguesia (BIGINT UNSIGNED).
            // Permite NULL, pois administradores ou técnicos da CIMBB não pertencem a uma freguesia específica.
            $table->unsignedBigInteger('freguesia_id')->nullable();

            // Coluna para contacto telefónico, com limite de 20 caracteres e permitindo valor nulo.
            $table->string('telemovel', 20)->nullable();

            // Cria automaticamente duas colunas TIMESTAMP: 'created_at' e 'updated_at'.
            // O Laravel gere estas colunas para registar quando o registo foi criado e modificado.
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrações.
     * Este método é chamado quando o comando 'php artisan migrate:rollback' é executado.
     * A sua função é desfazer o que foi feito no método up(), apagando a tabela.
     */
    public function down(): void
    {
        // Remove a tabela 'users' se ela existir na base de dados.
        Schema::dropIfExists('users');
    }
};