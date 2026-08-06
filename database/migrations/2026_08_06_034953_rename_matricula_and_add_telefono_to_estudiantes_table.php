<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->renameColumn('matricula', 'cedula');
            $table->string('telefono')->nullable()->after('correo');
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->renameColumn('cedula', 'matricula');
            $table->dropColumn('telefono');
        });
    }
};