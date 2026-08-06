<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Seccion;
use App\Models\Materia;
use App\Models\PeriodoAcademico;
use App\Models\Trayecto;
use App\Models\User;

layout('layouts.app');

state([
    'materia_id' => '',
    'profesor_id' => '',
    'periodo_academico_id' => '',
    'trayecto_id' => '',
    'modalidad' => 'regular',
    'nombre_seccion' => '',
    'editando_id' => null,
]);

$secciones = computed(function () {
    return Seccion::with(['materia', 'profesor', 'periodoAcademico', 'trayecto'])
        ->orderBy('nombre_seccion')
        ->get();
});

$materias = computed(fn () => Materia::orderBy('nombre')->get());
$profesores = computed(fn () => User::orderBy('name')->get());
$periodos = computed(fn () => PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get());
$trayectos = computed(fn () => Trayecto::orderBy('nombre')->get());

$guardar = function () {
    $this->validate([
        'materia_id' => 'required|exists:materias,id',
        'profesor_id' => 'required|exists:users,id',
        'periodo_academico_id' => 'required|exists:periodo_academicos,id',
        'trayecto_id' => 'required|exists:trayectos,id',
        'modalidad' => 'required|in:regular,paralelo',
        'nombre_seccion' => 'required|string|max:255',
    ]);

    $data = [
        'materia_id' => $this->materia_id,
        'profesor_id' => $this->profesor_id,
        'periodo_academico_id' => $this->periodo_academico_id,
        'trayecto_id' => $this->trayecto_id,
        'modalidad' => $this->modalidad,
        'nombre_seccion' => $this->nombre_seccion,
    ];

    if ($this->editando_id) {
        Seccion::find($this->editando_id)->update($data);
    } else {
        Seccion::create($data);
    }

    $this->reset(['materia_id', 'profesor_id', 'periodo_academico_id', 'trayecto_id', 'modalidad', 'nombre_seccion', 'editando_id']);
    $this->modalidad = 'regular';
};

$editar = function ($id) {
    $seccion = Seccion::findOrFail($id);
    $this->materia_id = $seccion->materia_id;
    $this->profesor_id = $seccion->profesor_id;
    $this->periodo_academico_id = $seccion->periodo_academico_id;
    $this->trayecto_id = $seccion->trayecto_id;
    $this->modalidad = $seccion->modalidad;
    $this->nombre_seccion = $seccion->nombre_seccion;
    $this->editando_id = $id;
};

$cancelarEdicion = function () {
    $this->reset(['materia_id', 'profesor_id', 'periodo_academico_id', 'trayecto_id', 'modalidad', 'nombre_seccion', 'editando_id']);
    $this->modalidad = 'regular';
};

$eliminar = function ($id) {
    Seccion::findOrFail($id)->delete();
};

?>

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Secciones</h1>

    <form wire:submit="guardar" class="mb-8 bg-white p-4 rounded-lg shadow space-y-3">
        <div>
            <label class="block text-sm font-medium mb-1">Materia</label>
            <select wire:model="materia_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Selecciona una materia --</option>
                @foreach ($this->materias as $materia)
                    <option value="{{ $materia->id }}">{{ $materia->nombre }} ({{ $materia->codigo }})</option>
                @endforeach
            </select>
            @error('materia_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Trayecto</label>
                <select wire:model="trayecto_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Selecciona --</option>
                    @foreach ($this->trayectos as $trayecto)
                        <option value="{{ $trayecto->id }}">{{ $trayecto->nombre }}</option>
                    @endforeach
                </select>
                @error('trayecto_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Modalidad</label>
                <select wire:model="modalidad" class="w-full border rounded px-3 py-2">
                    <option value="regular">Regular</option>
                    <option value="paralelo">Paralelo</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Profesor</label>
            <select wire:model="profesor_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Selecciona un profesor --</option>
                @foreach ($this->profesores as $profesor)
                    <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
                @endforeach
            </select>
            @error('profesor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Período académico</label>
            <select wire:model="periodo_academico_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Selecciona un período --</option>
                @foreach ($this->periodos as $periodo)
                    <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                @endforeach
            </select>
            @error('periodo_academico_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Nombre de la sección</label>
            <input type="text" wire:model="nombre_seccion" class="w-full border rounded px-3 py-2" placeholder="Ej. Sección A">
            @error('nombre_seccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ $editando_id ? 'Actualizar' : 'Crear' }}
            </button>
            @if ($editando_id)
                <button type="button" wire:click="cancelarEdicion" class="bg-gray-300 px-4 py-2 rounded">
                    Cancelar
                </button>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($this->secciones as $seccion)
            <div class="flex justify-between items-center p-4">
                <div>
                    <span class="font-medium">{{ $seccion->materia->nombre }} - {{ $seccion->nombre_seccion }}</span>
                    <div class="text-gray-500 text-sm">
                        {{ $seccion->trayecto->nombre }} ({{ ucfirst($seccion->modalidad) }}) —
                        Prof. {{ $seccion->profesor->name }} —
                        {{ $seccion->periodoAcademico->nombre }}
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="editar({{ $seccion->id }})" class="text-blue-600 text-sm">Editar</button>
                    <button wire:click="eliminar({{ $seccion->id }})" wire:confirm="¿Seguro que quieres eliminar esta sección?" class="text-red-600 text-sm">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay secciones registradas todavía.</p>
        @endforelse
    </div>
</div>