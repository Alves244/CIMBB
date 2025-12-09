<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setor_atividades', function (Blueprint $table) {
            $table->string('macro_grupo', 40)->default('producao')->after('nome');
        });

        DB::table('setor_atividades')->whereIn('nome', [
            'Agricultura',
            'Floresta e limpezas',
            'Construção civil',
            'Indústria',
        ])->update(['macro_grupo' => 'producao']);

        DB::table('setor_atividades')->whereIn('nome', [
            'Turismo (Restauração e Alojamento)',
            'Serviço social',
            'Comércio',
            'Outra',
        ])->update(['macro_grupo' => 'servicos']);
    }

    public function down(): void
    {
        Schema::table('setor_atividades', function (Blueprint $table) {
            $table->dropColumn('macro_grupo');
        });
    }
};
