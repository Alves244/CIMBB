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
        Schema::create('inquerito_anuals', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 170, 519]
            $table->unsignedBigInteger('familia_id'); // familia_id INT NOT NULL [cite: 170, 520]
            $table->integer('ano'); // ano INT NOT NULL [cite: 170, 521]
            $table->enum('situacao_emprego', ['empregado', 'desempregado', 'estudante', 'reformado'])->nullable(); // ENUM(...) NULLABLE [cite: 170, 522]
            $table->boolean('acesso_saude')->nullable(); // BOOLEAN NULL [cite: 170, 523]
            $table->boolean('acesso_educacao')->nullable(); // BOOLEAN NULL [cite: 170, 524]
            $table->timestamp('data_preenchimento')->useCurrent(); // DATETIME DEFAULT CURRENT_TIMESTAMP [cite: 170, 525]

            // Chave estrangeira para familia_id -> familias.id [cite: 170, 526]
            // Nota: O teu SQL não define ON DELETE, o padrão é RESTRICT.
            $table->foreign('familia_id')
                  ->references('id')->on('familias'); // Tabela 'familias'

            // Chave única composta [cite: 170, 527]
            $table->unique(['familia_id', 'ano'], 'unique_familia_ano');

            // Não precisa de timestamps() se data_preenchimento já existe
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_anuals');
    }
};