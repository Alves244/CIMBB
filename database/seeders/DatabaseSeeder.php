<?php

namespace Database\Seeders;

// O uso de WithoutModelEvents pode ser ativado se quiseres ignorar observers durante o seed
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            // Cria os concelhos (ex: Castelo Branco, Idanha-a-Nova)
            ConcelhoSeeder::class,

            // Cria as freguesias (Depende de existir concelhos na BD)
            FreguesiaSeeder::class, 

            // Cria os agrupamentos de escolas e instituições (Depende de concelhos)
            AgrupamentoSeeder::class,

            // Configura os setores de atividade económica (ex: Agricultura, Serviços)
            // Nota: Importante correr apenas uma vez para não duplicar categorias
            SetorAtividadeSeeder::class, 

            // Cria os utilizadores e atribui-lhes perfis
            // (Depende de freguesias e agrupamentos para associar os users às entidades)
            UserSeeder::class,
        ]);

        // A limpeza de chamadas duplicadas evita erros de "Unique Constraint" 
        // e mantém a base de dados íntegra.
    }
}