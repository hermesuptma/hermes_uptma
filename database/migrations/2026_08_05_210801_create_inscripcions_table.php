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
    Schema::create('inscripcions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
        $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['estudiante_id', 'seccion_id']); // evita inscribir 2 veces en la misma sección
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcions');
    }
};
