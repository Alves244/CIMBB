<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agregado_familiars', function (Blueprint $table) {
            // Adiciona novas colunas para membros sem informação e eleitores repenicados
            $table->integer('membros_sem_informacao')->default(0)->after('criancas_n')->comment('Total de pessoas sem idade/género identificados');
            $table->integer('eleitores_repenicados')->nullable()->after('membros_sem_informacao')->comment('Número de eleitores acompanhados');
        });

        // Atualiza a coluna membros_sem_informacao com a soma dos membros N/I existentes
        DB::statement(<<<SQL
            UPDATE agregado_familiars
            SET membros_sem_informacao = COALESCE(adultos_laboral_n, 0) + COALESCE(adultos_65_mais_n, 0) + COALESCE(criancas_n, 0)
        SQL);

        // Zera os campos N/I para evitar contagem dupla
        DB::statement('UPDATE agregado_familiars SET adultos_laboral_n = 0, adultos_65_mais_n = 0, criancas_n = 0');

        // Atualiza as colunas calculadas para refletir as mudanças
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas + membros_sem_informacao) STORED');
    }


    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        // Restaura as colunas calculadas para o estado original
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f + adultos_laboral_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f + criancas_n) STORED');

        Schema::table('agregado_familiars', function (Blueprint $table) {
            // Remove as colunas adicionadas
            $table->dropColumn(['eleitores_repenicados', 'membros_sem_informacao']);
        });
    }
};
