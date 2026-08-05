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
    Schema::create('secciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
        $table->foreignId('profesor_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('periodo_academico_id')->constrained('periodo_academicos')->cascadeOnDelete();
        $table->string('nombre_seccion'); // ej. "Sección B"
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seccions');
    }
};
