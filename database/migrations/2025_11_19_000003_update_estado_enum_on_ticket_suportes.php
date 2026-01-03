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
        // Atualiza o enum 'estado' na tabela 'ticket_suportes' para incluir o novo estado 'respondido'
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','respondido','resolvido','fechado') DEFAULT 'em_processamento'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','resolvido','fechado') DEFAULT 'aberto'");
    }
};
