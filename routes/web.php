<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Estudiante;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    ->middleware(['auth', 'verified'])
    ->name('carreras');

Volt::route('materias', 'materias.index')
    ->middleware(['auth', 'verified'])
    ->name('materias');

Volt::route('periodos', 'periodos.index')
    ->middleware(['auth', 'verified'])
    ->name('periodos');

Volt::route('trayectos', 'trayectos.index')
    ->middleware(['auth', 'verified'])
    ->name('trayectos');

Volt::route('secciones', 'secciones.index')
    ->middleware(['auth', 'verified'])
    ->name('secciones');

Volt::route('estudiantes', 'estudiantes.index')
    ->middleware(['auth', 'verified'])
    ->name('estudiantes');

Volt::route('inscripciones', 'inscripciones.index')
    ->middleware(['auth', 'verified'])
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