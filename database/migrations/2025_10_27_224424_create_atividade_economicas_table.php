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
        Schema::create('atividade_economicas', function (Blueprint $table) {
            // ID único do registo de atividade
            $table->id(); 

            // ID da família associada (Chave Estrangeira)
            $table->unsignedBigInteger('familia_id'); 

            // Define se trabalham por conta própria ou para terceiros (Enum)
            $table->enum('tipo', ['conta_propria', 'conta_outrem']); 

            // ID do setor de atividade (Chave Estrangeira que liga ao catálogo de setores)
            $table->unsignedBigInteger('setor_id'); 

            // Detalhes adicionais sobre a profissão ou local de trabalho
            $table->text('descricao')->nullable(); 

            // Regista automaticamente a data do lançamento (created_at) e edição (updated_at)
            $table->timestamps(); 

            // --- RELAÇÕES (Chaves Estrangeiras) ---

            // Se a família for apagada, as suas atividades económicas também são removidas (cascade)
            $table->foreign('familia_id')
                  ->references('id')->on('familias')
                  ->onDelete('cascade');

            // Impede apagar um setor (ex: Agricultura) se existirem famílias ligadas a ele (restrict)
            $table->foreign('setor_id')
                  ->references('id')->on('setor_atividades')
                  ->onDelete('restrict');

            // --- PERFORMANCE ---
            // Índices para acelerar a filtragem de dados nos relatórios por família ou regime
            $table->index('familia_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations (Eliminação da tabela).
     */
    public function down(): void
    {
        Schema::dropIfExists('atividade_economicas');
    }
};