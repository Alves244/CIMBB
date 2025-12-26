<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Repõe as colunas de forma segura).
     */
    public function up(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            
            // Se a coluna 'total_criancas' NÃO existir, cria-a
            if (! Schema::hasColumn('inquerito_freguesias', 'total_criancas')) {
                $table->integer('total_criancas')->default(0)->after('total_adultos');
            }

            // Se a coluna 'total_arrendada' NÃO existir, cria-a
            if (! Schema::hasColumn('inquerito_freguesias', 'total_arrendada')) {
                $table->integer('total_arrendada')->default(0)->after('total_propria');
            }

            // Repõe o campo JSON para setores (conta própria) se faltar
            if (! Schema::hasColumn('inquerito_freguesias', 'total_por_setor_propria')) {
                $table->json('total_por_setor_propria')->nullable()->after('total_arrendada');
            }

            // Repõe o campo JSON para setores (conta outrem) se faltar
            if (! Schema::hasColumn('inquerito_freguesias', 'total_por_setor_outrem')) {
                $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria');
            }
        });
    }

    /**
     * Reverse the migrations (Remove as colunas se existirem).
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            // Verifica a existência de cada coluna antes de tentar apagá-la
            if (Schema::hasColumn('inquerito_freguesias', 'total_por_setor_outrem')) {
                $table->dropColumn('total_por_setor_outrem');
            }

            if (Schema::hasColumn('inquerito_freguesias', 'total_por_setor_propria')) {
                $table->dropColumn('total_por_setor_propria');
            }

            if (Schema::hasColumn('inquerito_freguesias', 'total_arrendada')) {
                $table->dropColumn('total_arrendada');
            }

            if (Schema::hasColumn('inquerito_freguesias', 'total_criancas')) {
                $table->dropColumn('total_criancas');
            }
        });
    }
};