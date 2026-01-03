<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('freguesias', function (Blueprint $table) {
            // Cria a tabela 'freguesias'
            $table->id();
            $table->string('nome', 100);
            $table->unsignedBigInteger('concelho_id');
            $table->string('codigo', 10)->nullable();
            $table->timestamps();
            $table->foreign('concelho_id')
                ->references('id')->on('concelhos')
                ->onDelete('cascade');
            $table->index('concelho_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freguesias');
    }
};