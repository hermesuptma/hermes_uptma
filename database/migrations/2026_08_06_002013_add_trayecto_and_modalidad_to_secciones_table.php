<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->foreignId('trayecto_id')->after('materia_id')->constrained('trayectos')->cascadeOnDelete();
            $table->enum('modalidad', ['regular', 'paralelo'])->default('regular')->after('trayecto_id');
        });
    }

    public function down(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->dropForeign(['trayecto_id']);
            $table->dropColumn(['trayecto_id', 'modalidad']);
        });
    }
};