<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations (Aplica as alterações no ENUM).
     */
    public function up(): void
    {
        // Adiciona o estado 'respondido' à lista de opções.
        // Altera o padrão (default) para 'em_processamento' em vez de 'aberto'.
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','respondido','resolvido','fechado') DEFAULT 'em_processamento'");
    }

    /**
     * Reverse the migrations (Reverte para a estrutura original).
     */
    public function down(): void
    {
        // Remove o estado 'respondido' e volta a colocar o padrão como 'aberto'.
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','resolvido','fechado') DEFAULT 'aberto'");
    }
};