<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SetorAtividade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetorAtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define os setores de atividade a serem inseridos
        $setores = [
            ['nome' => 'Agricultura, silvicultura e pecuária', 'descricao' => 'Trabalho agrícola, florestal ou com animais', 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Indústria transformadora', 'descricao' => 'Fábricas, transformação de bens e produção industrial', 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Construção civil e obras públicas', 'descricao' => 'Construção, manutenção e obras de engenharia', 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Comércio e retalho', 'descricao' => 'Lojas, mercados, vendas e atendimento ao público', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Restauração, hotelaria e turismo', 'descricao' => 'Cafés, restaurantes, alojamento e atividades turísticas', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Serviços pessoais e sociais (inclui limpezas, cuidados de idosos, apoio doméstico)', 'descricao' => 'Limpezas, cuidadoras/es, apoio social ou doméstico', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Transportes e logística', 'descricao' => 'Motoristas, distribuição, armazenagem e entregas', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Administração, serviços técnicos ou trabalho de escritório', 'descricao' => 'Secretariado, técnicos administrativos e trabalho de escritório', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Outro (especificar)', 'descricao' => 'Utilize quando não se enquadra nas categorias anteriores', 'macro_grupo' => 'servicos', 'ativo' => true],
        ];

        // Limpa a tabela antes de inserir os novos dados
        Schema::disableForeignKeyConstraints();
        DB::table('setor_atividades')->truncate();
        Schema::enableForeignKeyConstraints();
        
        // Insere os setores corretos
        foreach ($setores as $setor) {
            SetorAtividade::create($setor);
        }
    }
}