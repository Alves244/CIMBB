<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations (Atualiza para os novos setores).
     */
    public function up(): void
    {
        // 1. Definição da nova lista de setores atualizada
        $novosSetores = [
            ['nome' => 'Agricultura, silvicultura e pecuária', 'macro_grupo' => 'producao', 'descricao' => 'Trabalho agrícola, florestal ou com animais'],
            ['nome' => 'Indústria transformadora', 'macro_grupo' => 'producao', 'descricao' => 'Fábricas, transformação de bens e produção industrial'],
            ['nome' => 'Construção civil e obras públicas', 'macro_grupo' => 'producao', 'descricao' => 'Construção, manutenção e obras de engenharia'],
            ['nome' => 'Comércio e retalho', 'macro_grupo' => 'servicos', 'descricao' => 'Lojas, mercados e atendimento ao público'],
            ['nome' => 'Restauração, hotelaria e turismo', 'macro_grupo' => 'servicos', 'descricao' => 'Cafés, restaurantes, alojamento e atividades turísticas'],
            ['nome' => 'Serviços pessoais e sociais (inclui limpezas, cuidados de idosos, apoio doméstico)', 'macro_grupo' => 'servicos', 'descricao' => 'Limpezas, cuidadoras/es, apoio social ou doméstico'],
            ['nome' => 'Transportes e logística', 'macro_grupo' => 'servicos', 'descricao' => 'Motoristas, distribuição e entregas'],
            ['nome' => 'Administração, serviços técnicos ou trabalho de escritório', 'macro_grupo' => 'servicos', 'descricao' => 'Secretariado, técnicos administrativos e trabalho de escritório'],
            ['nome' => 'Outro (especificar)', 'macro_grupo' => 'servicos', 'descricao' => 'Quando não se enquadra nas categorias anteriores'],
        ];

        // 2. Insere ou atualiza cada setor na base de dados
        foreach ($novosSetores as $setor) {
            DB::table('setor_atividades')->updateOrInsert(
                ['nome' => $setor['nome']], // Procura pelo nome
                [
                    'descricao' => $setor['descricao'],
                    'macro_grupo' => $setor['macro_grupo'],
                    'ativo' => true, // Garante que estes estão visíveis
                ]
            );
        }

        // 3. Desativa os setores antigos que não estão na nova lista
        // Isto preserva o histórico mas esconde as opções obsoletas no formulário
        DB::table('setor_atividades')
            ->whereNotIn('nome', array_column($novosSetores, 'nome'))
            ->update(['ativo' => false]);
    }

    /**
     * Reverse the migrations (Reverte para a lista antiga).
     */
    public function down(): void { /* Reverte para os nomes e estados anteriores */ }
};