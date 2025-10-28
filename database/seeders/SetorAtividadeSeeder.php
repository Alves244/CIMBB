<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SetorAtividade;
use Illuminate\Support\Facades\DB;

class SetorAtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            ['nome' => 'Agricultura', 'descricao' => null, 'ativo' => true],
            ['nome' => 'Floresta e Limpezas', 'descricao' => 'Atividades florestais e de limpeza de terrenos', 'ativo' => true],
            ['nome' => 'Turismo (Restauração e Alojamento)', 'descricao' => 'Hotelaria, restauração, alojamento local', 'ativo' => true],
            ['nome' => 'Construção Civil', 'descricao' => null, 'ativo' => true],
            ['nome' => 'Serviço Social', 'descricao' => 'Apoio social, cuidados a idosos/crianças', 'ativo' => true],
            ['nome' => 'Comércio', 'descricao' => 'Venda a retalho ou por grosso', 'ativo' => true],
            ['nome' => 'Indústria', 'descricao' => 'Transformação e produção', 'ativo' => true],
            ['nome' => 'Serviços Diversos', 'descricao' => 'Serviços não classificados noutras categorias', 'ativo' => true],
            ['nome' => 'Outro', 'descricao' => 'A especificar na descrição da atividade', 'ativo' => true],
        ];

        // Inserir usando o Model
        foreach ($setores as $setor) {
            SetorAtividade::create($setor);
        }
        
        // Ou alternativamente usando DB Facade:
        // DB::table('setor_atividades')->insert($setores);
    }
}