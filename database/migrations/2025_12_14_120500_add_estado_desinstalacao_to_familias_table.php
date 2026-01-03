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
        Schema::table('familias', function (Blueprint $table) {
            // Adiciona as colunas relacionadas ao estado de desinstalação à tabela 'familias'
            $table->string('estado_acompanhamento', 30)->default('ativa')->after('ano_instalacao');
            $table->date('data_desinstalacao')->nullable()->after('estado_acompanhamento');
            $table->smallInteger('ano_desinstalacao')->nullable()->after('data_desinstalacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn(['estado_acompanhamento', 'data_desinstalacao', 'ano_desinstalacao']);
        });
    }
};
