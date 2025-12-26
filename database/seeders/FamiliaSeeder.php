<?php

namespace Database\Seeders;

use App\Models\Familia;
use App\Models\Freguesia;
use App\Models\User;
use App\Models\SetorAtividade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamiliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obter dados necessários para as relações
        // Precisamos de um utilizador (técnico de freguesia) para ser o autor do registo
        $user = User::where('perfil', 'freguesia')->first();
        $freguesia = Freguesia::first();
        $setor = SetorAtividade::where('macro_grupo', 'producao')->first();

        if (!$user || !$freguesia) {
            return; // Evita erro se o UserSeeder ainda não tiver corrido
        }

        // 2. Criar uma Família de Exemplo
        $familia = Familia::create([
            'utilizador_id'         => $user->id,
            'freguesia_id'          => $freguesia->id,
            'ano_instalacao'        => 2023,
            'localizacao_tipo'      => 'sede_freguesia',
            'tipologia_habitacao'   => 'moradia',
            'tipologia_propriedade' => 'arrendada',
            'condicao_alojamento'   => 'bom_estado',
            'inscrito_centro_saude' => true,
            'estado_acompanhamento' => 'ativa',
            'necessidades_apoio'    => json_encode(['alimentar', 'saude']), // Exemplo de JSON
            'observacoes'           => 'Família integrada na comunidade local.',
        ]);

        // 3. Criar o Agregado Familiar associado
        // A tabela agregado_familiars usa colunas calculadas (STORED), 
        // por isso inserimos apenas os dados base (M/F).
        $familia->agregado()->create([
            'adultos_laboral_m'   => 1,
            'adultos_laboral_f'   => 1,
            'adultos_65_mais_m'   => 0,
            'adultos_65_mais_f'   => 0,
            'criancas_m'          => 1,
            'criancas_f'          => 1,
            'membros_sem_informacao' => 0,
            'estrutura_familiar'  => json_encode(['casal_com_filhos']),
        ]);

        // 4. Criar uma Atividade Económica para esta família
        $familia->atividadesEconomicas()->create([
            'setor_id'       => $setor->id,
            'tipo'           => 'conta_outrem',
            'vinculo'        => 'efetivo',
            'local_trabalho' => 'Zona Industrial de Castelo Branco',
        ]);
    }
}