<div class="font-body bg-gray-100 min-h-screen">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        .font-body { font-family: 'Inter', sans-serif; }
        
        /* Estilos para el carrusel automático */
        .campana-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .campana-card:hover {
            transform: translateY(-2px);
        }
        
        /* Animación de entrada para las campañas */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .campana-card {
            animation: slideInRight 0.3s ease-out;
        }
        
        /* Indicador de carrusel activo */
        .carrusel-activo {
            position: relative;
        }
        
        .carrusel-activo::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
            background-size: 200% 100%;
            border-radius: 1rem;
            z-index: -1;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>

    {{-- Mensajes Flash --}}
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <span class="material-icons mr-2">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
            <div class="flex items-center text-sm">
                <span class="text-slate-500">Configuración</span>
                <span class="material-icons text-xs mx-2 text-slate-400">chevron_right</span>
                <span class="text-[#2c4370] font-medium">Gestión de Campañas</span>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#2c4370]">Gestión de Banners y Noticias</h1>
                <p class="text-slate-500 mt-1">Administra las campañas visibles en la pantalla principal del hospital.</p>
            </div>
            <div class="flex space-x-3">
                <button wire:click="abrirModalCrear" class="inline-flex items-center px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg text-sm font-medium transition-colors shadow-md">
                    <span class="material-icons text-base mr-2">add</span>
                    Nueva Campaña
                </button>
            </div>
        </div>

        {{-- Panel de Control de Sección --}}
        <div class="mb-6 bg-gradient-to-r {{ $seccionHabilitada ? 'from-green-50 to-emerald-50 border-green-200' : 'from-red-50 to-orange-50 border-red-200' }} border rounded-xl p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full {{ $seccionHabilitada ? 'bg-green-100' : 'bg-red-100' }} mr-4">
                        <span class="material-icons text-2xl {{ $seccionHabilitada ? 'text-green-600' : 'text-red-600' }}">
                            {{ $seccionHabilitada ? 'visibility' : 'visibility_off' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Sección "Novedades y Campañas" en Vista Principal</h3>
                        <p class="text-sm {{ $seccionHabilitada ? 'text-green-600' : 'text-red-600' }}">
                            {{ $seccionHabilitada ? 'La sección está HABILITADA y visible para los usuarios' : 'La sección está DESHABILITADA y oculta para los usuarios' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="mr-3 text-sm font-medium {{ $seccionHabilitada ? 'text-green-700' : 'text-red-700' }}">
                        {{ $seccionHabilitada ? 'Habilitada' : 'Deshabilitada' }}
                    </span>
                    <button wire:click="toggleSeccionNovedades" 
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $seccionHabilitada ? 'bg-green-500 focus:ring-green-500' : 'bg-red-400 focus:ring-red-400' }}">
                        <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform {{ $seccionHabilitada ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Panel de Control de Carrusel Automático --}}
        <div class="mb-6 bg-gradient-to-r {{ $carruselAutomatico ? 'from-blue-50 to-indigo-50 border-blue-200' : 'from-gray-50 to-slate-50 border-gray-200' }} border rounded-xl p-4 shadow-sm">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $carruselAutomatico ? 'bg-blue-100' : 'bg-gray-100' }} mr-4">
                            <span class="material-icons text-2xl {{ $carruselAutomatico ? 'text-blue-600' : 'text-gray-600' }}">
                                {{ $carruselAutomatico ? 'play_circle' : 'pause_circle' }}
                            </span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Carrusel Automático de Campañas</h3>
                            <p class="text-sm {{ $carruselAutomatico ? 'text-blue-600' : 'text-gray-600' }}">
                                {{ $carruselAutomatico ? 'Las campañas se desplazan automáticamente cada ' . $intervaloCarrusel . ' segundos' : 'El carrusel está en modo manual' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-3 text-sm font-medium {{ $carruselAutomatico ? 'text-blue-700' : 'text-gray-700' }}">
                            {{ $carruselAutomatico ? 'Automático' : 'Manual' }}
                        </span>
                        <button wire:click="toggleCarruselAutomatico" 
                                class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $carruselAutomatico ? 'bg-blue-500 focus:ring-blue-500' : 'bg-gray-400 focus:ring-gray-400' }}">
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform {{ $carruselAutomatico ? 'translate-x-7' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
                
                {{-- Selector de Intervalo --}}
                <div class="flex items-center gap-4 border-t border-gray-200 pt-4">
                    <label class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="material-icons text-sm mr-2">schedule</span>
                        Intervalo de cambio:
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="range" 
                               wire:model.live="intervaloCarrusel" 
                               min="3" 
                               max="30" 
                               step="1"
                               class="w-48 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <span class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full min-w-[60px] text-center">
                            {{ $intervaloCarrusel }}s
                        </span>
                    </div>
                    <span class="text-xs text-gray-500">(3-30 segundos)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Lista de Campañas --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Estadísticas --}}
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Activos</p>
                            <p class="text-2xl font-bold text-green-600">{{ $totalActivos }}</p>
                        </div>
                        <span class="material-icons text-green-200 text-4xl">check_circle</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Programados</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $totalProgramados }}</p>
                        </div>
                        <span class="material-icons text-blue-200 text-4xl">schedule</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Total</p>
                            <p class="text-2xl font-bold text-slate-600">{{ $totalCampanas }}</p>
                        </div>
                        <span class="material-icons text-slate-200 text-4xl">inventory_2</span>
                    </div>
                </div>

                {{-- Filtros --}}
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input type="text" wire:model.live.debounce.300ms="busqueda" placeholder="Buscar campaña..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <select wire:model.live="filtroEstado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Todos los estados</option>
                                <option value="borrador">Borrador</option>
                                <option value="publicado">Publicado</option>
                                <option value="programado">Programado</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="filtroCategoria" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Todas las categorías</option>
                                <option value="campana">Campaña</option>
                                <option value="noticia">Noticia</option>
                                <option value="urgente">Urgente</option>
                                <option value="servicio">Servicio</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Listado de Campañas --}}
                @forelse($campanas as $campana)
                <div class="campana-card bg-white rounded-xl p-4 shadow-sm border border-gray-200 flex flex-col sm:flex-row gap-4 group hover:border-blue-400 transition-colors {{ $campana->estado !== 'publicado' ? 'opacity-75' : '' }}" data-campana-id="{{ $campana->id }}">
                    <div class="relative w-full sm:w-48 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                        @if($campana->imagen)
                            <img alt="{{ $campana->titulo }}" class="w-full h-full object-cover {{ $campana->estado !== 'publicado' ? 'filter grayscale' : '' }}" 
                                 src="{{ asset('storage/' . $campana->imagen) }}"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <span class="material-icons text-gray-400 text-4xl">image</span>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2 
                            @if($campana->estado === 'publicado') bg-green-500 
                            @elseif($campana->estado === 'programado') bg-blue-500 
                            @else bg-gray-500 @endif 
                            text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                            {{ ucfirst($campana->estado) }}
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-lg text-slate-800" style="color: {{ $campana->color_texto }}; font-family: {{ $campana->fuente_texto }}; font-size: {{ $campana->tamano_texto }};">
                                    {{ $campana->titulo }}
                                </h3>
                                <div class="flex items-center space-x-1">
                                    <button wire:click="abrirModalEditar({{ $campana->id }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Editar">
                                        <span class="material-icons text-lg">edit</span>
                                    </button>
                                    <button wire:click="confirmarEliminar({{ $campana->id }})" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Eliminar">
                                        <span class="material-icons text-lg">delete</span>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $campana->descripcion }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3 text-xs text-slate-500 border-t border-gray-100 pt-3">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <span class="material-icons text-sm mr-1">calendar_today</span> 
                                    {{ $campana->fecha_inicio ? $campana->fecha_inicio->format('d M') : 'Sin fecha' }} - {{ $campana->fecha_fin ? $campana->fecha_fin->format('d M') : 'Sin fecha' }}
                                </span>
                                <span class="flex items-center px-2 py-0.5 rounded
                                    @if($campana->categoria === 'urgente') text-red-600 bg-red-50
                                    @elseif($campana->categoria === 'noticia') text-purple-600 bg-purple-50
                                    @elseif($campana->categoria === 'servicio') text-green-600 bg-green-50
                                    @else text-blue-600 bg-blue-50 @endif">
                                    {{ ucfirst($campana->categoria) }}
                                </span>
                                @if($campana->archivo_pdf)
                                    <a href="{{ asset('storage/' . $campana->archivo_pdf) }}" target="_blank" class="flex items-center text-red-500 hover:text-red-700">
                                        <span class="material-icons text-sm mr-1">picture_as_pdf</span> PDF
                                    </a>
                                @endif
                            </div>
                            <label class="flex items-center cursor-pointer">
                                <span class="mr-2 text-slate-400">Visible</span>
                                <div class="relative">
                                    <input type="checkbox" wire:click="toggleVisible({{ $campana->id }})" {{ $campana->visible ? 'checked' : '' }} class="sr-only"/>
                                    <div class="block {{ $campana->visible ? 'bg-green-500' : 'bg-gray-300' }} w-10 h-6 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition {{ $campana->visible ? 'transform translate-x-4' : '' }}"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200 text-center">
                    <span class="material-icons text-gray-300 text-6xl mb-4">campaign</span>
                    <p class="text-gray-500">No hay campañas registradas</p>
                    <button wire:click="abrirModalCrear" class="mt-4 inline-flex items-center px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg text-sm font-medium">
                        <span class="material-icons text-base mr-2">add</span> Crear primera campaña
                    </button>
                </div>
                @endforelse

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $campanas->links() }}
                </div>
            </div>

            {{-- Panel Editor (Formulario Nuevo) --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 sticky top-8">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-[#2c4370] flex items-center">
                            <span class="material-icons text-[#1e3a8a] mr-2">edit_note</span>
                            Editor Rápido
                        </h2>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-medium">Info</span>
                    </div>
                    <div class="p-6 space-y-4 text-sm text-gray-600">
                        <p><span class="material-icons text-sm align-middle mr-1">info</span> Haz clic en <strong>"Nueva Campaña"</strong> para crear una nueva entrada.</p>
                        <p><span class="material-icons text-sm align-middle mr-1">edit</span> Usa el botón de editar en cada campaña para modificarla.</p>
                        <p><span class="material-icons text-sm align-middle mr-1">visibility</span> El toggle "Visible" controla si la campaña aparece en la pantalla principal.</p>
                        <p><span class="material-icons text-sm align-middle mr-1">play_circle</span> Activa el <strong>Carrusel Automático</strong> para que las campañas se desplacen cada 5 segundos.</p>
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Estados:</h4>
                            <ul class="space-y-1">
                                <li><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>Publicado: Visible ahora</li>
                                <li><span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>Programado: Se activará en fecha</li>
                                <li><span class="inline-block w-3 h-3 bg-gray-500 rounded-full mr-2"></span>Borrador: No visible</li>
                            </ul>
                        </div>
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Carrusel Automático:</h4>
                            <ul class="space-y-1 text-xs">
                                <li>• Las campañas se desplazan de izquierda a derecha</li>
                                <li>• Cambio cada 5 segundos</li>
                                <li>• Efecto visual de resaltado</li>
                                <li>• Se activa/desactiva con el toggle superior</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    @if($modalCrear)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-[#2c4370]">Nueva Campaña</h3>
                        <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="guardar" class="space-y-4">
                        {{-- Imagen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imagen de Portada</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="file" wire:model="imagen" accept="image/*" class="hidden" id="imagen-input">
                                <label for="imagen-input" class="cursor-pointer">
                                    @if($imagen)
                                        <img src="{{ $imagen->temporaryUrl() }}" class="mx-auto h-32 object-cover rounded mb-2">
                                        <p class="text-xs text-green-600">Imagen seleccionada</p>
                                    @else
                                        <span class="material-icons text-gray-400 text-4xl mb-2">cloud_upload</span>
                                        <p class="text-xs text-gray-500">Arrastra o <span class="text-blue-600 font-medium">haz clic</span></p>
                                        <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, GIF, BMP, WEBP, SVG hasta 5MB</p>
                                    @endif
                                </label>
                            </div>
                            @error('imagen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video (opcional)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">
                                <input type="file" wire:model="video" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo" class="hidden" id="video-input">
                                <label for="video-input" class="cursor-pointer">
                                    @if($video)
                                        <div class="flex items-center justify-center">
                                            <span class="material-icons text-green-500 text-3xl mr-2">videocam</span>
                                            <span class="text-xs text-green-600">Video seleccionado: {{ $video->getClientOriginalName() }}</span>
                                        </div>
                                    @else
                                        <span class="material-icons text-gray-400 text-3xl mb-1">video_library</span>
                                        <p class="text-xs text-gray-500">Arrastra o <span class="text-blue-600 font-medium">haz clic</span></p>
                                        <p class="text-[10px] text-gray-400 mt-1">MP4, WEBM, OGG, MOV, AVI hasta 50MB</p>
                                    @endif
                                </label>
                            </div>
                            @error('video') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- PDF --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Archivo PDF (opcional)</label>
                            <input type="file" wire:model="archivo_pdf" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('archivo_pdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Título --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" wire:model="titulo" placeholder="Ej: Nueva Jornada de Salud" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Categoría y Estado --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                                <select wire:model="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="campana">Campaña</option>
                                    <option value="noticia">Noticia</option>
                                    <option value="urgente">Urgente</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="borrador">Borrador</option>
                                    <option value="publicado">Publicado</option>
                                    <option value="programado">Programado</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                                <input type="date" wire:model="fecha_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                                <input type="date" wire:model="fecha_fin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @error('fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea wire:model="descripcion" rows="3" placeholder="Resumen breve para el banner..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                        </div>

                        {{-- Estilos de Texto --}}
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Estilos del Texto</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Color</label>
                                    <input type="color" wire:model="color_texto" class="mt-1 h-10 w-full rounded border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Fuente</label>
                                    <select wire:model="fuente_texto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                                        <option value="Inter">Inter</option>
                                        <option value="Arial">Arial</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                        <option value="Verdana">Verdana</option>
                                        <option value="Courier New">Courier New</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Tamaño</label>
                                    <select wire:model="tamano_texto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                                        <option value="12px">Pequeño (12px)</option>
                                        <option value="14px">Normal (14px)</option>
                                        <option value="16px">Mediano (16px)</option>
                                        <option value="18px">Grande (18px)</option>
                                        <option value="20px">Muy Grande (20px)</option>
                                        <option value="24px">Extra Grande (24px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Visible --}}
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="visible" id="visible-crear" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="visible-crear" class="ml-2 block text-sm text-gray-700">Visible en pantalla principal</label>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" wire:click="cerrarModal" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar</button>
                            <button type="submit" class="px-6 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white text-sm font-medium rounded-lg shadow-sm flex items-center">
                                <span class="material-icons text-sm mr-2">save</span> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL EDITAR --}}
    @if($modalEditar)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-[#2c4370]">Editar Campaña</h3>
                        <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="actualizar" class="space-y-4">
                        {{-- Imagen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imagen de Portada</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="file" wire:model="imagen" accept="image/*" class="hidden" id="imagen-edit-input">
                                <label for="imagen-edit-input" class="cursor-pointer">
                                    @if($imagen)
                                        <img src="{{ $imagen->temporaryUrl() }}" class="mx-auto h-32 object-cover rounded mb-2">
                                        <p class="text-xs text-green-600">Nueva imagen seleccionada</p>
                                    @elseif($imagenActual)
                                        <img src="{{ asset('storage/' . $imagenActual) }}" class="mx-auto h-32 object-cover rounded mb-2">
                                        <p class="text-xs text-gray-500">Clic para cambiar imagen</p>
                                    @else
                                        <span class="material-icons text-gray-400 text-4xl mb-2">cloud_upload</span>
                                        <p class="text-xs text-gray-500">Arrastra o <span class="text-blue-600 font-medium">haz clic</span></p>
                                    @endif
                                </label>
                            </div>
                            @error('imagen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video</label>
                            @if($videoActual)
                                <div class="mb-2">
                                    <video controls class="w-full h-24 rounded">
                                        <source src="{{ asset('storage/' . $videoActual) }}" type="video/mp4">
                                    </video>
                                    <p class="text-xs text-gray-500 mt-1">Video actual (clic abajo para cambiar)</p>
                                </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">
                                <input type="file" wire:model="video" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo" class="hidden" id="video-edit-input">
                                <label for="video-edit-input" class="cursor-pointer">
                                    @if($video)
                                        <div class="flex items-center justify-center">
                                            <span class="material-icons text-green-500 text-3xl mr-2">videocam</span>
                                            <span class="text-xs text-green-600">Nuevo video: {{ $video->getClientOriginalName() }}</span>
                                        </div>
                                    @else
                                        <span class="material-icons text-gray-400 text-3xl mb-1">video_library</span>
                                        <p class="text-xs text-gray-500">{{ $videoActual ? 'Cambiar video' : 'Subir video' }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">MP4, WEBM, OGG, MOV, AVI hasta 50MB</p>
                                    @endif
                                </label>
                            </div>
                            @error('video') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- PDF --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Archivo PDF</label>
                            @if($pdfActual)
                                <p class="text-xs text-gray-500 mb-2">PDF actual: <a href="{{ asset('storage/' . $pdfActual) }}" target="_blank" class="text-blue-600 hover:underline">Ver PDF</a></p>
                            @endif
                            <input type="file" wire:model="archivo_pdf" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('archivo_pdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Título --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" wire:model="titulo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Categoría y Estado --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                                <select wire:model="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="campana">Campaña</option>
                                    <option value="noticia">Noticia</option>
                                    <option value="urgente">Urgente</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="borrador">Borrador</option>
                                    <option value="publicado">Publicado</option>
                                    <option value="programado">Programado</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                                <input type="date" wire:model="fecha_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                                <input type="date" wire:model="fecha_fin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @error('fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea wire:model="descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                        </div>

                        {{-- Estilos de Texto --}}
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Estilos del Texto</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Color</label>
                                    <input type="color" wire:model="color_texto" class="mt-1 h-10 w-full rounded border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Fuente</label>
                                    <select wire:model="fuente_texto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                                        <option value="Inter">Inter</option>
                                        <option value="Arial">Arial</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                        <option value="Verdana">Verdana</option>
                                        <option value="Courier New">Courier New</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">Tamaño</label>
                                    <select wire:model="tamano_texto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                                        <option value="12px">Pequeño (12px)</option>
                                        <option value="14px">Normal (14px)</option>
                                        <option value="16px">Mediano (16px)</option>
                                        <option value="18px">Grande (18px)</option>
                                        <option value="20px">Muy Grande (20px)</option>
                                        <option value="24px">Extra Grande (24px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Visible --}}
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="visible" id="visible-editar" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="visible-editar" class="ml-2 block text-sm text-gray-700">Visible en pantalla principal</label>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" wire:click="cerrarModal" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar</button>
                            <button type="submit" class="px-6 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white text-sm font-medium rounded-lg shadow-sm flex items-center">
                                <span class="material-icons text-sm mr-2">save</span> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL ELIMINAR --}}
    @if($modalEliminar)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Eliminar Campaña</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar esta campaña? Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="eliminar" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Eliminar
                    </button>
                    <button wire:click="cerrarModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Script para Carrusel Automático --}}
    <script>
        (function() {
            let carruselInterval = null;
            let posicionActual = 0;
            let carruselIniciado = false;

            function iniciarCarrusel(activo, intervalo) {
                console.log('=== iniciarCarrusel llamado ===');
                console.log('Activo:', activo, 'Intervalo:', intervalo);
                
                // Limpiar intervalo anterior
                if (carruselInterval) {
                    clearInterval(carruselInterval);
                    carruselInterval = null;
                }

                // Remover efectos visuales de todas las campañas
                const todasCampanas = document.querySelectorAll('.campana-card');
                todasCampanas.forEach(c => {
                    c.style.transform = '';
                    c.style.boxShadow = '';
                    c.style.borderColor = '';
                });

                if (!activo) {
                    console.log('Carrusel detenido');
                    carruselIniciado = false;
                    return;
                }

                console.log('Iniciando carrusel con intervalo de ' + intervalo + ' segundos');
                carruselIniciado = true;

                carruselInterval = setInterval(() => {
                    const contenedor = document.querySelector('.lg\\:col-span-8 .space-y-4');
                    if (!contenedor) {
                        console.log('Contenedor no encontrado');
                        return;
                    }

                    const campanas = contenedor.querySelectorAll('.campana-card');
                    if (campanas.length === 0) {
                        console.log('No hay campañas');
                        return;
                    }

                    // Remover efectos de la campaña anterior
                    campanas.forEach(c => {
                        c.style.transform = '';
                        c.style.boxShadow = '';
                        c.style.borderColor = '';
                    });

                    // Incrementar posición
                    posicionActual = (posicionActual + 1) % campanas.length;

                    console.log('Mostrando campaña ' + (posicionActual + 1) + ' de ' + campanas.length);

                    // Desplazar suavemente a la siguiente campaña
                    campanas[posicionActual].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });

                    // Aplicar efecto visual a la campaña activa
                    campanas[posicionActual].style.transform = 'scale(1.02)';
                    campanas[posicionActual].style.boxShadow = '0 10px 25px rgba(59, 130, 246, 0.5)';
                    campanas[posicionActual].style.borderColor = '#3b82f6';
                    campanas[posicionActual].style.transition = 'all 0.5s ease';

                }, intervalo * 1000);
            }

            // Escuchar evento de Livewire 2
            window.addEventListener('carruselToggled', event => {
                console.log('=== Evento carruselToggled recibido ===');
                console.log('Event detail:', event.detail);
                const activo = event.detail[0];
                const intervalo = event.detail[1];
                iniciarCarrusel(activo, intervalo);
            });

            // Función para inicializar el carrusel al cargar
            function inicializarCarruselAlCargar() {
                const carruselActivo = @json($carruselAutomatico);
                const intervaloInicial = @json($intervaloCarrusel);
                
                console.log('=== Inicializando carrusel al cargar ===');
                console.log('Estado inicial - Activo:', carruselActivo, 'Intervalo:', intervaloInicial);
                
                if (carruselActivo && !carruselIniciado) {
                    // Esperar a que el DOM esté completamente cargado
                    setTimeout(() => {
                        const campanas = document.querySelectorAll('.campana-card');
                        console.log('Campañas encontradas:', campanas.length);
                        if (campanas.length > 0) {
                            iniciarCarrusel(carruselActivo, intervaloInicial);
                        } else {
                            console.log('No se encontraron campañas, reintentando...');
                            setTimeout(() => inicializarCarruselAlCargar(), 1000);
                        }
                    }, 1000);
                }
            }

            // Iniciar cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', inicializarCarruselAlCargar);
            } else {
                inicializarCarruselAlCargar();
            }

            // También iniciar cuando Livewire termine de renderizar
            document.addEventListener('livewire:load', function() {
                console.log('=== Livewire cargado ===');
                inicializarCarruselAlCargar();
            });

            // Limpiar al salir
            window.addEventListener('beforeunload', function() {
                if (carruselInterval) {
                    clearInterval(carruselInterval);
                }
            });
        })();
    </script>
</div>
