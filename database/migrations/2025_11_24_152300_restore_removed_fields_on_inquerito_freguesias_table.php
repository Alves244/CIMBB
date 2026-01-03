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
            // Recria os campos removidos anteriormente na tabela 'inquerito_freguesias'
            // 1. Recriar o campo 'total_criancas'
            if (! Schema::hasColumn('inquerito_freguesias', 'total_criancas')) {
                $table->integer('total_criancas')->default(0)->after('total_adultos');
            }
            // 2. Recriar o campo 'total_arrendada'
            if (! Schema::hasColumn('inquerito_freguesias', 'total_arrendada')) {
                $table->integer('total_arrendada')->default(0)->after('total_propria');
            }
            // 3. Recriar os campos 'total_por_setor_propria' e 'total_por_setor_outrem'
            if (! Schema::hasColumn('inquerito_freguesias', 'total_por_setor_propria')) {
                $table->json('total_por_setor_propria')->nullable()->after('total_arrendada');
            }
            // 4. Recriar o campo 'total_por_setor_outrem'
            if (! Schema::hasColumn('inquerito_freguesias', 'total_por_setor_outrem')) {
                $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
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
