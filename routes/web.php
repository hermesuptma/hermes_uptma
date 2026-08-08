<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Estudiante;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Asistencia;

Route::get('/reportes/{seccion}/pdf', function (\App\Models\Seccion $seccion) {
    $estudiantes = $seccion->estudiantes;

    $resumen = $estudiantes->map(function ($estudiante) use ($seccion) {
        $asistencias = Asistencia::where('estudiante_id', $estudiante->id)
            ->whereHas('sesionClase', fn($q) => $q->where('seccion_id', $seccion->id))
            ->get();

        $total = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente_completo')->count();
        $parciales = $asistencias->where('estado', 'no_marco_salida')->count();
        $faltas = $asistencias->where('estado', 'falta')->count();
        $porcentaje = $total > 0 ? round((($presentes + $parciales) / $total) * 100) : 0;

        return compact('estudiante', 'total', 'presentes', 'parciales', 'faltas', 'porcentaje');
    })->sortBy('estudiante.nombre');

    $pdf = Pdf::loadView('reportes.pdf', compact('seccion', 'resumen'));
    return $pdf->stream('reporte-asistencia.pdf');
})->middleware(['auth', 'verified'])->name('reportes.pdf');

Route::get('/carnets/generar', function () {
    $estudiantes = Estudiante::orderBy('nombre')->get();

    $qrs = [];
    foreach ($estudiantes as $estudiante) {
        $qrs[$estudiante->id] = 'data:image/svg+xml;base64,' . base64_encode(
        QrCode::format('svg')->size(200)->generate($estudiante->codigo_qr)
        );
    }

    $pdf = Pdf::loadView('carnets.pdf', compact('estudiantes', 'qrs'));
    return $pdf->stream('carnets.pdf');
})->middleware(['auth', 'verified'])->name('carnets.generar');

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('carreras', 'carreras.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('carreras');

Volt::route('materias', 'materias.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('materias');

Volt::route('periodos', 'periodos.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('periodos');

Volt::route('trayectos', 'trayectos.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('trayectos');

Volt::route('secciones', 'secciones.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('secciones');

Volt::route('estudiantes', 'estudiantes.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('estudiantes');

Volt::route('inscripciones', 'inscripciones.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('inscripciones');

Volt::route('escaneo', 'escaneo.index')
    ->middleware(['auth', 'verified'])
    ->name('escaneo');

Volt::route('reportes', 'reportes.index')
    ->middleware(['auth', 'verified'])
    ->name('reportes');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';