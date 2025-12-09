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
        $setores = [
            // Produção, Construção e Agricultura
            ['nome' => 'Agricultura, Pecuária e Floresta', 'descricao' => null, 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Indústria Transformadora (Fábricas)', 'descricao' => 'Unidades fabris e transformação de bens', 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Construção Civil e Obras', 'descricao' => null, 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Pesca', 'descricao' => null, 'macro_grupo' => 'producao', 'ativo' => true],
            ['nome' => 'Energia e Águas', 'descricao' => 'Produção energética, saneamento e abastecimento', 'macro_grupo' => 'producao', 'ativo' => true],

            // Serviços, Comércio e Turismo
            ['nome' => 'Comércio (Lojas e Supermercados)', 'descricao' => null, 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Restauração e Hotelaria (Cafés, Restaurantes, Hotéis)', 'descricao' => null, 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Limpeza e Segurança', 'descricao' => null, 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Apoio Domiciliário e Ação Social', 'descricao' => 'Inclui IPSS, lares, cuidadores', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Transportes e Logística', 'descricao' => null, 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Educação e Saúde', 'descricao' => 'Creches, escolas, hospitais, clínicas', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Serviços Domésticos', 'descricao' => 'Empregados domésticos, cuidadores informais', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Serviços Administrativos e Escritórios', 'descricao' => 'Secretariado, receção, apoio administrativo', 'macro_grupo' => 'servicos', 'ativo' => true],
            ['nome' => 'Beleza e Bem-estar (Cabeleireiros, Estética)', 'descricao' => null, 'macro_grupo' => 'servicos', 'ativo' => true],
        ];

        // Limpa a tabela antes de inserir (Necessário se não usar migrate:fresh)
        // Para evitar o erro de foreign key, desativamos as verificações
        Schema::disableForeignKeyConstraints();
        DB::table('setor_atividades')->truncate();
        Schema::enableForeignKeyConstraints();
        
        // Insere os setores corretos
        foreach ($setores as $setor) {
            SetorAtividade::create($setor);
        }
    }
}