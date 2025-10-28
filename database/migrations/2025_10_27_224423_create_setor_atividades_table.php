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
        Schema::create('setor_atividades', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 164, 453]
            $table->string('nome', 100)->unique(); // nome VARCHAR(100) NOT NULL UNIQUE [cite: 164, 454]
            $table->text('descricao')->nullable(); // descricao TEXT NULL [cite: 164, 455]
            $table->boolean('ativo')->default(true); // ativo BOOLEAN DEFAULT TRUE [cite: 164, 456]
            // Não necessita de timestamps() se não for especificado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setor_atividades');
    }
};