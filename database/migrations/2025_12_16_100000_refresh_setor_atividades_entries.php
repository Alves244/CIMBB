<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

        foreach ($novosSetores as $setor) {
            DB::table('setor_atividades')->updateOrInsert(
                ['nome' => $setor['nome']],
                [
                    'descricao' => $setor['descricao'],
                    'macro_grupo' => $setor['macro_grupo'],
                    'ativo' => true,
                ]
            );
        }

        DB::table('setor_atividades')
            ->whereNotIn('nome', array_column($novosSetores, 'nome'))
            ->update(['ativo' => false]);
    }

    public function down(): void
    {
        $antigosSetores = [
            ['nome' => 'Agricultura, Pecuária e Floresta', 'macro_grupo' => 'producao', 'descricao' => null],
            ['nome' => 'Indústria Transformadora (Fábricas)', 'macro_grupo' => 'producao', 'descricao' => 'Unidades fabris e transformação de bens'],
            ['nome' => 'Construção Civil e Obras', 'macro_grupo' => 'producao', 'descricao' => null],
            ['nome' => 'Pesca', 'macro_grupo' => 'producao', 'descricao' => null],
            ['nome' => 'Energia e Águas', 'macro_grupo' => 'producao', 'descricao' => 'Produção energética, saneamento e abastecimento'],
            ['nome' => 'Comércio (Lojas e Supermercados)', 'macro_grupo' => 'servicos', 'descricao' => null],
            ['nome' => 'Restauração e Hotelaria (Cafés, Restaurantes, Hotéis)', 'macro_grupo' => 'servicos', 'descricao' => null],
            ['nome' => 'Limpeza e Segurança', 'macro_grupo' => 'servicos', 'descricao' => null],
            ['nome' => 'Apoio Domiciliário e Ação Social', 'macro_grupo' => 'servicos', 'descricao' => 'Inclui IPSS, lares, cuidadores'],
            ['nome' => 'Transportes e Logística', 'macro_grupo' => 'servicos', 'descricao' => null],
            ['nome' => 'Educação e Saúde', 'macro_grupo' => 'servicos', 'descricao' => 'Creches, escolas, hospitais, clínicas'],
            ['nome' => 'Serviços Domésticos', 'macro_grupo' => 'servicos', 'descricao' => 'Empregados domésticos, cuidadores informais'],
            ['nome' => 'Serviços Administrativos e Escritórios', 'macro_grupo' => 'servicos', 'descricao' => 'Secretariado, receção, apoio administrativo'],
            ['nome' => 'Beleza e Bem-estar (Cabeleireiros, Estética)', 'macro_grupo' => 'servicos', 'descricao' => null],
        ];

        DB::table('setor_atividades')
            ->whereIn('nome', array_column($antigosSetores, 'nome'))
            ->update(['ativo' => true]);

        foreach ($antigosSetores as $setor) {
            DB::table('setor_atividades')->where('nome', $setor['nome'])->update([
                'descricao' => $setor['descricao'],
                'macro_grupo' => $setor['macro_grupo'],
            ]);
        }

        DB::table('setor_atividades')
            ->whereNotIn('nome', array_column($antigosSetores, 'nome'))
            ->update(['ativo' => false]);
    }
};
