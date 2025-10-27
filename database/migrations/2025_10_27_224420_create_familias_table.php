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
        // O nome da tabela será 'familias' (plural, por convenção do Laravel)
        Schema::create('familias', function (Blueprint $table) {
            // Coluna 'id': INT AUTO_INCREMENT PRIMARY KEY [cite: 160, 670]
            $table->id();

            // Coluna 'codigo': VARCHAR(50) UNIQUE NOT NULL [cite: 160, 671]
            $table->string('codigo', 50)->unique();

            // Coluna 'ano_instalacao': INT NOT NULL [cite: 160, 672]
            $table->integer('ano_instalacao');

            // Coluna 'nacionalidade': VARCHAR(50) NOT NULL [cite: 160, 673]
            $table->string('nacionalidade', 50);

            // Coluna 'freguesia_id': INT NOT NULL (Chave estrangeira) [cite: 160, 674]
            $table->unsignedBigInteger('freguesia_id'); // Tipo correto para referenciar IDs

            // Coluna 'tipologia_habitacao': ENUM('casa', 'quinta', 'apartamento') NOT NULL [cite: 160, 675]
            // (Nota: o teu doc SQL tem 'quinta' [cite: 675], o modelo ER tem 'monte' [cite: 60], o formulário word tem 'Quinta' [cite: 203] - usei 'quinta' do SQL)
            $table->enum('tipologia_habitacao', ['casa', 'quinta', 'apartamento']);

            // Coluna 'tipologia_propriedade': ENUM('propria', 'arrendada') NOT NULL [cite: 160, 676]
            $table->enum('tipologia_propriedade', ['propria', 'arrendada']);

            // Coluna 'data_registo': DATETIME DEFAULT CURRENT_TIMESTAMP [cite: 160, 677]
            // O Laravel trata isto automaticamente com timestamps(), que cria 'created_at' e 'updated_at'.
            // Se quiseres EXATAMENTE 'data_registo':
            // $table->timestamp('data_registo')->useCurrent();
            // Vamos usar o padrão Laravel por agora:
            $table->timestamps(); // Cria created_at (para data_registo) e updated_at

            // Coluna 'utilizador_registo_id': INT NOT NULL (Chave estrangeira) [cite: 160, 678]
            // (Refere-se à tabela 'users', não 'utilizador')
            $table->unsignedBigInteger('utilizador_registo_id');

            // --- Definir Chaves Estrangeiras ---

            // FK para freguesia_id -> freguesias.id (ON DELETE CASCADE) [cite: 679]
            $table->foreign('freguesia_id')
                  ->references('id')->on('freguesias') // Nome da tabela 'freguesias'
                  ->onDelete('cascade');

            // FK para utilizador_registo_id -> users.id (ON DELETE RESTRICT) [cite: 680]
            $table->foreign('utilizador_registo_id')
                  ->references('id')->on('users') // Nome da tabela 'users'
                  ->onDelete('restrict');

            // --- Adicionar Índices (Opcional, mas recomendado) --- [cite: 681-683]
            $table->index('freguesia_id');
            $table->index('ano_instalacao');
            $table->index('nacionalidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};