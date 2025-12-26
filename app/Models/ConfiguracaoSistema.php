<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo responsável por armazenar parâmetros globais de funcionamento do sistema
class ConfiguracaoSistema extends Model
{
    use HasFactory;

    // Define a tabela que guarda pares de chave-valor (ex: 'ano_letivo_ativo', '2024')
    protected $table = 'configuracao_sistemas';

    /**
     * Otimização de Base de Dados.
     * Desativa a data de criação automática para tabelas de configuração estática.
     */
    const CREATED_AT = null;
    
    // Define os campos que podem ser atualizados via interface administrativa
    protected $fillable = [
        'chave',
        'valor',
        'descricao',
    ];
}