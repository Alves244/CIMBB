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
            
            // 1. Adicionar novo campo para indivíduos com negócio próprio
            $table->integer('total_individuos_negocio_proprio')->default(0)->after('total_propria');

            // 2. Remover os campos que já não são pedidos
            $table->dropColumn('total_criancas');
            $table->dropColumn('total_arrendada');
            $table->dropColumn('total_por_setor_propria');
            $table->dropColumn('total_por_setor_outrem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            // 1. Remover o novo campo
            $table->dropColumn('total_individuos_negocio_proprio');

            // 2. Recriar os campos antigos
            $table->integer('total_criancas')->default(0)->after('total_adultos');
            $table->integer('total_arrendada')->default(0)->after('total_propria');
            $table->json('total_por_setor_propria')->nullable()->after('total_arrendada');
            $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria');
        });
    }
};