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
        Schema::create('concelhos', function (Blueprint $table) {
            $table->id(); // Equivalente a INT AUTO_INCREMENT PRIMARY KEY
            $table->string('nome', 100)->unique(); // VARCHAR(100) UNIQUE NOT NULL
            $table->string('codigo', 10)->nullable(); // VARCHAR(10) NULL
            // Laravel adiciona automaticamente created_at e updated_at com timestamps()
            // Se precisares dos nomes exatos 'created' e 'updated' como no teu SQL[cite: 391, 392]:
            // $table->timestamp('created')->useCurrent();
            // $table->timestamp('updated')->useCurrent()->useCurrentOnUpdate();
            // Mas o padrão do Laravel é created_at/updated_at:
            $table->timestamps(); // Cria created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concelhos');
    }
};