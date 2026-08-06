<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\PeriodoAcademico;

layout('layouts.app');

state([
    'nombre' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
    'editando_id' => null,
]);

$periodos = computed(function () {
    return PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();
});

$guardar = function () {
    $this->validate([
        'nombre' => 'required|string|max:255',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ]);

    if ($this->editando_id) {
        PeriodoAcademico::find($this->editando_id)->update([
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ]);
    } else {
        PeriodoAcademico::create([
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ]);
    }

    $this->reset(['nombre', 'fecha_inicio', 'fecha_fin', 'editando_id']);
};

$editar = function ($id) {
    $periodo = PeriodoAcademico::findOrFail($id);
    $this->nombre = $periodo->nombre;
    $this->fecha_inicio = $periodo->fecha_inicio->format('Y-m-d');
    $this->fecha_fin = $periodo->fecha_fin->format('Y-m-d');
    $this->editando_id = $id;
};

$cancelarEdicion = function () {
    $this->reset(['nombre', 'fecha_inicio', 'fecha_fin', 'editando_id']);
};

$eliminar = function ($id) {
    PeriodoAcademico::findOrFail($id)->delete();
};

?>

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Períodos Académicos</h1>

    <form wire:submit="guardar" class="mb-8 bg-white p-4 rounded-lg shadow">
        <label class="block text-sm font-medium mb-1">Nombre del período</label>
        <input type="text" wire:model="nombre" class="w-full border rounded px-3 py-2 mb-2" placeholder="Ej. Trimestre 1 - 2026">
        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="grid grid-cols-2 gap-2 mt-3">
            <div>
                <label class="block text-sm font-medium mb-1">Fecha de inicio</label>
                <input type="date" wire:model="fecha_inicio" class="w-full border rounded px-3 py-2">
                @error('fecha_inicio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Fecha de fin</label>
                <input type="date" wire:model="fecha_fin" class="w-full border rounded px-3 py-2">
                @error('fecha_fin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex gap-2 mt-3">
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
        @forelse ($this->periodos as $periodo)
            <div class="flex justify-between items-center p-4">
                <div>
                    <span class="font-medium">{{ $periodo->nombre }}</span>
                    <span class="text-gray-500 text-sm"> ({{ $periodo->fecha_inicio->format('d/m/Y') }} - {{ $periodo->fecha_fin->format('d/m/Y') }})</span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="editar({{ $periodo->id }})" class="text-blue-600 text-sm">Editar</button>
                    <button wire:click="eliminar({{ $periodo->id }})" wire:confirm="¿Seguro que quieres eliminar este período?" class="text-red-600 text-sm">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay períodos registrados todavía.</p>
        @endforelse
    </div>
</div>