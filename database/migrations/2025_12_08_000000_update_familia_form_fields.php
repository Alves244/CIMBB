<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->string('localizacao_tipo', 50)->default('sede_freguesia')->after('tipologia_propriedade');
            $table->string('localizacao_detalhe')->nullable()->after('localizacao_tipo');
            $table->string('condicao_alojamento', 30)->default('bom_estado')->after('localizacao_detalhe');
            $table->boolean('inscrito_centro_saude')->default(false)->after('condicao_alojamento');
            $table->string('inscrito_escola', 20)->nullable()->after('inscrito_centro_saude');
            $table->json('necessidades_apoio')->nullable()->after('inscrito_escola');
            $table->text('observacoes')->nullable()->after('necessidades_apoio');
        });

        DB::statement("ALTER TABLE familias MODIFY tipologia_habitacao VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE familias MODIFY tipologia_propriedade VARCHAR(50) NOT NULL");

        DB::table('familias')->where('tipologia_habitacao', 'casa')->update(['tipologia_habitacao' => 'moradia']);
        DB::table('familias')->where('tipologia_habitacao', 'quinta')->update(['tipologia_habitacao' => 'outro']);

        DB::table('familias')->update([
            'localizacao_tipo' => DB::raw("CASE localizacao WHEN 'nucleo_urbano' THEN 'sede_freguesia' WHEN 'aldeia_anexa' THEN 'lugar_aldeia' WHEN 'espaco_agroflorestal' THEN 'espaco_agroflorestal' ELSE 'sede_freguesia' END"),
        ]);

        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('localizacao');
        });

        Schema::table('agregado_familiars', function (Blueprint $table) {
            $table->json('estrutura_familiar')->nullable()->after('total_membros');
        });

        Schema::table('atividade_economicas', function (Blueprint $table) {
            $table->string('identificador', 20)->nullable()->after('familia_id');
            $table->string('vinculo', 20)->nullable()->after('tipo');
            $table->string('local_trabalho', 120)->nullable()->after('setor_id');
        });

        DB::statement("ALTER TABLE atividade_economicas MODIFY tipo VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->enum('localizacao', ['nucleo_urbano', 'aldeia_anexa', 'espaco_agroflorestal'])->default('nucleo_urbano')->after('tipologia_propriedade');
        });

        DB::table('familias')->update([
            'localizacao' => DB::raw("CASE localizacao_tipo WHEN 'lugar_aldeia' THEN 'aldeia_anexa' WHEN 'espaco_agroflorestal' THEN 'espaco_agroflorestal' ELSE 'nucleo_urbano' END"),
        ]);

        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn([
                'localizacao_tipo',
                'localizacao_detalhe',
                'condicao_alojamento',
                'inscrito_centro_saude',
                'inscrito_escola',
                'necessidades_apoio',
                'observacoes',
            ]);
        });

        DB::table('familias')->where('tipologia_habitacao', 'moradia')->update(['tipologia_habitacao' => 'casa']);
        DB::table('familias')->whereIn('tipologia_habitacao', ['caravana_tenda', 'anexo', 'outro'])->update(['tipologia_habitacao' => 'quinta']);
        DB::table('familias')->where('tipologia_propriedade', 'cedida')->update(['tipologia_propriedade' => 'arrendada']);
        DB::table('familias')->where('tipologia_propriedade', 'outra')->update(['tipologia_propriedade' => 'arrendada']);

        DB::statement("ALTER TABLE familias MODIFY tipologia_habitacao ENUM('casa','quinta','apartamento') NOT NULL");
        DB::statement("ALTER TABLE familias MODIFY tipologia_propriedade ENUM('propria','arrendada') NOT NULL");

        Schema::table('agregado_familiars', function (Blueprint $table) {
            $table->dropColumn('estrutura_familiar');
        });

        Schema::table('atividade_economicas', function (Blueprint $table) {
            $table->dropColumn(['identificador', 'vinculo', 'local_trabalho']);
        });

        DB::statement("ALTER TABLE atividade_economicas MODIFY tipo ENUM('conta_propria','conta_outrem') NOT NULL");
    }
};
