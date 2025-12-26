<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Criação da tabela).
     */
    public function up(): void
    {
        Schema::create('agregado_familiars', function (Blueprint $table) {
            // ID único do registo
            $table->id();

            // ID da família (um agregado por família). 
            // 'unique' garante que não existam dois agregados para a mesma família.
            $table->unsignedBigInteger('familia_id')->unique();

            // --- CAMPOS DE CONTAGEM POR GÉNERO E IDADE ---
            // Adultos em idade ativa (Masculino, Feminino, Não Informado)
            $table->integer('adultos_laboral_m')->default(0);
            $table->integer('adultos_laboral_f')->default(0);
            $table->integer('adultos_laboral_n')->default(0);
            
            // Adultos com 65 ou mais anos
            $table->integer('adultos_65_mais_m')->default(0);
            $table->integer('adultos_65_mais_f')->default(0);
            $table->integer('adultos_65_mais_n')->default(0);
            
            // Crianças e Jovens
            $table->integer('criancas_m')->default(0);
            $table->integer('criancas_f')->default(0);
            $table->integer('criancas_n')->default(0);

            // --- TOTAIS CALCULADOS PELA BASE DE DADOS (Stored Columns) ---
            // 'storedAs' faz a soma automática no SQL sem precisar de código PHP.
            $table->integer('adultos_laboral')->storedAs('adultos_laboral_m + adultos_laboral_f + adultos_laboral_n');
            $table->integer('adultos_65_mais')->storedAs('adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n');
            $table->integer('criancas')->storedAs('criancas_m + criancas_f + criancas_n');
            
            // Soma total de todos os membros da casa
            $table->integer('total_membros')->storedAs('adultos_laboral + adultos_65_mais + criancas');

            // Chave Estrangeira: liga à tabela 'familias'.
            // 'onDelete cascade': se a família for apagada, o agregado desaparece também.
            $table->foreign('familia_id')
                  ->references('id')->on('familias')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('agregado_familiars');
    }
};