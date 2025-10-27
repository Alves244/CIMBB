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
        Schema::create('atividade_economicas', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 167, 461]
            $table->unsignedBigInteger('familia_id'); // familia_id INT NOT NULL [cite: 167, 462]
            $table->enum('tipo', ['conta_propria', 'conta_outrem']); // tipo ENUM('conta_propria', 'conta_outrem') NOT NULL [cite: 167, 463]
            $table->unsignedBigInteger('setor_id'); // setor_id INT NOT NULL [cite: 167, 464]
            $table->text('descricao')->nullable(); // descricao TEXT NULL [cite: 167, 465]
            $table->timestamps(); // Usa created_at para data_registo [cite: 167, 466]

            // Chave estrangeira para familia_id -> familias.id (ON DELETE CASCADE) [cite: 167, 467]
            $table->foreign('familia_id')
                  ->references('id')->on('familias')
                  ->onDelete('cascade');

            // Chave estrangeira para setor_id -> setor_atividades.id (ON DELETE RESTRICT) [cite: 167, 468]
            $table->foreign('setor_id')
                  ->references('id')->on('setor_atividades')
                  ->onDelete('restrict');

            // Índices [cite: 167, 469-470]
            $table->index('familia_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividade_economicas');
    }
};