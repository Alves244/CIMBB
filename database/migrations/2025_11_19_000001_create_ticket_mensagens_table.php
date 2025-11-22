<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('ticket_suportes')
                ->onDelete('cascade');
            $table->foreignId('autor_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('mensagem');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_mensagens');
    }
};
