<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalAccessTokensTable extends Migration
{
    /**
     * Executa as migrações.
     * Cria a tabela 'personal_access_tokens' para gestão de autenticação via API.
     */
    public function up()
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            // Identificador único do registo do token.
            $table->id();

            // Cria duas colunas: 'tokenable_id' e 'tokenable_type'.
            // Isto é uma relação polimórfica. Permite emitir tokens para qualquer modelo 
            // da aplicação (ex: um token para um 'User', outro para um 'Admin'), 
            // sem precisar de tabelas de tokens separadas para cada tipo de entidade.
            $table->morphs('tokenable');

            // Um nome descritivo para o token (ex: "iPad do Técnico", "Integração Externa").
            // Permite ao utilizador revogar tokens específicos reconhecendo o nome.
            $table->string('name');

            // Armazena o hash do token (SHA-256), e não o token em texto simples.
            // O campo é limitado a 64 caracteres e deve ser único. 
            // Se a base de dados for comprometida, os atacantes não conseguem usar estes tokens.
            $table->string('token', 64)->unique();

            // Campo de texto (normalmente JSON) que define as permissões (scopes) deste token.
            // Define o que este token específico pode fazer (ex: ['inqueritos:create', 'familias:read']).
            $table->text('abilities')->nullable();

            // Regista a data e hora da última vez que este token foi usado para fazer um pedido.
            // Essencial para segurança: permite identificar e remover tokens inativos ou abandonados.
            $table->timestamp('last_used_at')->nullable();

            // Colunas created_at e updated_at.
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrações.
     * Elimina a tabela de tokens de acesso.
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
}