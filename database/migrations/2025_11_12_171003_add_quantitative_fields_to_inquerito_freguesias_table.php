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

            // --- Perguntas 11-13 (Localização) ---
            // (Guardamos o total de famílias, não o 'ano de instalação')
            $table->integer('total_nucleo_urbano')->default(0)->after('ano');
            $table->integer('total_aldeia_anexa')->default(0)->after('total_nucleo_urbano');
            $table->integer('total_agroflorestal')->default(0)->after('total_aldeia_anexa');

            // --- Pergunta 14 (Indivíduos) ---
            $table->integer('total_adultos')->default(0)->after('total_agroflorestal');
            $table->integer('total_criancas')->default(0)->after('total_adultos'); // (Embora a perg. 14 só peça adultos, é bom guardar ambos)

            // --- Pergunta 15 (Propriedade) ---
            $table->integer('total_propria')->default(0)->after('total_criancas');
            $table->integer('total_arrendada')->default(0)->after('total_propria');

            // --- Perguntas 16-19 (Sectores de Atividade) [cite: 440-459] ---
            // Vamos guardar o NÚMERO DE FAMÍLIAS (ou indivíduos, decida) em cada setor
            // Usamos 'json' para ser flexível, ou criamos colunas individuais. Vamos usar JSON.
            $table->json('total_por_setor_propria')->nullable()->after('total_arrendada'); // (Perg. 16-17)
            $table->json('total_por_setor_outrem')->nullable()->after('total_por_setor_propria'); // (Perg. 18-19)

            // Alterar colunas de opinião (Perg 20-24) [cite: 290-309] para estarem depois das novas colunas
            // (Não é estritamente necessário, mas organiza)
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