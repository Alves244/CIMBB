<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Conselho;
use App\Models\Freguesia;

class FreguesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar a tabela antes de inserir
        // DB::table('freguesias')->delete();

        // Função auxiliar para inserir freguesias de um concelho
        $insertFreguesias = function($nomeConselho, $freguesias) {
            $conselho = Conselho::where('nome', $nomeConselho)->first();
            if ($conselho) {
                foreach ($freguesias as $nomeFreguesia) {
                    Freguesia::firstOrCreate(
                        ['nome' => $nomeFreguesia, 'conselho_id' => $conselho->id],
                        ['codigo' => null]
                    );
                }
            } else {
                $this->command->warn("Conselho não encontrado no seeder: " . $nomeConselho);
            }
        };

        // Inserir para cada Concelho (REMOVA OS [cite_start])
        $insertFreguesias('Penamacor', [
            'Aranhas', 'Benquerença', 'Meimão', 'Meimoa', 'Penamacor', 'Salvador',
            'Aldeia do Bispo, Águas e Aldeia de João Pires', 'Pedrógão de São Pedro e Bemposta',
            'Vale da Senhora da Póvoa'
        ]);

        $insertFreguesias('Oleiros', [
            'Álvaro', 'Cambas', 'Estreito', 'Isna', 'Madeirã', 'Mosteiro', 'Oleiros',
            'Orvalho', 'Sarnadas de São Simão', 'Sobral'
        ]);

        $insertFreguesias('Sertã', [
            'Cabeçudo', 'Carvalhal', 'Castelo', 'Pedrógão Pequeno', 'Sertã', 'Troviscal',
            'Várzea dos Cavaleiros', 'Cernache do Bonjardim, Nesperal e Palhais',
            'Cumeada e Marmeleiro', 'Ermida e Figueiredo'
        ]);

        $insertFreguesias('Vila de Rei', [
            'Fundada', 'São João do Peso', 'Vila de Rei'
        ]);

        $insertFreguesias('Proença-a-Nova', [
            'Montes da Senhora', 'Proença-a-Nova e Peral', 'São Pedro do Esteval',
            'Sobreira Formosa e Alvito da Beira'
        ]);

        $insertFreguesias('Vila Velha de Ródão', [
            'Fratel', 'Perais', 'Sarnadas de Ródão', 'Vila Velha de Ródão'
        ]);

        $insertFreguesias('Castelo Branco', [
            'Alcains', 'Almaceda', 'Benquerenças', 'Cebolais de Cima e Retaxo', 'Castelo Branco',
            'Escalos de Baixo e Mata', 'Escalos de Cima e Lousa', 'Freixial do Campo e Juncal do Campo',
            'Lardosa', 'Louriçal do Campo', 'Malpica do Tejo', 'Monforte da Beira',
            'Ninho do Açor e Sobral do Campo', 'Póvoa de Rio de Moinhos e Cafede',
            'Salgueiro do Campo', 'Santo André das Tojeiras', 'São Vicente da Beira', 'Sarzedas', 'Tinalhas'
        ]);

        $insertFreguesias('Idanha-a-Nova', [
            'Idanha-a-Nova', 'Aldeia de Santa Margarida', 'Ladoeiro', 'Medelim', 'Monsanto e Idanha-a-Velha',
            'Oledo', 'Penha Garcia', 'Proença-a-Velha', 'Rosmaninhal', 'São Miguel de Acha',
            'Segura', 'Toulões', 'Zebreira e Benquerença'
        ]);
    }
}