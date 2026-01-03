<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tornar a coluna 'inscrito_centro_saude' nullable na tabela 'familias'
        DB::statement('ALTER TABLE familias MODIFY inscrito_centro_saude TINYINT(1) NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('UPDATE familias SET inscrito_centro_saude = 0 WHERE inscrito_centro_saude IS NULL');
        DB::statement('ALTER TABLE familias MODIFY inscrito_centro_saude TINYINT(1) NOT NULL DEFAULT 0');
    }
};
