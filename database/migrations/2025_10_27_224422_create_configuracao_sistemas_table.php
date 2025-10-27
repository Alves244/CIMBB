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
        Schema::create('configuracao_sistemas', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 176, 498]
            $table->string('chave', 100)->unique(); // chave VARCHAR(100) UNIQUE NOT NULL [cite: 176, 499]
            $table->text('valor'); // valor TEXT NOT NULL [cite: 176, 500]
            $table->text('descricao')->nullable(); // descricao TEXT NULL [cite: 176, 501]
            // Nota: O tipo 'json' não é padrão SQL mas útil. Se preferir só SQL padrão, remova 'json'.
            $table->enum('tipo', ['string', 'int', 'boolean', 'json'])->default('string'); // tipo ENUM(...) DEFAULT 'string' [cite: 176, 502]
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP [cite: 176, 503]
            // Não precisa de created_at se só existe updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_sistemas');
    }
};