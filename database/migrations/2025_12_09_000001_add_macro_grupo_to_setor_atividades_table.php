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
        // 1. Adiciona a coluna 'macro_grupo' para agrupar setores semelhantes
        Schema::table('setor_atividades', function (Blueprint $table) {
            $table->string('macro_grupo', 40)->default('producao')->after('nome');
        });

        // 2. Classifica setores no grupo de 'producao' (Setores primário e secundário)
        DB::table('setor_atividades')->whereIn('nome', [
            'Agricultura',
            'Floresta e limpezas',
            'Construção civil',
            'Indústria',
        ])->update(['macro_grupo' => 'producao']);

        // 3. Classifica setores no grupo de 'servicos' (Setor terciário)
        DB::table('setor_atividades')->whereIn('nome', [
            'Turismo (Restauração e Alojamento)',
            'Serviço social',
            'Comércio',
            'Outra',
        ])->update(['macro_grupo' => 'servicos']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setor_atividades', function (Blueprint $table) {
            // Remove a coluna de agrupamento
            $table->dropColumn('macro_grupo');
        });
    }
};