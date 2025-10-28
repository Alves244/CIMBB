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
        Schema::create('freguesias', function (Blueprint $table) {
            $table->id(); // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('nome', 100); // VARCHAR(100) NOT NULL
            $table->unsignedBigInteger('conselho_id'); // INT NOT NULL (para chave estrangeira)
            $table->string('codigo', 10)->nullable(); // VARCHAR(10) NULL
            $table->timestamps(); // Cria created_at e updated_at

            // Definir a chave estrangeira
            $table->foreign('conselho_id')
                  ->references('id')->on('conselhos')
                  ->onDelete('cascade'); // ON DELETE CASCADE [cite: 403]

            // Adicionar índices (opcional mas bom para performance)
            $table->index('conselho_id'); // INDEX idx_freguesia_concelho [cite: 404]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freguesias');
    }
};