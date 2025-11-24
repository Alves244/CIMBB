<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conselho;
use App\Models\Freguesia;

class FreguesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concelhos = config('concelhos', []);

        foreach ($concelhos as $concelho) {
            $conselhoModel = Conselho::where('codigo', $concelho['codigo'])
                ->orWhere('nome', $concelho['nome'])
                ->first();

            if (!$conselhoModel) {
                $this->command?->warn('Conselho não encontrado no seeder: ' . $concelho['nome']);
                continue;
            }

            foreach ($concelho['freguesias'] ?? [] as $freguesia) {
                Freguesia::updateOrCreate(
                    ['codigo' => $freguesia['codigo']],
                    [
                        'nome' => $freguesia['nome'],
                        'conselho_id' => $conselhoModel->id,
                    ]
                );
            }
        }
    }
}