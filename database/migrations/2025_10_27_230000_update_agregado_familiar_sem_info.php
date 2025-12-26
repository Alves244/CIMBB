<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona novas colunas à tabela existente
        Schema::table('agregado_familiars', function (Blueprint $table) {
            // Regista pessoas cujo género ou idade não foram identificados
            $table->integer('membros_sem_informacao')->default(0)->after('criancas_n');
            // Regista o número de eleitores acompanhados (Pergunta do inquérito)
            $table->integer('eleitores_repenicados')->nullable()->after('membros_sem_informacao');
        });

        // 2. Transfere os dados das colunas antigas (Laboral N, 65+ N, Crianças N) 
        // para a nova coluna unificada 'membros_sem_informacao'
        DB::statement(<<<SQL
            UPDATE agregado_familiars
            SET membros_sem_informacao = COALESCE(adultos_laboral_n, 0) + COALESCE(adultos_65_mais_n, 0) + COALESCE(criancas_n, 0)
        SQL);

        // 3. Limpa os valores das colunas antigas após a transferência
        DB::statement('UPDATE agregado_familiars SET adultos_laboral_n = 0, adultos_65_mais_n = 0, criancas_n = 0');

        // 4. Recalcula as colunas geradas (Stored Columns)
        // Agora, os totais por faixa etária focam-se apenas em M/F, 
        // e o 'total_membros' inclui os sem informação.
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas + membros_sem_informacao) STORED');
    }

    public function down(): void
    {
        // Reverte as colunas calculadas para a lógica anterior (incluindo as colunas _n)
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f + adultos_laboral_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f + criancas_n) STORED');

        // Remove as colunas criadas nesta migração
        Schema::table('agregado_familiars', function (Blueprint $table) {
            $table->dropColumn(['eleitores_repenicados', 'membros_sem_informacao']);
        });
    }
};