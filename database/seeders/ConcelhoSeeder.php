<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Concelho;

class ConcelhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concelhos = config('concelhos', []);

        foreach ($concelhos as $concelho) {
            Concelho::updateOrCreate(
                ['nome' => $concelho['nome']],
                ['codigo' => $concelho['codigo']]
            );
        }
    }
}
