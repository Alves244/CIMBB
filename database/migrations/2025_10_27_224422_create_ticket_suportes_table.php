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
        Schema::create('ticket_suportes', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 173, 476]
            $table->string('codigo', 20)->unique(); // codigo VARCHAR(20) UNIQUE NOT NULL [cite: 173, 477]
            $table->unsignedBigInteger('utilizador_id'); // utilizador_id INT NOT NULL [cite: 173, 478]
            $table->string('assunto', 200); // assunto VARCHAR(200) NOT NULL [cite: 173, 479]
            $table->text('descricao'); // descricao TEXT NOT NULL [cite: 173, 480]
            // data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP -> usar timestamps() [cite: 173, 481]
            $table->enum('estado', ['aberto', 'em_processamento', 'resolvido', 'fechado'])->default('aberto'); // estado ENUM(...) DEFAULT 'aberto' [cite: 173, 482]
            $table->text('resposta_admin')->nullable(); // resposta_admin TEXT NULL [cite: 173, 483]
            $table->dateTime('data_resposta')->nullable(); // data_resposta DATETIME NULL [cite: 173, 484]
            $table->unsignedBigInteger('administrador_id')->nullable(); // administrador_id INT NULL [cite: 173, 485]
            $table->string('anexo', 255)->nullable(); // anexo VARCHAR(255) NULL [cite: 173, 486]
            $table->enum('categoria', ['duvida', 'erro', 'sugestao', 'outro'])->default('duvida'); // categoria ENUM(...) DEFAULT 'duvida' [cite: 173, 487]
            $table->timestamps(); // Cria created_at (para data_criacao) e updated_at

            // Chave estrangeira para utilizador_id -> users.id (ON DELETE CASCADE) [cite: 173, 488]
            $table->foreign('utilizador_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Chave estrangeira para administrador_id -> users.id (ON DELETE SET NULL) [cite: 173, 489]
            $table->foreign('administrador_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // Índices [cite: 173, 490-493]
            $table->index('estado');
            // $table->index('prioridade'); // Nota: A coluna 'prioridade' não existe na definição da tabela. Removi o índice.
            $table->index('created_at'); // Índice na data de criação (created_at)
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_suportes');
    }
};