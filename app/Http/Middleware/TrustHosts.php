<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

// Middleware responsável por validar e confiar apenas em domínios específicos para evitar ataques de Host Injection
class TrustHosts extends Middleware
{
    /**
     * Define os padrões de domínio que o portal deve considerar seguros e legítimos.
     * Crucial para a integridade da ferramenta quando alojada em servidores institucionais.
     */
    public function hosts()
    {
        return [
            // Confia automaticamente em todos os subdomínios derivados do URL principal da aplicação
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}