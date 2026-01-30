<x-guest-layout>
    <!-- Particles.js container -->
    <div id="particles-js" style="position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: 0; background: linear-gradient(135deg, #2c4370 0%, #1e2f4d 100%);"></div>
    
    <section class="h-screen relative" style="z-index: 1;">
        <div class="px-6 h-full">
            <div class="flex xl:justify-center lg:justify-between justify-center items-center flex-wrap h-full g-6">   
                <div class="grow-0 shrink-1 md:shrink-0 basis-auto xl:w-6/12 lg:w-6/12 md:w-9/12 mb-12 md:mb-0 flex justify-center">
                    <img src="{{ asset('img/logoinicio.gif') }}" class="max-w-md w-full" style="max-height: 480px; object-fit: contain;" alt="Logo HUV">
                </div>
                <div class="xl:ml-20 xl:w-5/12 lg:w-5/12 md:w-8/12 mb-12 md:mb-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="flex items-center my-4 before:flex-1 before:border-t before:border-gray-300 before:mt-0.5 after:flex-1 after:border-t after:border-gray-300 after:mt-0.5">
                            <p class="text-center font-semibold mx-4 mb-0 text-4xl text-white">Iniciar sesión</p>
                        </div>
                        <x-jet-validation-errors class="mb-4 text-center" />

                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        <!-- Email input -->
                        <div class="mb-6">
                            <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" placeholder="Correo electrónico" :value="old('email')" required autofocus />
                        </div>

                        <!-- Password input -->
                        <div class="mb-6">
                            <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Contraseña" required autocomplete="current-password" />
                        </div>

                        <div class="flex sm:justify-center md:justify-end lg:justify-end items-center mb-6">
                            <a href="{{ route('password.request') }}" class="text-white hover:text-gray-200">¿Olvidó su contraseña?</a>
                        </div>

                        <div class="text-center lg:text-left">
                            <div class="flex items-center justify-center mt-4">
                                <x-jet-button class="ml-4">
                                    {{ __('Iniciar sesión') }}
                                </x-jet-button>
                            </div>
                                <p class="text-base font-semibold mt-2 pt-1 mb-0 text-white">
                                    Si usted es usuario nuevo
                                    <a href="{{ route('registro') }}" class="text-yellow-300 hover:text-yellow-200 focus:text-yellow-200 transition duration-200 ease-in-out">Regístrese aquí</a>
                                </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Particles.js Script -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ffffff"
                },
                "shape": {
                    "type": "circle"
                },
                "opacity": {
                    "value": 0.5,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 2,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.4,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 2,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "grab"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 140,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "push": {
                        "particles_nb": 4
                    }
                }
            },
            "retina_detect": true
        });
    </script>
</x-guest-layout>