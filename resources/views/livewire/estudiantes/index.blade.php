<?php

use function Livewire\Volt\{state, computed, layout, usesFileUploads};
use App\Models\Estudiante;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

layout('layouts.app');
usesFileUploads();

state([
    'archivo' => null,
    'mensaje' => null,
    'errores' => [],
]);

$estudiantes = computed(function () {
    return Estudiante::orderBy('nombre')->get();
});

$importar = function () {
    $this->validate([
        'archivo' => 'required|mimes:xlsx,xls,csv',
    ]);

    $this->errores = [];
    $this->mensaje = null;

    $rutaTemporal = $this->archivo->getRealPath();
    $spreadsheet = IOFactory::load($rutaTemporal);
    $hoja = $spreadsheet->getActiveSheet();
    $filas = $hoja->toArray();

    $encabezados = array_map('strtolower', array_map('trim', array_shift($filas)));

    $creados = 0;

    foreach ($filas as $i => $fila) {
        $numeroFila = $i + 2;

        if (count(array_filter($fila)) === 0) continue;

        $datos = array_combine($encabezados, $fila);

        if (empty($datos['cedula']) || empty($datos['nombre_completo']) || empty($datos['correo'])) {
            $this->errores[] = "Fila {$numeroFila}: faltan datos obligatorios.";
            continue;
        }

        if (Estudiante::where('cedula', $datos['cedula'])->exists()) {
            $this->errores[] = "Fila {$numeroFila}: la cédula '{$datos['cedula']}' ya existe.";
            continue;
        }

        if (Estudiante::where('correo', $datos['correo'])->exists()) {
            $this->errores[] = "Fila {$numeroFila}: el correo '{$datos['correo']}' ya existe.";
            continue;
        }

        Estudiante::create([
            'cedula'    => $datos['cedula'],
            'nombre'    => $datos['nombre_completo'],
            'correo'    => $datos['correo'],
            'telefono'  => $datos['numero_de_telefono'] ?? null,
            'codigo_qr' => (string) Str::uuid(),
        ]);

        $creados++;
    }

    $this->mensaje = "{$creados} estudiante(s) importado(s) correctamente.";
    $this->reset('archivo');
};

$eliminar = function ($id) {
    Estudiante::findOrFail($id)->delete();
};

?>

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Estudiantes</h1>

    <div class="mb-8 bg-white p-4 rounded-lg shadow">
        <h2 class="font-medium mb-2">Importar desde Excel</h2>
        <p class="text-sm text-gray-500 mb-3">
            Columnas requeridas: cedula, nombre_completo, correo, numero_de_telefono
        </p>

        <form wire:submit="importar">
            <input type="file" wire:model="archivo" class="mb-2" accept=".xlsx,.xls,.csv">
            @error('archivo')
                <span class="text-red-500 text-sm block">{{ $message }}</span>
            @enderror

            <div wire:loading wire:target="archivo" class="text-sm text-gray-400">
                Subiendo archivo...
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mt-2">
                Importar
            </button>
        </form>

        @if ($mensaje)
            <p class="text-green-600 text-sm mt-3">{{ $mensaje }}</p>
        @endif

        @if (count($errores) > 0)
            <div class="mt-3 bg-red-50 border border-red-200 rounded p-3">
                <p class="text-red-700 font-medium text-sm mb-1">Errores encontrados:</p>
                <ul class="text-red-600 text-sm list-disc list-inside">
                    @foreach ($errores as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($this->estudiantes as $estudiante)
            <div class="flex justify-between items-center p-4">
                <div>
                    <span class="font-medium">{{ $estudiante->nombre }}</span>
                    <span class="text-gray-500 text-sm">
                        — C.I. {{ $estudiante->cedula }} — {{ $estudiante->correo }}
                    </span>
                </div>
                <button wire:click="eliminar({{ $estudiante->id }})" wire:confirm="¿Eliminar este estudiante?" class="text-red-600 text-sm">
                    Eliminar
                </button>
            </div>
        @empty
            <p class="p-4 text-gray-500">No hay estudiantes registrados todavía.</p>
        @endforelse
    </div>
</div>