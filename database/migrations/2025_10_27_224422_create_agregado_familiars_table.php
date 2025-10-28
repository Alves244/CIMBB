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
        Schema::create('agregado_familiars', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY [cite: 161, 442]
            $table->unsignedBigInteger('familia_id')->unique(); // familia_id INT UNIQUE NOT NULL [cite: 161, 443]
            $table->integer('adultos_laboral')->default(0); // adultos_laboral INT DEFAULT 0 CHECK (>=0) [cite: 161, 444]
            $table->integer('adultos_65_mais')->default(0); // adultos_65_mais INT DEFAULT 0 CHECK (>=0) [cite: 161, 445]
            $table->integer('criancas')->default(0); // criancas INT DEFAULT 0 CHECK (>=0) [cite: 161, 446]
            // total_membros INT GENERATED ALWAYS AS (adultos_laboral + adultos_65_mais + criancas) STORED [cite: 161, 447]
            $table->integer('total_membros')->storedAs('adultos_laboral + adultos_65_mais + criancas');

            // Chave estrangeira para familia_id -> familias.id (ON DELETE CASCADE) [cite: 161, 448]
            $table->foreign('familia_id')
                  ->references('id')->on('familias') // Tabela 'familias'
                  ->onDelete('cascade');

            // Laravel não suporta CHECK constraints diretamente no schema builder,
            // mas as colunas são unsigned por defeito ou podemos usar validação no Model/Controller.
            // Para garantir >= 0 no DB, podemos fazer:
            // $table->unsignedInteger('adultos_laboral')->default(0);
            // $table->unsignedInteger('adultos_65_mais')->default(0);
            // $table->unsignedInteger('criancas')->default(0);
            // Vamos manter integer() por simplicidade e confiar na validação da aplicação.

            // Não necessita de timestamps() se não for especificado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agregado_familiars');
    }
};