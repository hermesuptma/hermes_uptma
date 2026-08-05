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
    Schema::create('asistencias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sesion_clase_id')->constrained('sesion_clases')->cascadeOnDelete();
        $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
        $table->timestamp('hora_entrada')->nullable();
        $table->timestamp('hora_salida')->nullable();
        $table->enum('estado', ['presente_completo', 'se_retiro_antes', 'no_marco_salida', 'falta'])->default('falta');
        $table->timestamps();
        $table->unique(['sesion_clase_id', 'estudiante_id']); // un registro por estudiante por sesión
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
