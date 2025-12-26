<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Executa as migrações.
     * Cria a tabela 'password_resets' que armazena os pedidos de recuperação pendentes.
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            // Coluna para armazenar o e-mail do utilizador que solicitou a recuperação.
            // O método ->index() é crucial aqui: como a busca pelo token será feita 
            // através do e-mail (WHERE email = '...'), o índice acelera drasticamente a consulta.
            $table->string('email')->index();

            // Coluna para armazenar o token de segurança (hash).
            // Este é o código único enviado por e-mail que valida a identidade do utilizador.
            $table->string('token');

            // Coluna de carimbo temporal (TIMESTAMP).
            // Diferente das tabelas normais, esta não usa 'updated_at'. 
            // Serve exclusivamente para calcular a expiração do token (ex: se created_at + 60min < agora, o token é inválido).
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverte as migrações.
     * Remove a tabela de resets caso seja necessário fazer rollback à base de dados.
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}