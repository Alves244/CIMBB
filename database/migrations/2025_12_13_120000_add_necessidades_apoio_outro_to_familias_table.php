<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Adiciona a coluna).
     */
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            
            // Cria um campo para especificar outras necessidades de apoio
            // que não estejam listadas nas opções predefinidas (JSON).
            // O 'nullable' permite que o campo fique vazio.
            $table->string('necessidades_apoio_outro', 255)
                  ->nullable()
                  ->after('necessidades_apoio');
        });
    }

    /**
     * Reverse the migrations (Remove a coluna).
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            // Elimina a coluna caso seja necessário reverter a migração
            $table->dropColumn('necessidades_apoio_outro');
        });
    }
};