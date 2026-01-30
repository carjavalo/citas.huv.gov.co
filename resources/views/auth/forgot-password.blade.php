<x-guest-layout>
    <div style="background-image: url('{{ asset('img/huv_fondo_2.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;" class="flex items-center justify-center">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="https://citas.huv.gov.co/huv-icon.png" alt="Logo HUV" class="w-20 h-20">
            </div>

            <div class="mb-4 text-sm text-gray-600">
                {{ __('¿Olvidó su contraseña? Digite su dirección de correo electrónico y le enviaremos un enlace para asignar una nueva contraseña.') }}
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <x-jet-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="block">
                    <x-jet-label for="email" value="{{ __('Correo') }}" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        {{ __('Atrás') }}
                    </a>

                    <x-jet-button>
                        {{ __('Restablecer mi contraseña') }}
                    </x-jet-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
