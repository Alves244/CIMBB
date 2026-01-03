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
        Schema::create('configuracao_sistemas', function (Blueprint $table) {

            // Definição das colunas da tabela 'configuracao_sistemas'
            $table->id();
            $table->string('chave', 100)->unique();
            $table->text('valor');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['string', 'int', 'boolean', 'json'])->default('string');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_sistemas');
    }
};