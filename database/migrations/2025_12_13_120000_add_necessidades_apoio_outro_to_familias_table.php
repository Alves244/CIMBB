<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            // Adiciona a coluna 'necessidades_apoio_outro' à tabela 'familias'
            $table->string('necessidades_apoio_outro', 255)->nullable()->after('necessidades_apoio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('necessidades_apoio_outro');
        });
    }
};
