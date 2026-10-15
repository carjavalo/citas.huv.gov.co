<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Confirmación de cita - HUV</title>
    <link rel="icon" href="{{ asset('img/favicon2.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-[#2c4370]">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
            <span class="bg-white rounded-full p-1 shadow">
                <img src="{{ asset('huv-icon.png') }}" alt="HUV" class="h-10 w-10 object-contain">
            </span>
            <div class="text-white">
                <p class="text-sm font-semibold leading-tight">Hospital Universitario del Valle</p>
                <p class="text-xs text-blue-100">"Evaristo García" E.S.E.</p>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
        @if (!$citacion)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <span class="material-icons text-5xl text-gray-400">link_off</span>
                <h1 class="text-xl font-bold text-gray-800 mt-3">Enlace no válido</h1>
                <p class="text-gray-600 mt-2">El enlace de confirmación no existe o está incompleto. Verifique que abrió el botón "Ir a confirmar" del correo que recibió.</p>
            </div>
        @else
            @if (session('respuesta_guardada'))
                <div class="mb-6 rounded-xl p-4 flex items-start border {{ $citacion->estado === 'asistira' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                    <span class="material-icons mr-2">{{ $citacion->estado === 'asistira' ? 'check_circle' : 'info' }}</span>
                    <p class="text-sm font-medium">
                        @if ($citacion->estado === 'asistira')
                            ¡Gracias! Su asistencia quedó confirmada. Recuerde llegar 40 minutos antes de la consulta.
                        @else
                            Gracias por avisarnos. Registramos que no asistirá a esta cita.
                        @endif
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h1 class="text-xl font-bold text-[#2c4370]">Confirmación de cita médica</h1>
                    <p class="text-gray-600 mt-1">SR(A) <strong>{{ $citacion->paciente }}</strong>, estos son los datos de su cita:</p>
                </div>

                <dl class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Especialidad</dt>
                        <dd class="font-semibold text-gray-800">{{ $citacion->especialidad }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Especialista</dt>
                        <dd class="font-semibold text-gray-800">Dr. {{ $citacion->especialista }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-semibold text-gray-800">{{ trim($citacion->dia . ', ' . $citacion->fecha_cita, ', ') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hora</dt>
                        <dd class="font-semibold text-gray-800">{{ $citacion->hora }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Lugar</dt>
                        <dd class="font-semibold text-gray-800">{{ $direccion }}</dd>
                    </div>
                </dl>

                <div class="px-6 py-4 bg-slate-50 border-t border-gray-200 text-xs text-slate-600">
                    Traer autorización vigente, orden médica e historia clínica. Llegar 40 minutos antes de la consulta para facturar.
                </div>

                <div class="px-6 py-6 border-t border-gray-200">
                    @if ($citacion->estado !== 'pendiente')
                        <p class="text-sm text-gray-600 mb-4">
                            Su respuesta actual:
                            <strong class="{{ $citacion->estado === 'asistira' ? 'text-green-700' : 'text-amber-700' }}">{{ $citacion->estado_texto }}</strong>
@if ($citacion->respondido_at) <span class="text-gray-400">({{ $citacion->respondido_at->format('d/m/Y h:i A') }})</span>@endif.
                            Si cambió de opinión puede actualizarla.
                        </p>
                    @else
                        <p class="text-sm font-medium text-gray-700 mb-4">¿Asistirá a esta cita?</p>
                    @endif

                    <form method="POST" action="{{ route('citacion.responder', $citacion->token) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <button type="submit" name="respuesta" value="asistira"
                                class="inline-flex items-center justify-center px-4 py-3 rounded-lg text-sm font-semibold shadow-sm transition-colors {{ $citacion->estado === 'asistira' ? 'bg-green-700 text-white ring-2 ring-green-300' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                            <span class="material-icons text-base mr-2">event_available</span> Sí, asistiré
                        </button>
                        <button type="submit" name="respuesta" value="no_asistira"
                                class="inline-flex items-center justify-center px-4 py-3 rounded-lg text-sm font-semibold shadow-sm transition-colors {{ $citacion->estado === 'no_asistira' ? 'bg-red-700 text-white ring-2 ring-red-300' : 'bg-white border border-red-300 text-red-700 hover:bg-red-50' }}">
                            <span class="material-icons text-base mr-2">event_busy</span> No asistiré
                        </button>
                    </form>
                    @error('respuesta') <p class="text-xs text-red-600 mt-2">Seleccione una de las dos opciones.</p> @enderror
                </div>
            </div>

            <p class="text-xs text-gray-500 text-center mt-6">Para cancelación de citas comuníquese al <strong>6206000 ext 5555</strong>.</p>
        @endif
    </main>
</body>
</html>
