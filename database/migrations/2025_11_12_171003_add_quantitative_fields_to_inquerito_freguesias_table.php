<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Adiciona as colunas de totais).
     */
    public function up(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {

            // --- LOCALIZAÇÃO (Perguntas 11-13) ---
            // Regista o total de famílias em cada tipo de zona
            $table->integer('total_nucleo_urbano')->default(0)->after('ano');
            $table->integer('total_aldeia_anexa')->default(0)->after('total_nucleo_urbano');
            $table->integer('total_agroflorestal')->default(0)->after('total_aldeia_anexa');

            // --- DEMOGRAFIA (Pergunta 14) ---
            // Totais de indivíduos reportados pela freguesia
            $table->integer('total_adultos')->default(0)->after('total_agroflorestal');
            $table->integer('total_criancas')->default(0)->after('total_adultos');

            // --- HABITAÇÃO (Pergunta 15) ---
            // Totais de regime de propriedade
            $table->integer('total_propria')->default(0)->after('total_criancas');
            $table->integer('total_arrendada')->default(0)->after('total_propria');

            // --- ECONOMIA (Perguntas 16-19) ---
            // Usa o tipo JSON para guardar uma lista de setores e quantidades.
            // Ex: {"Agricultura": 5, "Construção": 2}
            $table->json('total_por_setor_propria')->nullable()->after('total_arrendada'); 
            $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria'); 
        });
    }

    /**
     * Reverse the migrations (Remove as colunas).
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            $table->dropColumn([
                'total_nucleo_urbano', 'total_aldeia_anexa', 'total_agroflorestal',
                'total_adultos', 'total_criancas', 'total_propria', 'total_arrendada',
                'total_por_setor_propria', 'total_por_setor_outrem'
            ]);
        });
    }
};