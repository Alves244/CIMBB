<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Definição do estado padrão do modelo.
     * Este método retorna um array que mapeia as colunas da base de dados 
     * para geradores de dados aleatórios.
     */
    public function definition()
    {
        return [
            // Gera um nome aleatório utilizando a biblioteca 'Faker'.
            // Nota-se a adaptação para a coluna 'nome' específica deste projeto (o padrão Laravel é 'name').
            'nome' => $this->faker->name(), 
            
            // Gera um endereço de e-mail único e seguro (ex: não envia emails reais por engano).
            'email' => $this->faker->unique()->safeEmail(),
            
            // Define a data de verificação como "agora", assumindo que o utilizador gerado já está validado.
            'email_verified_at' => now(),
            
            // Define uma password estática já encriptada (hash bcrypt).
            // Isto é uma otimização de performance para testes, evitando que o sistema tenha 
            // de calcular o hash para cada utilizador falso gerado. A password real é "password".
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
            
            // Gera uma string aleatória para simular o token de "Lembrar-me" na sessão.
            'remember_token' => Str::random(10),
            
            // Atributos específicos do domínio CIMBB:
            // Define o perfil padrão como 'freguesia'.
            'perfil' => 'freguesia',
            
            // Inicializa as chaves estrangeiras como nulas. 
            // Em testes, estas seriam sobrescritas conforme necessário (ex: associar a uma Freguesia real).
            'freguesia_id' => null,
            'agrupamento_id' => null,
        ];
    }

    /**
     * Estado: Não Verificado.
     * Este método permite alterar o estado padrão definido acima. 
     * Ao chamar User::factory()->unverified(), o campo 'email_verified_at' 
     * será forçado a null, permitindo testar cenários de utilizadores que ainda não validaram o e-mail.
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}