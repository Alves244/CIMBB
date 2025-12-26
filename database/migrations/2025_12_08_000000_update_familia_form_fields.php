<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- 1. ATUALIZAÇÃO DA TABELA FAMILIAS ---
        Schema::table('familias', function (Blueprint $table) {
            // Novos campos de localização e estado da casa
            $table->string('localizacao_tipo', 50)->default('sede_freguesia')->after('tipologia_propriedade');
            $table->string('localizacao_detalhe')->nullable()->after('local_tipo');
            $table->string('condicao_alojamento', 30)->default('bom_estado')->after('localizacao_detalhe');
            
            // Novos campos de Integração Social (Saúde e Educação)
            $table->boolean('inscrito_centro_saude')->default(false)->after('condicao_alojamento');
            $table->string('inscrito_escola', 20)->nullable()->after('inscrito_centro_saude');
            
            // Campo JSON para múltiplas necessidades (ex: Apoio alimentar, vestuário)
            $table->json('necessidades_apoio')->nullable()->after('inscrito_escola');
            $table->text('observacoes')->nullable()->after('necessidades_apoio');
        });

        // Converte Enums para Varchar para permitir maior flexibilidade de valores
        DB::statement("ALTER TABLE familias MODIFY tipologia_habitacao VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE familias MODIFY tipologia_propriedade VARCHAR(50) NOT NULL");

        // Normalização de dados: Converte termos antigos para novos
        DB::table('familias')->where('tipologia_habitacao', 'casa')->update(['tipologia_habitacao' => 'moradia']);
        
        // Migra os dados da coluna antiga 'localizacao' para a nova 'localizacao_tipo'
        DB::table('familias')->update([
            'localizacao_tipo' => DB::raw("CASE localizacao 
                WHEN 'nucleo_urbano' THEN 'sede_freguesia' 
                WHEN 'aldeia_anexa' THEN 'lugar_aldeia' 
                WHEN 'espaco_agroflorestal' THEN 'espaco_agroflorestal' 
                ELSE 'sede_freguesia' END"),
        ]);

        // Remove a coluna de localização antiga que foi substituída
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('localizacao');
        });

        // --- 2. ATUALIZAÇÃO DO AGREGADO FAMILIAR ---
        Schema::table('agregado_familiars', function (Blueprint $table) {
            // Guarda a composição da família (ex: Casal com filhos, Monoparental) em JSON
            $table->json('estrutura_familiar')->nullable()->after('total_membros');
        });

        // --- 3. ATUALIZAÇÃO DAS ATIVIDADES ECONÓMICAS ---
        Schema::table('atividade_economicas', function (Blueprint $table) {
            $table->string('identificador', 20)->nullable()->after('familia_id'); // Ex: NIF ou ID Interno
            $table->string('vinculo', 20)->nullable()->after('tipo'); // Ex: Efetivo, Temporário
            $table->string('local_trabalho', 120)->nullable()->after('setor_id');
        });

        DB::statement("ALTER TABLE atividade_economicas MODIFY tipo VARCHAR(50) NOT NULL");
    }

    public function down(): void { /* ... código para reverter todas estas alterações ... */ }
};