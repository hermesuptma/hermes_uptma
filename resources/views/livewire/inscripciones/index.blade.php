<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Seccion;
use App\Models\Estudiante;

layout('layouts.app');

state([
    'seccion_id' => '',
    'busqueda' => '',
    'mensaje' => null,
]);

$secciones = computed(function () {
    return Seccion::with(['materia', 'trayecto'])->orderBy('nombre_seccion')->get();
});

$seccionSeleccionada = computed(function () {
    if (!$this->seccion_id) return null;
    return Seccion::with('estudiantes')->find($this->seccion_id);
});

$resultadosBusqueda = computed(function () {
    if (!$this->seccion_id || strlen($this->busqueda) < 2) return collect();

    $idsInscritos = $this->seccionSeleccionada->estudiantes->pluck('id');

    return Estudiante::where(function ($q) {
            $q->where('nombre', 'like', '%' . $this->busqueda . '%')
              ->orWhere('cedula', 'like', '%' . $this->busqueda . '%');
        })
        ->whereNotIn('id', $idsInscritos)
        ->limit(10)
        ->get();
});

$inscribir = function ($estudianteId) {
    $seccion = Seccion::findOrFail($this->seccion_id);

    if (!$seccion->estudiantes->contains($estudianteId)) {
        $seccion->estudiantes()->attach($estudianteId);
        $this->mensaje = 'Estudiante inscrito correctamente.';
    }

    $this->busqueda = '';
};

$desinscribir = function ($estudianteId) {
    $seccion = Seccion::findOrFail($this->seccion_id);
    $seccion->estudiantes()->detach($estudianteId);
};

?>

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Inscripción de Estudiantes en Secciones</h1>

    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <label class="block text-sm font-medium mb-1">Selecciona una sección</label>
        <select wire:model.live="seccion_id" class="w-full border rounded px-3 py-2">
            <option value="">-- Selecciona --</option>
            @foreach ($this->secciones as $seccion)
                <option value="{{ $seccion->id }}">
                    {{ $seccion->materia->nombre }} - {{ $seccion->nombre_seccion }} ({{ $seccion->trayecto->nombre }})
                </option>
            @endforeach
        </select>
    </div>

    @if ($seccion_id && $this->seccionSeleccionada)
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <h2 class="font-medium mb-2">Agregar estudiante</h2>
            <input
                type="text"
                wire:model.live.debounce.300ms="busqueda"
                placeholder="Busca por nombre o cédula..."
                class="w-full border rounded px-3 py-2"
            >

            @if ($busqueda && strlen($busqueda) >= 2)
                <div class="mt-2 border rounded divide-y">
                    @forelse ($this->resultadosBusqueda as $estudiante)
                        <div class="flex justify-between items-center p-2">
                            <span>{{ $estudiante->nombre }} — C.I. {{ $estudiante->cedula }}</span>
                            <button wire:click="inscribir({{ $estudiante->id }})" class="text-blue-600 text-sm">
                                + Inscribir
                            </button>
                        </div>
                    @empty
                        <p class="p-2 text-gray-500 text-sm">No se encontraron estudiantes disponibles.</p>
                    @endforelse
                </div>
            @endif

            @if ($mensaje)
                <p class="text-green-600 text-sm mt-2">{{ $mensaje }}</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow divide-y">
            <div class="p-4 font-medium bg-gray-50">
                Estudiantes inscritos ({{ $this->seccionSeleccionada->estudiantes->count() }})
            </div>
            @forelse ($this->seccionSeleccionada->estudiantes as $estudiante)
                <div class="flex justify-between items-center p-4">
                    <span>{{ $estudiante->nombre }} — C.I. {{ $estudiante->cedula }}</span>
                    <button wire:click="desinscribir({{ $estudiante->id }})" wire:confirm="¿Quitar a este estudiante de la sección?" class="text-red-600 text-sm">
                        Quitar
                    </button>
                </div>
            @empty
                <p class="p-4 text-gray-500">No hay estudiantes inscritos en esta sección todavía.</p>
            @endforelse
        </div>
    @endif
</div>