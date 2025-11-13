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
            $table->id();
            $table->unsignedBigInteger('familia_id')->unique();

            // --- NOVOS CAMPOS DE GÉNERO (Perg. 14) ---
            $table->integer('adultos_laboral_m')->default(0)->comment('Adultos Idade Laboral - Masculino');
            $table->integer('adultos_laboral_f')->default(0)->comment('Adultos Idade Laboral - Feminino');
            $table->integer('adultos_laboral_n')->default(0)->comment('Adultos Idade Laboral - N/I (Não Informado)');
            
            $table->integer('adultos_65_mais_m')->default(0)->comment('Adultos 65+ - Masculino');
            $table->integer('adultos_65_mais_f')->default(0)->comment('Adultos 65+ - Feminino');
            $table->integer('adultos_65_mais_n')->default(0)->comment('Adultos 65+ - N/I (Não Informado)');
            
            $table->integer('criancas_m')->default(0)->comment('Crianças/Jovens - Masculino');
            $table->integer('criancas_f')->default(0)->comment('Crianças/Jovens - Feminino');
            $table->integer('criancas_n')->default(0)->comment('Crianças/Jovens - N/I (Não Informado)');

            // --- TOTAIS GERADOS AUTOMATICAMENTE PELA BD ---
            // (Estes são os campos que o seu código antigo usava)
            $table->integer('adultos_laboral')->storedAs('adultos_laboral_m + adultos_laboral_f + adultos_laboral_n');
            $table->integer('adultos_65_mais')->storedAs('adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n');
            $table->integer('criancas')->storedAs('criancas_m + criancas_f + criancas_n');
            $table->integer('total_membros')->storedAs('adultos_laboral + adultos_65_mais + criancas');

            // Chave estrangeira (como já tinha)
            $table->foreign('familia_id')
                  ->references('id')->on('familias')
                  ->onDelete('cascade');
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