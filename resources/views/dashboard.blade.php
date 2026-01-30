@php
    use App\Models\ConfiguracionSistema;
    use App\Models\Campana;
    
    $mostrarSeccionCampanas = ConfiguracionSistema::estaHabilitado('seccion_novedades_campanas_habilitada');
    
    // Obtener campañas publicadas y visibles
    $campanas = Campana::where('estado', 'publicado')
        ->where('visible', true)
        ->orderBy('orden')
        ->orderByDesc('created_at')
        ->take(6)
        ->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #2c4370;">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Hospital Universitario del Valle Evaristo García E.S.E.</h1>
                    <p class="text-xl text-gray-600 mt-4">Bienvenido {{ Auth::user()->name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <!-- Solicitar nueva cita -->
                    <div class="p-6 bg-gray-50 rounded-lg hover:shadow-md transition-shadow border border-gray-200 group">
                        <div class="flex items-start justify-between mb-4">
                            <a href="{{ route('cita.solicitar') }}" class="text-xl font-semibold hover:underline" style="color: #2c4370;">Solicitar nueva cita</a>
                            <svg class="w-8 h-8 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mt-2">Envía la solicitud para el agendamiento de tu cita médica.</p>
                        <a href="{{ route('cita.solicitar') }}" class="inline-flex items-center mt-4 font-medium hover:underline" style="color: #2c4370;">
                            Solicitar cita
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Consultar mis citas -->
                    <div class="p-6 bg-gray-50 rounded-lg hover:shadow-md transition-shadow border border-gray-200 group">
                        <div class="flex items-start justify-between mb-4">
                            <a href="{{ route('cita.consulta') }}" class="text-xl font-semibold hover:underline" style="color: #2c4370;">Consultar mis citas</a>
                            <svg class="w-8 h-8 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mt-2">Consulta el estado de tus citas médicas agendadas.</p>
                        <a href="{{ route('cita.consulta') }}" class="inline-flex items-center mt-4 font-medium hover:underline" style="color: #2c4370;">
                            Consultar cita
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            @if($mostrarSeccionCampanas && $campanas->count() > 0)
            <!-- Sección de Novedades y Campañas -->
            <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-3 flex items-center justify-between">
                    <h2 class="text-lg font-bold flex items-center gap-2" style="color: #2c4370;">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Novedades y Campañas
                    </h2>
                    @if($campanas->count() > 3)
                    <div class="flex items-center gap-2">
                        <button onclick="moverCarrusel(-1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors border border-gray-300" title="Anterior">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button onclick="moverCarrusel(1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors border border-gray-300" title="Siguiente">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>
                
                <div class="p-4 overflow-hidden">
                    <div id="carrusel-campanas" class="flex transition-transform duration-500 ease-in-out" style="gap: 1rem;">
                        @foreach($campanas as $campana)
                        <div class="relative rounded-lg overflow-hidden group shadow hover:shadow-md transition-shadow cursor-pointer flex-shrink-0" style="width: calc(33.333% - 0.67rem);">
                            @if($campana->video)
                                <video class="w-full h-28 object-cover" autoplay muted loop playsinline>
                                    <source src="{{ asset('storage/' . $campana->video) }}" type="video/mp4">
                                </video>
                            @elseif($campana->imagen)
                                <img alt="{{ $campana->titulo }}" class="w-full h-28 object-cover transform group-hover:scale-105 transition-transform duration-500" src="{{ asset('storage/' . $campana->imagen) }}"/>
                            @else
                                <div class="w-full h-28 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t 
                                @if($campana->categoria === 'urgente') from-red-900/90 via-red-900/40 
                                @elseif($campana->categoria === 'noticia') from-purple-900/90 via-purple-900/40
                                @elseif($campana->categoria === 'servicio') from-green-900/90 via-green-900/40
                                @else from-blue-900/90 via-blue-900/40 @endif 
                                to-transparent p-3 flex flex-col justify-end">
                                <span class="text-[10px] font-bold uppercase tracking-wider
                                    @if($campana->categoria === 'urgente') text-red-300
                                    @elseif($campana->categoria === 'noticia') text-purple-300
                                    @elseif($campana->categoria === 'servicio') text-green-300
                                    @else text-blue-300 @endif">
                                    {{ ucfirst($campana->categoria) }}
                                </span>
                                @php
                                    $tamanoTexto = is_numeric($campana->tamano_texto) ? (int)$campana->tamano_texto : 14;
                                    $colorTexto = $campana->color_texto ?: '#ffffff';
                                    $fuenteTexto = $campana->fuente_texto ?: 'inherit';
                                @endphp
                                <h3 class="font-semibold leading-tight" 
                                    style="font-family: {{ $fuenteTexto }}; color: {{ $colorTexto }}; font-size: {{ $tamanoTexto }}px;">
                                    {{ $campana->titulo }}
                                </h3>
                                @if($campana->descripcion)
                                <p class="mt-1 line-clamp-2" style="color: {{ $colorTexto }}; opacity: 0.8; font-size: {{ max($tamanoTexto - 2, 10) }}px;">{{ Str::limit($campana->descripcion, 60) }}</p>
                                @endif
                            </div>
                            @if($campana->archivo_pdf)
                            <a href="{{ asset('storage/' . $campana->archivo_pdf) }}" target="_blank" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors" title="Ver PDF">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                
                @if($campanas->count() >= 3)
                <div class="mt-4 text-center border-t border-gray-200 py-4">
                    <a class="text-sm font-medium hover:underline" style="color: #2c4370;" href="#">
                        Ver todas las noticias
                    </a>
                </div>
                @endif
            </div>
            
            @if($campanas->count() > 3)
            <script>
                (function() {
                    let posicionActual = 0;
                    const totalCampanas = {{ $campanas->count() }};
                    const campanasPorVista = 3;
                    const maxPosicion = totalCampanas - campanasPorVista;
                    
                    window.moverCarrusel = function(direccion) {
                        const carrusel = document.getElementById('carrusel-campanas');
                        if (!carrusel) return;
                        
                        posicionActual += direccion;
                        
                        // Limitar posición dentro del rango válido
                        if (posicionActual < 0) posicionActual = maxPosicion;
                        if (posicionActual > maxPosicion) posicionActual = 0;
                        
                        // Calcular el desplazamiento (cada campaña ocupa ~33.333% + gap)
                        const porcentajeDesplazamiento = posicionActual * (100 / campanasPorVista);
                        carrusel.style.transform = `translateX(-${porcentajeDesplazamiento}%)`;
                    };
                })();
            </script>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
