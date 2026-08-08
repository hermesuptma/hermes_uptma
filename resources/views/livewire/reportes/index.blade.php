<?php

use function Livewire\Volt\{state, computed, layout};
use App\Models\Seccion;
use App\Models\Asistencia;

layout('layouts.app');

state([
    'seccion_id' => '',
]);

$secciones = computed(function () {
    return Seccion::with(['materia', 'trayecto'])->orderBy('nombre_seccion')->get();
});

$resumen = computed(function () {
    if (!$this->seccion_id) return collect();

    $seccion = Seccion::with('estudiantes')->find($this->seccion_id);

    return $seccion->estudiantes->map(function ($estudiante) use ($seccion) {
        $asistencias = Asistencia::where('estudiante_id', $estudiante->id)
            ->whereHas('sesionClase', function ($q) use ($seccion) {
                $q->where('seccion_id', $seccion->id);
            })
            ->get();

        $totalSesiones = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente_completo')->count();
        $faltas = $asistencias->where('estado', 'falta')->count();
        $parciales = $asistencias->where('estado', 'no_marco_salida')->count();

        $porcentaje = $totalSesiones > 0
            ? round((($presentes + $parciales) / $totalSesiones) * 100)
            : 0;

        return [
            'estudiante' => $estudiante,
            'total_sesiones' => $totalSesiones,
            'presentes' => $presentes,
            'faltas' => $faltas,
            'parciales' => $parciales,
            'porcentaje' => $porcentaje,
        ];
    })->sortBy('estudiante.nombre');
});

?>

<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Reporte de Asistencia</h1>

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

    @if ($seccion_id)
        <div class="mb-3">
            <a href="{{ route('reportes.pdf', $seccion_id) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block text-sm">
                Exportar a PDF
            </a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Estudiante</th>
                        <th class="p-3 text-center">Sesiones</th>
                        <th class="p-3 text-center">Presente</th>
                        <th class="p-3 text-center">Parcial</th>
                        <th class="p-3 text-center">Faltas</th>
                        <th class="p-3 text-center">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($this->resumen as $fila)
                        <tr>
                            <td class="p-3">{{ $fila['estudiante']->nombre }}</td>
                            <td class="p-3 text-center">{{ $fila['total_sesiones'] }}</td>
                            <td class="p-3 text-center text-green-600">{{ $fila['presentes'] }}</td>
                            <td class="p-3 text-center text-yellow-600">{{ $fila['parciales'] }}</td>
                            <td class="p-3 text-center text-red-600">{{ $fila['faltas'] }}</td>
                            <td class="p-3 text-center font-medium">{{ $fila['porcentaje'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No hay estudiantes inscritos en esta sección.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>