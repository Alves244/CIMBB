<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agrupamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo')->nullable();
            $table->foreignId('concelho_id')->constrained('concelhos')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agrupamentos');
    }
};
