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
        Schema::create('log_acessos', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 179, 508]
            $table->unsignedBigInteger('utilizador_id'); // utilizador_id INT NOT NULL [cite: 179, 509]
            $table->string('acao', 100); // acao VARCHAR(100) NOT NULL [cite: 179, 510]
            $table->timestamp('data_hora')->useCurrent(); // data_hora DATETIME DEFAULT CURRENT_TIMESTAMP [cite: 179, 511]
            $table->string('ip', 45)->nullable(); // ip VARCHAR(45) NULL [cite: 179, 512]
            $table->text('descricao')->nullable(); // descricao TEXT NULL [cite: 179, 513]

            // Chave estrangeira para utilizador_id -> users.id [cite: 179, 514]
            // Nota: O teu SQL não define ON DELETE. O padrão é RESTRICT. Considera 'cascade' ou 'set null'.
            $table->foreign('utilizador_id')
                  ->references('id')->on('users'); // Tabela 'users'

            // Não precisa de timestamps() se data_hora já existe
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_acessos');
    }
};