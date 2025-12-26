<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Adiciona a coluna).
     */
    public function up(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            
            // Adiciona um campo para guardar o total de indivíduos que trabalham
            // por conta de outrem (assalariados), conforme reportado pela freguesia.
            // O campo é inserido após os dados detalhados por setor.
            $table->integer('total_trabalhadores_outrem')
                  ->default(0)
                  ->after('total_por_setor_outrem');
        });
    }

    /**
     * Reverse the migrations (Remove a coluna).
     */
    public function down(): void
    {
        Schema::table('inquerito_freguesias', function (Blueprint $table) {
            // Remove a coluna se a migração for revertida
            $table->dropColumn('total_trabalhadores_outrem');
        });
    }
};