<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';