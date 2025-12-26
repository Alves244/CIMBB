<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Simplificação do modelo).
     */
    public function up(): void
    {
        // 1. Remove a chave estrangeira e a coluna da tabela de registos.
        // Isto é feito primeiro para evitar erros de violação de integridade.
        if (Schema::hasTable('inquerito_agrupamento_registos') && Schema::hasColumn('inquerito_agrupamento_registos', 'estabelecimento_id')) {
            Schema::table('inquerito_agrupamento_registos', function (Blueprint $table) {
                $table->dropForeign(['estabelecimento_id']);
                $table->dropColumn('estabelecimento_id');
            });
        }

        // 2. Elimina a tabela 'estabelecimentos_ensino'.
        // Agora que nenhuma tabela depende dela, pode ser removida com segurança.
        Schema::dropIfExists('estabelecimentos_ensino');
    }

    /**
     * Reverse the migrations (Recria a estrutura complexa).
     */
    public function down(): void
    {
        // Caso seja necessário reverter, recria a tabela de escolas.
        if (! Schema::hasTable('estabelecimentos_ensino')) {
            Schema::create('estabelecimentos_ensino', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agrupamento_id')->constrained('agrupamentos')->cascadeOnDelete();
                $table->foreignId('concelho_id')->nullable()->constrained('concelhos')->nullOnDelete();
                $table->string('nome');
                $table->string('codigo')->nullable();
                $table->timestamps();
            });
        }

        // Devolve a coluna 'estabelecimento_id' aos registos do inquérito escolar.
        if (Schema::hasTable('inquerito_agrupamento_registos') && ! Schema::hasColumn('inquerito_agrupamento_registos', 'estabelecimento_id')) {
            Schema::table('inquerito_agrupamento_registos', function (Blueprint $table) {
                $table->foreignId('estabelecimento_id')
                    ->nullable()
                    ->after('ano_letivo')
                    ->constrained('estabelecimentos_ensino')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }
    }
};