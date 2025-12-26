<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations (Permite valores nulos).
     */
    public function up(): void
    {
        // Altera a coluna para aceitar NULL. 
        // Isto é importante para quando o técnico não sabe se a família está inscrita ou não,
        // evitando assumir o valor '0' (Não) por defeito.
        DB::statement('ALTER TABLE familias MODIFY inscrito_centro_saude TINYINT(1) NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations (Volta a obrigar a um valor).
     */
    public function down(): void
    {
        // 1. Primeiro, converte todos os registos NULL para 0 (Não) para evitar erros de integridade.
        DB::statement('UPDATE familias SET inscrito_centro_saude = 0 WHERE inscrito_centro_saude IS NULL');
        
        // 2. Repõe a coluna como NOT NULL e volta a definir o padrão como 0.
        DB::statement('ALTER TABLE familias MODIFY inscrito_centro_saude TINYINT(1) NOT NULL DEFAULT 0');
    }
};