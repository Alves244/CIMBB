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
        Schema::create('agrupamentos', function (Blueprint $table) {
            // ID único do agrupamento escolar
            $table->id();

            // Nome oficial (ex: Agrupamento de Escolas Nuno Álvares)
            $table->string('nome');

            // Código do Ministério da Educação (opcional)
            $table->string('codigo')->nullable();

            // Relacionamento com a tabela 'concelhos'
            // cascadeOnUpdate: Se o ID do concelho mudar, atualiza aqui também.
            // restrictOnDelete: Impede apagar um concelho se este tiver agrupamentos associados.
            $table->foreignId('concelho_id')
                  ->constrained('concelhos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Datas de criação e atualização automática
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('agrupamentos');
    }
};