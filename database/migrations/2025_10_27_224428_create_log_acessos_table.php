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
        Schema::create('log_acessos', function (Blueprint $table) {

            // Definição das colunas da tabela 'log_acessos'
            $table->id();
            $table->unsignedBigInteger('utilizador_id');
            $table->string('acao', 100);
            $table->timestamp('data_hora')->useCurrent();
            $table->string('ip', 45)->nullable();
            $table->text('descricao')->nullable();

            // Chave estrangeira para utilizador_id -> users.id
            $table->foreign('utilizador_id')
                  ->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_acessos');
    }
};