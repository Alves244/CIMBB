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
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            // Adiciona a coluna 'total_trabalhadores_outrem' à tabela 'inquerito_freguesias'
            $table->integer('total_trabalhadores_outrem')->default(0)->after('total_por_setor_outrem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            $table->dropColumn('total_trabalhadores_outrem');
        });
    }
};
