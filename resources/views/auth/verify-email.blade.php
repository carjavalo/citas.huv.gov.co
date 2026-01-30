<x-guest-layout>
    <div style="background-image: url('{{ asset('img/huv_fondo_2.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;" class="flex items-center justify-center">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="https://citas.huv.gov.co/huv-icon.png" alt="Logo HUV" class="w-20 h-20">
            </div>

            <div class="mb-4 text-sm text-gray-600">
                {{ __('¡Gracias por registrarte! Antes de que comiences, debes verificar tu cuenta haciendo click en el enlace que te hemos enviado. Si no recibiste el correo, te enviaremos otro.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ __('Un nuevo enlace de verificación ha sido enviado a la dirección de correo electrónico que registraste.') }}
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <x-jet-button type="submit">
                            {{ __('Reenviar correo de verificación') }}
                        </x-jet-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Cerrar sesión') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
