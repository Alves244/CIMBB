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

        // ***** ALTERAÇÃO AQUI *****
        // Usar firstOrCreate() em vez de create()
        foreach ($setores as $setor) {
            SetorAtividade::firstOrCreate(
                ['nome' => $setor['nome']], // 1. Procura por um setor com este nome
                [ // 2. Se não encontrar, cria com estes dados
                    'descricao' => $setor['descricao'],
                    'ativo' => $setor['ativo']
                ]
            );
        }
    }
}