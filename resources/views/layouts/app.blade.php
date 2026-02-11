<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ 'Citas HUV', config('app.name') }}</title>
        <link rel="icon" href="{{ asset('img/favicon2.png') }}" type="image/png">
        <style> 
            :root {
                --color-corporativo: #2c4370;
                --color-corporativo-hover: #1e2f4d;
            }
            .imagen{
                background-image: url("{{asset('img/huv_fondo_2.jpg')}}"); 
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
            }
            .bg-corporativo {
                background-color: #2c4370;
            }
            .text-corporativo {
                color: #2c4370;
            }
            .border-corporativo {
                border-color: #2c4370;
            }
            .btn-corporativo {
                background-color: #2c4370;
                color: white;
            }
            .btn-corporativo:hover {
                background-color: #1e2f4d;
            }
            /* Estilos para asegurar que el botón de SweetAlert2 sea siempre visible */
            .swal2-confirm {
                opacity: 1 !important;
                visibility: visible !important;
                background-color: #3085d6 !important;
                color: white !important;
            }
            .swal2-actions {
                opacity: 1 !important;
                visibility: visible !important;
            }
        </style>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body class="font-sans antialiased">
     
              
        <x-jet-banner />
        

        <div class="min-h-screen bg-gray-100"> <!-- //"min-h-screen bg-gray-100"-->
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="imagen">
                {{ $slot }}
            </main>
        </div>
       
        @stack('modals')

    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Livewire.on('alertSuccess', function(message,modal){
            Swal.fire({
                title: 'Acción realizada',
                text: message,
                icon: 'success',
                confirmButtonText: "Ok",
                confirmButtonColor: '#3085d6'
            })
        });
        Livewire.on('alertSuccessCita', function(message,modal){
            Swal.fire({
                title: 'Acción realizada',
                text: message,
                icon: 'success',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Ok",
                confirmButtonColor: '#06a94d'
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{ route('dashboard') }}"
                }
            })
        });
        Livewire.on('alertError', function(message){
            Swal.fire(
            'Ocurrió un error',
            message,
            'error'
            )
        });
        Livewire.on('delete', function(message, id){
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                    )
                }
            });
        });
        Livewire.on('error', function(message){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
        });
        Livewire.hook('message.failed', (message, component) => {
            if (message.status === 419) {
                location.reload();
            }
        });
    </script>
    <script>
    window.addEventListener('alert', event => { 
        toastr[event.detail.type](event.detail.message, 
        event.detail.title ?? ''), toastr.options = {
            "closeButton": true,
        }
    });
    </script>
    </body>
</html>
