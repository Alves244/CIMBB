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
        // Chamadas para os seeders individuais
        $this->call([
            ConcelhoSeeder::class,
            FreguesiaSeeder::class, 
            AgrupamentoSeeder::class,
            SetorAtividadeSeeder::class,
            UserSeeder::class,
            
        ]);
    }
}