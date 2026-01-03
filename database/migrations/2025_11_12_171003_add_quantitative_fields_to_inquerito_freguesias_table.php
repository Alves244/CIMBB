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

            // Localização da habitação
            $table->integer('total_nucleo_urbano')->default(0)->after('ano');
            $table->integer('total_aldeia_anexa')->default(0)->after('total_nucleo_urbano');
            $table->integer('total_agroflorestal')->default(0)->after('total_aldeia_anexa');

            // Indivíduos na família
            $table->integer('total_adultos')->default(0)->after('total_agroflorestal');
            $table->integer('total_criancas')->default(0)->after('total_adultos');

            // Tipo de propriedade
            $table->integer('total_propria')->default(0)->after('total_criancas');
            $table->integer('total_arrendada')->default(0)->after('total_propria');

            // Setores de atividade
            $table->json('total_por_setor_propria')->nullable()->after('total_arrendada');
            $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            $table->dropColumn([
                'total_nucleo_urbano',
                'total_aldeia_anexa',
                'total_agroflorestal',
                'total_adultos',
                'total_criancas',
                'total_propria',
                'total_arrendada',
                'total_por_setor_propria',
                'total_por_setor_outrem'
            ]);
        });
    }
};