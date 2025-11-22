<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','respondido','resolvido','fechado') DEFAULT 'em_processamento'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ticket_suportes MODIFY estado ENUM('aberto','em_processamento','resolvido','fechado') DEFAULT 'aberto'");
    }
};
