<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Atualização da tabela de utilizadores).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Adiciona a ligação ao Agrupamento de Escolas
            // nullable: Nem todos os users pertencem a escolas (ex: admins cimbb)
            // nullOnDelete: Se o agrupamento for apagado, o user mantém-se mas sem vínculo
            if (! Schema::hasColumn('users', 'agrupamento_id')) {
                $table->foreignId('agrupamento_id')
                    ->nullable()
                    ->after('freguesia_id')
                    ->constrained('agrupamentos')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });

        // 2. Expande as opções de perfil de utilizador
        // Adiciona 'agrupamento' aos perfis permitidos no sistema
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('freguesia','cimbb','admin','agrupamento') NOT NULL DEFAULT 'freguesia'");
    }

    /**
     * Reverse the migrations (Reverte as permissões e o vínculo).
     */
    public function down(): void
    {
        // Remove 'agrupamento' do ENUM (Cuidado: users com este perfil podem causar erro se não alterados antes)
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('freguesia','cimbb','admin') NOT NULL DEFAULT 'freguesia'");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'agrupamento_id')) {
                $table->dropForeign(['agrupamento_id']);
                $table->dropColumn('agrupamento_id');
            }
        });
    }
};