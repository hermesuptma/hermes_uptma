<?php

use function Livewire\Volt\{state, computed, layout, on};
use App\Models\Seccion;
use App\Models\SesionClase;
use App\Models\Estudiante;

layout('layouts.app');

state([
    'seccion_id' => '',
    'sesionActivaId' => null,
    'ultimoResultado' => null,
]);

$secciones = computed(function () {
    return Seccion::with(['materia', 'trayecto'])->orderBy('nombre_seccion')->get();
});

$sesionActiva = computed(function () {
    if (!$this->sesionActivaId) return null;
    return SesionClase::find($this->sesionActivaId);
});

$iniciarClase = function () {
    $this->validate([
        'seccion_id' => 'required|exists:secciones,id',
    ]);

    $sesion = SesionClase::create([
        'seccion_id' => $this->seccion_id,
        'fecha' => now()->toDateString(),
        'hora_inicio' => now()->toTimeString(),
        'modo_actual' => 'entrada',
    ]);

    $this->sesionActivaId = $sesion->id;
    $this->dispatch('iniciar-camara');
};

$finalizarSesion = function () {
    $this->sesionActivaId = null;
    $this->seccion_id = '';
    $this->ultimoResultado = null;
};

on(['procesar-escaneo-cliente' => function ($texto) {
    $estudiante = Estudiante::buscarPorCodigoEscaneado($texto);

    if (!$estudiante) {
        $this->ultimoResultado = ['ok' => false, 'mensaje' => 'Código no reconocido.'];
        return;
    }

    $this->ultimoResultado = ['ok' => true, 'mensaje' => "Detectado: {$estudiante->nombre}"];
}]);

?>

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Escaneo de Asistencia</h1>

    @if (!$sesionActivaId)
        <div class="bg-white p-4 rounded-lg shadow">
            <label class="block text-sm font-medium mb-1">Selecciona la sección</label>
            <select wire:model="seccion_id" class="w-full border rounded px-3 py-2 mb-3">
                <option value="">-- Selecciona --</option>
                @foreach ($this->secciones as $seccion)
                    <option value="{{ $seccion->id }}">
                        {{ $seccion->materia->nombre }} - {{ $seccion->nombre_seccion }} ({{ $seccion->trayecto->nombre }})
                    </option>
                @endforeach
            </select>
            @error('seccion_id') <span class="text-red-500 text-sm block mb-2">{{ $message }}</span> @enderror

            <button wire:click="iniciarClase" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Iniciar clase
            </button>
        </div>
    @else
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="mb-1">
                Sesión activa: <strong>{{ $this->sesionActiva->seccion->materia->nombre }} - {{ $this->sesionActiva->seccion->nombre_seccion }}</strong>
            </p>
            <p class="text-sm text-gray-500 mb-4">
                Modo actual: <strong>{{ $this->sesionActiva->modo_actual }}</strong>
            </p>

            <div id="lector-qr" style="width: 100%; max-width: 400px;"></div>

            @if ($ultimoResultado)
                <p class="mt-3 text-sm {{ $ultimoResultado['ok'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ $ultimoResultado['mensaje'] }}
                </p>
            @endif

            <button wire:click="finalizarSesion" class="bg-gray-300 px-4 py-2 rounded mt-4">
                Finalizar sesión
            </button>
        </div>
    @endif
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('livewire:init', function () {
        var lector = null;

        function iniciarLector() {
            var elemento = document.getElementById('lector-qr');
            if (!elemento || lector) return;

            lector = new Html5Qrcode('lector-qr');
            lector.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 250 },
                function (textoDecodificado) {
                    Livewire.dispatch('procesar-escaneo-cliente', { texto: textoDecodificado });
                },
                function (error) {}
            ).catch(function (err) {
                console.error('No se pudo iniciar la camara:', err);
            });
        }

        Livewire.on('iniciar-camara', function () {
            setTimeout(iniciarLector, 300);
        });
    });
</script>