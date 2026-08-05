<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Carrera;

layout('layouts.app');

state([
    'nombre' => '',
    'editando_id' => null,
]);

$carreras = computed(function () {
    return Carrera::orderBy('nombre')->get();
});

$guardar = function () {
    $this->validate([
        'nombre' => 'required|string|max:255',
    ]);

    if ($this->editando_id) {
        Carrera::find($this->editando_id)->update(['nombre' => $this->nombre]);
    } else {
        Carrera::create(['nombre' => $this->nombre]);
    }

    $this->reset(['nombre', 'editando_id']);
};

$editar = function ($id) {
    $carrera = Carrera::findOrFail($id);
    $this->nombre = $carrera->nombre;
    $this->editando_id = $id;
};

$cancelarEdicion = function () {
    $this->reset(['nombre', 'editando_id']);
};

$eliminar = function ($id) {
    Carrera::findOrFail($id)->delete();
};

?>

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Carreras</h1>

    {{-- Formulario --}}
    <form wire:submit="guardar" class="mb-8 bg-white p-4 rounded-lg shadow">
        <label class="block text-sm font-medium mb-1">Nombre de la carrera</label>
        <input type="text" wire:model="nombre" class="w-full border rounded px-3 py-2 mb-2" placeholder="Ej. Ingeniería en Informática">
        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

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

    {{-- Lista --}}
    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($this->carreras as $carrera)
            <div class="flex justify-between items-center p-4">
                <span>{{ $carrera->nombre }}</span>
                <div class="flex gap-2">
                    <button wire:click="editar({{ $carrera->id }})" class="text-blue-600 text-sm">Editar</button>
                    <button wire:click="eliminar({{ $carrera->id }})" wire:confirm="¿Seguro que quieres eliminar esta carrera?" class="text-red-600 text-sm">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay carreras registradas todavía.</p>
        @endforelse
    </div>
</div>