<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chamar os seeders pela ordem de dependência
        $this->call([
            ConselhoSeeder::class,
            FreguesiaSeeder::class, 
            SetorAtividadeSeeder::class, // Chamado SÓ UMA VEZ
            UserSeeder::class,
            // A chamada duplicada foi removida
        ]);
    }
}