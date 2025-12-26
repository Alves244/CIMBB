<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedJobsTable extends Migration
{
    /**
     * Executa as migrações.
     * Cria a tabela 'failed_jobs' para persistência de erros em filas assíncronas.
     */
    public function up()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            // Identificador numérico sequencial do registo de falha.
            $table->id();

            // Identificador Único Universal (UUID) da tarefa.
            // Permite rastrear um job específico através de logs ou interfaces de gestão,
            // garantindo que não há colisões de IDs entre diferentes servidores.
            $table->string('uuid')->unique();

            // Armazena qual a conexão usada (ex: 'database', 'redis', 'beanstalkd').
            $table->text('connection');

            // Armazena o nome da fila onde o job estava (ex: 'default', 'emails', 'relatorios').
            $table->text('queue');

            // Guarda todo o objeto do job serializado (os dados que estavam a ser processados).
            // Usa 'longText' para suportar grandes volumes de dados (JSON/Serialized PHP).
            $table->longText('payload');

            // Armazena o "Stack Trace" completo do erro.
            // Essencial para o programador perceber exatamente em que linha de código
            // e por que motivo a tarefa falhou (ex: Timeout, Erro de Sintaxe, API em baixo).
            $table->longText('exception');

            // Regista o momento exato da falha.
            // O método ->useCurrent() define automaticamente a hora do sistema de base de dados.
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverte as migrações.
     * Elimina a tabela de registo de falhas.
     */
    public function down()
    {
        Schema::dropIfExists('failed_jobs');
    }
}