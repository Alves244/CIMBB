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
        // Lista de setores baseada EXATAMENTE no inquérito PDF [cite: 625-637, 649-657]
        $setores = [
            ['nome' => 'Agricultura', 'descricao' => null, 'ativo' => true],
            ['nome' => 'Floresta e limpezas', 'descricao' => 'Atividades florestais e de limpeza de terrenos', 'ativo' => true],
            ['nome' => 'Turismo (Restauração e Alojamento)', 'descricao' => 'Hotelaria, restauração, alojamento local', 'ativo' => true],
            ['nome' => 'Construção civil', 'descricao' => null, 'ativo' => true],
            ['nome' => 'Serviço social', 'descricao' => 'Apoio social, cuidados a idosos/crianças', 'ativo' => true],
            ['nome' => 'Comércio', 'descricao' => 'Venda a retalho ou por grosso', 'ativo' => true],
            ['nome' => 'Indústria', 'descricao' => 'Transformação e produção', 'ativo' => true],
            ['nome' => 'Outra', 'descricao' => 'A especificar na descrição da atividade', 'ativo' => true],
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