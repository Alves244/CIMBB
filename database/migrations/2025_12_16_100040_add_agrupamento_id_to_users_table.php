<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'agrupamento_id')) {
                $table->foreignId('agrupamento_id')
                    ->nullable()
                    ->after('freguesia_id')
                    ->constrained('agrupamentos')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });

        DB::statement("ALTER TABLE users MODIFY perfil ENUM('freguesia','cimbb','admin','agrupamento') NOT NULL DEFAULT 'freguesia'");
    }

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
