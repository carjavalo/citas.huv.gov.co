<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Sistema de Citas</title>
    <!-- Tailwind CSS desde CDN para asegurar visualización sin compilar assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #f3f4f6;
        }
        .corp-bg {
            background-color: #2e3a75;
        }
        .corp-text {
            color: #2e3a75;
        }
        .corp-gradient {
            background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        }
        .btn-corp {
            background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        }
        .btn-corp:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="max-w-xl w-full bg-white rounded-xl shadow-2xl overflow-hidden relative">
        <!-- Banner superior con gradiente corporativo -->
        <div class="h-3 corp-gradient"></div>

        <div class="p-8 sm:p-12 text-center">
            
            <!-- Logo Corporativo -->
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('img/logoinicio.gif') }}" alt="Logo Institucional" class="h-24 object-contain">
            </div>
            
            <!-- Icono de Mantenimiento (opcional/decorativo) -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 mb-6">
                <!-- Usando el color corporativo para el icono -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 corp-text" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-extrabold corp-text tracking-tight sm:text-4xl mb-4">
                Sitio en Mantenimiento Temporal
            </h1>
            
            <div class="text-lg text-gray-600 mb-8 leading-relaxed">
                <p class="mb-4">Ofrecemos disculpas por los inconvenientes causados.</p>
                <div class="bg-blue-50 border-l-4 border-[#2e3a75] p-4 text-left rounded-r">
                    <p class="text-gray-700 text-base italic">
                        @if($exception->getMessage())
                            "{{ $exception->getMessage() }}"
                        @else
                            "Estamos realizando mejoras en nuestra plataforma para brindarle un mejor servicio. Por favor, intente ingresar nuevamente más tarde."
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <!-- Botón de recarga -->
                <button onclick="window.location.reload()" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white btn-corp focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2e3a75] transition duration-150 ease-in-out w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Volver a intentar
                </button>

                <a href="{{ url('/') }}" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2e3a75] transition duration-150 ease-in-out w-full sm:w-auto">
                    Ir al Inicio
                </a>
            </div>
        </div>
        
        <!-- Pie de tarjeta -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-center border-t border-gray-200">
            <span class="text-xs text-gray-500 text-center">&copy; {{ date('Y') }} Hospital Universitario del Valle. Todos los derechos reservados.</span>
        </div>
    </div>
</body>
</html>