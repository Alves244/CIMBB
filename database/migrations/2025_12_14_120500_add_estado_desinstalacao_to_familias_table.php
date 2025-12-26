<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Adiciona colunas de estado e saída).
     */
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            
            // Define se a família ainda reside no local ('ativa') 
            // ou se já saiu/está inativa no sistema.
            $table->string('estado_acompanhamento', 30)->default('ativa')->after('ano_instalacao');
            
            // Data exata em que a família deixou a residência (se aplicável).
            $table->date('data_desinstalacao')->nullable()->after('estado_acompanhamento');
            
            // Ano da saída, útil para filtros rápidos em relatórios anuais de migração.
            $table->smallInteger('ano_desinstalacao')->nullable()->after('data_desinstalacao');
        });
    }

    /**
     * Reverse the migrations (Remove as colunas).
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            // Remove os campos de histórico de saída
            $table->dropColumn(['estado_acompanhamento', 'data_desinstalacao', 'ano_desinstalacao']);
        });
    }
};