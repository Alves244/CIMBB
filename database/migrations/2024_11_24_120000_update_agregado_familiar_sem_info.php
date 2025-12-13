<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agregado_familiars', function (Blueprint $table) {
            $table->integer('membros_sem_informacao')->default(0)->after('criancas_n')->comment('Total de pessoas sem idade/género identificados');
            $table->integer('eleitores_repenicados')->nullable()->after('membros_sem_informacao')->comment('Número de eleitores acompanhados');
        });

        DB::statement(<<<SQL
            UPDATE agregado_familiars
            SET membros_sem_informacao = COALESCE(adultos_laboral_n, 0) + COALESCE(adultos_65_mais_n, 0) + COALESCE(criancas_n, 0)
        SQL);

        DB::statement('UPDATE agregado_familiars SET adultos_laboral_n = 0, adultos_65_mais_n = 0, criancas_n = 0');

        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas + membros_sem_informacao) STORED');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE agregado_familiars MODIFY total_membros INT AS (adultos_laboral + adultos_65_mais + criancas) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_laboral INT AS (adultos_laboral_m + adultos_laboral_f + adultos_laboral_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY adultos_65_mais INT AS (adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n) STORED');
        DB::statement('ALTER TABLE agregado_familiars MODIFY criancas INT AS (criancas_m + criancas_f + criancas_n) STORED');

        Schema::table('agregado_familiars', function (Blueprint $table) {
            $table->dropColumn(['eleitores_repenicados', 'membros_sem_informacao']);
        });
    }
};
