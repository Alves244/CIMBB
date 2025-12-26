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
        // 1. Remove a ligação (chave estrangeira) na tabela de registos
        if (Schema::hasTable('inquerito_agrupamento_registos') && Schema::hasColumn('inquerito_agrupamento_registos', 'estabelecimento_id')) {
            Schema::table('inquerito_agrupamento_registos', function (Blueprint $table) {
                $table->dropForeign(['estabelecimento_id']);
                $table->dropColumn('estabelecimento_id');
            });
        }

        // 2. Elimina a tabela de escolas individuais (Estabelecimentos)
        Schema::dropIfExists('estabelecimentos_ensino');
    }

    /**
     * Reverse the migrations (Recria a estrutura complexa).
     */
    public function down(): void
    {
        // Recria a tabela de estabelecimentos caso seja necessário voltar atrás
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

        // Repõe a coluna de ligação nos registos do inquérito
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