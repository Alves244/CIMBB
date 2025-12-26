<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

// Middleware que permite à aplicação confiar em servidores intermediários (proxies/load balancers)
class TrustProxies extends Middleware
{
    /**
     * Lista de endereços IP dos proxies em que a aplicação deve confiar.
     * Se estiver definido como null, o sistema pode ser configurado para confiar em todos (útil em certas clouds).
     * Garante a integridade da comunicação entre o servidor institucional e o utilizador final.
     */
    protected $proxies;

    /**
     * Define quais os cabeçalhos HTTP que devem ser utilizados para detetar informações do cliente.
     * Inclui protocolos, portas e IPs originais, essencial para o registo correto no AuditLogger.
     * Suporta padrões comuns e infraestruturas específicas como AWS ELB.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}