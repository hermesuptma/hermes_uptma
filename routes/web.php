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

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';