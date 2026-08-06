<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Trayecto;

layout('layouts.app');

state([
    'nombre' => '',
    'editando_id' => null,
]);

$trayectos = computed(function () {
    return Trayecto::orderBy('nombre')->get();
});

$guardar = function () {
    $this->validate([
        'nombre' => 'required|string|max:255',
    ]);

    if ($this->editando_id) {
        Trayecto::find($this->editando_id)->update(['nombre' => $this->nombre]);
    } else {
        Trayecto::create(['nombre' => $this->nombre]);
    }

    $this->reset(['nombre', 'editando_id']);
};

$editar = function ($id) {
    $trayecto = Trayecto::findOrFail($id);
    $this->nombre = $trayecto->nombre;
    $this->editando_id = $id;
};

$cancelarEdicion = function () {
    $this->reset(['nombre', 'editando_id']);
};

$eliminar = function ($id) {
    Trayecto::findOrFail($id)->delete();
};

?>

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Trayectos</h1>

    <form wire:submit="guardar" class="mb-8 bg-white p-4 rounded-lg shadow">
        <label class="block text-sm font-medium mb-1">Nombre del trayecto</label>
        <input type="text" wire:model="nombre" class="w-full border rounded px-3 py-2 mb-2" placeholder="Ej. Trayecto II">
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

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($this->trayectos as $trayecto)
            <div class="flex justify-between items-center p-4">
                <span>{{ $trayecto->nombre }}</span>
                <div class="flex gap-2">
                    <button wire:click="editar({{ $trayecto->id }})" class="text-blue-600 text-sm">Editar</button>
                    <button wire:click="eliminar({{ $trayecto->id }})" wire:confirm="¿Seguro que quieres eliminar este trayecto?" class="text-red-600 text-sm">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay trayectos registrados todavía.</p>
        @endforelse
    </div>
</div>