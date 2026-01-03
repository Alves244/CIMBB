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
        // remove chave estrangeira e coluna de estabelecimento_id de inquerito_agrupamento_registos
        if (Schema::hasTable('inquerito_agrupamento_registos') && Schema::hasColumn('inquerito_agrupamento_registos', 'estabelecimento_id')) {
            Schema::table('inquerito_agrupamento_registos', function (Blueprint $table) {
                $table->dropForeign(['estabelecimento_id']);
                $table->dropColumn('estabelecimento_id');
            });
        }

        // drop tabela estabelecimentos_ensino
        Schema::dropIfExists('estabelecimentos_ensino');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
