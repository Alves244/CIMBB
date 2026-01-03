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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna 'agrupamento_id' à tabela 'users' com chave estrangeira para 'agrupamentos'
            if (! Schema::hasColumn('users', 'agrupamento_id')) {
                $table->foreignId('agrupamento_id')
                    ->nullable()
                    ->after('freguesia_id')
                    ->constrained('agrupamentos')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });

        // Atualiza o enum da coluna 'perfil' para incluir 'agrupamento'
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('freguesia','cimbb','admin','agrupamento') NOT NULL DEFAULT 'freguesia'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('freguesia','cimbb','admin') NOT NULL DEFAULT 'freguesia'");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'agrupamento_id')) {
                $table->dropForeign(['agrupamento_id']);
                $table->dropColumn('agrupamento_id');
            }
        });
    }
};
