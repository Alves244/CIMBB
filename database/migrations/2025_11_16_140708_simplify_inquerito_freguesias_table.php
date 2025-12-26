<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Executa as alterações).
     */
    public function up(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            
            // 1. Adiciona campo para a Pergunta 16: imigrantes com negócio próprio
            $table->integer('total_individuos_negocio_proprio')
                  ->default(0)
                  ->after('total_propria');

            // 2. Remove colunas que deixaram de ser relevantes para este inquérito
            $table->dropColumn([
                'total_criancas', 
                'total_arrendada', 
                'total_por_setor_propria', 
                'total_por_setor_outrem'
            ]);
        });
    }

    /**
     * Reverse the migrations (Reverte as alterações).
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            // 1. Remove o campo novo
            $table->dropColumn('total_individuos_negocio_proprio');

            // 2. Recria os campos originais para restaurar a estrutura anterior
            $table->integer('total_criancas')->default(0)->after('total_adultos');
            $table->integer('total_arrendada')->default(0)->after('total_propria');
            $table->json('total_por_setor_propria')->nullable()->after('total_arrendada');
            $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria');
        });
    }
};