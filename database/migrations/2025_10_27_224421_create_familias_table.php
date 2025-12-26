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
        // Cria a tabela 'familias'
        Schema::create('familias', function (Blueprint $table) {
            
            // Chave primária (ID autoincrementável)
            $table->id();

            // Código da família (único e obrigatório)
            $table->string('codigo', 50)->unique();

            // Ano em que a família se instalou na região
            $table->integer('ano_instalacao');

            // País de origem da família
            $table->string('nacionalidade', 50);

            // ID da Freguesia (chave estrangeira)
            $table->unsignedBigInteger('freguesia_id'); 

            // Define opções fixas para o tipo de casa
            $table->enum('tipologia_habitacao', ['casa', 'quinta', 'apartamento']);

            // Define se a casa é própria ou arrendada
            $table->enum('tipologia_propriedade', ['propria', 'arrendada']);

            // Datas automáticas de registo e atualização (created_at e updated_at)
            $table->timestamps(); 

            // ID do utilizador (técnico) que fez o registo
            $table->unsignedBigInteger('utilizador_registo_id');

            // --- RELAÇÕES (Chaves Estrangeiras) ---

            // Liga à tabela freguesias. Se a freguesia for apagada, apaga as famílias (cascade).
            $table->foreign('freguesia_id')
                  ->references('id')->on('freguesias')
                  ->onDelete('cascade');

            // Liga à tabela users. Impede apagar o utilizador se ele tiver registos (restrict).
            $table->foreign('utilizador_registo_id')
                  ->references('id')->on('users')
                  ->onDelete('restrict');

            // --- PERFORMANCE (Índices) ---
            
            // Cria índices para acelerar pesquisas e filtros nos relatórios
            $table->index('freguesia_id');
            $table->index('ano_instalacao');
            $table->index('nacionalidade');
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};