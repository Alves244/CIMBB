<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            
            
            $table->enum('localizacao', ['nucleo_urbano', 'aldeia_anexa', 'espaco_agroflorestal'])
                  ->after('tipologia_propriedade')
                  ->comment('Localização da habitação (baseado no inquérito PDF Perg. 11-13)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('localizacao');
        });
    }
};