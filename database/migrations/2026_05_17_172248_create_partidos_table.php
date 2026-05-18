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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('equipo_local');
            $table->string('equipo_visitante');
            $table->dateTime('fecha_partido');
            $table->string('fase');
            $table->enum('estado', ['pendiente', 'finalizado'])->default('pendiente');
            $table->integer('resultado_local')->nullable();
            $table->integer('resultado_visitante')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
