<?php

use function Livewire\Volt\{state, computed, layout, on};
use App\Models\Seccion;
use App\Models\SesionClase;
use App\Models\Estudiante;
use App\Models\Asistencia;

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
    return SesionClase::with('seccion.estudiantes')->find($this->sesionActivaId);
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

$cambiarModo = function ($nuevoModo) {
    $sesion = SesionClase::find($this->sesionActivaId);
    $sesion->update(['modo_actual' => $nuevoModo]);
    $this->ultimoResultado = null;
};

$finalizarSesion = function () {
    $sesion = $this->sesionActiva;

    $idsInscritos = $sesion->seccion->estudiantes->pluck('id');
    $idsConAsistencia = Asistencia::where('sesion_clase_id', $sesion->id)->pluck('estudiante_id');
    $idsSinAsistencia = $idsInscritos->diff($idsConAsistencia);

    foreach ($idsSinAsistencia as $estudianteId) {
        Asistencia::create([
            'sesion_clase_id' => $sesion->id,
            'estudiante_id' => $estudianteId,
            'estado' => 'falta',
        ]);
    }

    $sesion->update(['modo_actual' => 'cerrada']);

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

    $sesion = $this->sesionActiva;

    if (!$sesion->seccion->estudiantes->contains($estudiante->id)) {
        $this->ultimoResultado = ['ok' => false, 'mensaje' => "{$estudiante->nombre} no está inscrito en esta sección."];
        return;
    }

    $asistencia = Asistencia::firstOrNew([
        'sesion_clase_id' => $sesion->id,
        'estudiante_id' => $estudiante->id,
    ]);

    if ($sesion->modo_actual === 'entrada') {
        if ($asistencia->exists && $asistencia->hora_entrada) {
            $this->ultimoResultado = ['ok' => false, 'mensaje' => "{$estudiante->nombre} ya marcó entrada."];
            return;
        }
        $asistencia->hora_entrada = now();
        $asistencia->estado = 'no_marco_salida';
    } elseif ($sesion->modo_actual === 'salida') {
        if (!$asistencia->exists || !$asistencia->hora_entrada) {
            $this->ultimoResultado = ['ok' => false, 'mensaje' => "{$estudiante->nombre} no marcó entrada, no puede marcar salida."];
            return;
        }
        if ($asistencia->hora_salida) {
            $this->ultimoResultado = ['ok' => false, 'mensaje' => "{$estudiante->nombre} ya marcó salida."];
            return;
        }
        $asistencia->hora_salida = now();
        $asistencia->estado = 'presente_completo';
    }

    $asistencia->save();

    $this->ultimoResultado = ['ok' => true, 'mensaje' => "✓ {$estudiante->nombre} — {$sesion->modo_actual}"];
    $this->dispatch('reproducir-sonido');
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

            <div class="flex gap-2 mb-4">
                <button wire:click="cambiarModo('entrada')" class="px-3 py-1 rounded text-sm {{ $this->sesionActiva->modo_actual === 'entrada' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    Modo Entrada
                </button>
                <button wire:click="cambiarModo('salida')" class="px-3 py-1 rounded text-sm {{ $this->sesionActiva->modo_actual === 'salida' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    Modo Salida
                </button>
            </div>

            <div id="lector-qr" style="width: 100%; max-width: 400px;"></div>

            @if ($ultimoResultado)
                <p class="mt-3 text-sm font-medium {{ $ultimoResultado['ok'] ? 'text-green-600' : 'text-red-600' }}">
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
        var ultimoCodigoLeido = null;
        var ultimoTiempoLeido = 0;
        var COOLDOWN_MS = 3000;

        function iniciarLector() {
            var elemento = document.getElementById('lector-qr');
            if (!elemento || lector) return;

            lector = new Html5Qrcode('lector-qr');
            lector.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 250 },
                function (textoDecodificado) {
                    var ahora = Date.now();
                    if (textoDecodificado === ultimoCodigoLeido && (ahora - ultimoTiempoLeido) < COOLDOWN_MS) {
                        return;
                    }
                    ultimoCodigoLeido = textoDecodificado;
                    ultimoTiempoLeido = ahora;
                    Livewire.dispatch('procesar-escaneo-cliente', { texto: textoDecodificado });
                },
                function (error) {}
            ).catch(function (err) {
                console.error('No se pudo iniciar la camara:', err);
            });
        }

        function reproducirBeep() {
            var contexto = new (window.AudioContext || window.webkitAudioContext)();
            var oscilador = contexto.createOscillator();
            var ganancia = contexto.createGain();
            oscilador.connect(ganancia);
            ganancia.connect(contexto.destination);
            oscilador.frequency.value = 880;
            ganancia.gain.setValueAtTime(0.2, contexto.currentTime);
            oscilador.start();
            oscilador.stop(contexto.currentTime + 0.15);
        }

        Livewire.on('iniciar-camara', function () {
            setTimeout(iniciarLector, 300);
        });

        Livewire.on('reproducir-sonido', function () {
            reproducirBeep();
        });
    });
</script>