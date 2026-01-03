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
        // Criação da tabela 'familias'
        Schema::create('familias', function (Blueprint $table) {
            // Define as colunas da tabela 'familias'
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->integer('ano_instalacao');
            $table->string('nacionalidade', 50);
            $table->unsignedBigInteger('freguesia_id');
            $table->enum('tipologia_habitacao', ['casa', 'quinta', 'apartamento']);
            $table->enum('tipologia_propriedade', ['propria', 'arrendada']);
            $table->timestamps();
            $table->unsignedBigInteger('utilizador_registo_id');
            $table->foreign('freguesia_id')
                  ->references('id')->on('freguesias')
                  ->onDelete('cascade');
            $table->foreign('utilizador_registo_id')
                  ->references('id')->on('users')
                  ->onDelete('restrict');

            
            $table->index('freguesia_id');
            $table->index('ano_instalacao');
            $table->index('nacionalidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};