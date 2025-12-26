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
        Schema::table('familias', function (Blueprint $table) {
            
            // Cria um campo de escolha fixa (Enum) para a localização
            // nucleo_urbano: Vilas/Cidades | aldeia_anexa: Aldeias | agroflorestal: Quintas/Montes
            $table->enum('localizacao', ['nucleo_urbano', 'aldeia_anexa', 'espaco_agroflorestal'])
                  ->after('tipologia_propriedade') // Posiciona a coluna após a tipologia
                  ->comment('Localização da habitação (baseado no inquérito PDF Perg. 11-13)');
        });
    }

    /**
     * Reverse the migrations (Remove a coluna).
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            // Apaga a coluna criada se a migração for revertida
            $table->dropColumn('localizacao');
        });
    }
};