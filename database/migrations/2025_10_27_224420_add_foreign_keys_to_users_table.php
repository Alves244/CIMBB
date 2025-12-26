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
        // Schema::table (em vez de create) indica que vamos EDITAR a tabela 'users'.
        Schema::table('users', function (Blueprint $table) {
            // Aqui é onde adicionas as novas colunas.
            // Exemplo: $table->string('nif')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Aqui deves desfazer o que fizeste em cima (apagar a coluna).
            // Exemplo: $table->dropColumn('nif');
        });
    }
};