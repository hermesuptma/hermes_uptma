<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Materia;
use App\Models\Carrera;

layout('layouts.app');

state([
    'carrera_id' => '',
    'nombre' => '',
    'codigo' => '',
    'editando_id' => null,
]);

$materias = computed(function () {
    return Materia::with('carrera')->orderBy('nombre')->get();
});

$carreras = computed(function () {
    return Carrera::orderBy('nombre')->get();
});

$guardar = function () {
    $this->validate([
        'carrera_id' => 'required|exists:carreras,id',
        'nombre' => 'required|string|max:255',
        'codigo' => 'required|string|max:50|unique:materias,codigo,' . $this->editando_id,
    ]);

    if ($this->editando_id) {
        Materia::find($this->editando_id)->update([
            'carrera_id' => $this->carrera_id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
        ]);
    } else {
        Materia::create([
            'carrera_id' => $this->carrera_id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
        ]);
    }

    $this->reset(['carrera_id', 'nombre', 'codigo', 'editando_id']);
};

$editar = function ($id) {
    $materia = Materia::findOrFail($id);
    $this->carrera_id = $materia->carrera_id;
    $this->nombre = $materia->nombre;
    $this->codigo = $materia->codigo;
    $this->editando_id = $id;
};

$cancelarEdicion = function () {
    $this->reset(['carrera_id', 'nombre', 'codigo', 'editando_id']);
};

$eliminar = function ($id) {
    Materia::findOrFail($id)->delete();
};

?>

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Materias</h1>

    <form wire:submit="guardar" class="mb-8 bg-white p-4 rounded-lg shadow">
        <label class="block text-sm font-medium mb-1">Carrera</label>
        <select wire:model="carrera_id" class="w-full border rounded px-3 py-2 mb-2">
            <option value="">-- Selecciona una carrera --</option>
            @foreach ($this->carreras as $carrera)
                <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
            @endforeach
        </select>
        @error('carrera_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <label class="block text-sm font-medium mb-1 mt-3">Nombre de la materia</label>
        <input type="text" wire:model="nombre" class="w-full border rounded px-3 py-2 mb-2" placeholder="Ej. Programación II">
        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <label class="block text-sm font-medium mb-1 mt-3">Código</label>
        <input type="text" wire:model="codigo" class="w-full border rounded px-3 py-2 mb-2" placeholder="Ej. PROG202">
        @error('codigo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

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
        @forelse ($this->materias as $materia)
            <div class="flex justify-between items-center p-4">
                <div>
                    <span class="font-medium">{{ $materia->nombre }}</span>
                    <span class="text-gray-500 text-sm"> ({{ $materia->codigo }}) — {{ $materia->carrera->nombre }}</span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="editar({{ $materia->id }})" class="text-blue-600 text-sm">Editar</button>
                    <button wire:click="eliminar({{ $materia->id }})" wire:confirm="¿Seguro que quieres eliminar esta materia?" class="text-red-600 text-sm">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay materias registradas todavía.</p>
        @endforelse
    </div>
</div>