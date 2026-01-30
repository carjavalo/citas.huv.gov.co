<x-guest-layout>
    <div style="background-image: url('{{ asset('img/huv_fondo_2.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;" class="flex items-center justify-center">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="https://citas.huv.gov.co/huv-icon.png" alt="Logo HUV" class="w-20 h-20">
            </div>

            <div x-data="{ recovery: false }">
                <div class="mb-4 text-sm text-gray-600" x-show="! recovery">
                    {{ __('Confirme el acceso a su cuenta ingresando el código de autenticación proporcionado por su aplicación de autenticación.') }}
                </div>

                <div class="mb-4 text-sm text-gray-600" x-show="recovery">
                    {{ __('Confirme el acceso a su cuenta ingresando uno de sus códigos de recuperación de emergencia.') }}
                </div>

                <x-jet-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('two-factor.login') }}">
                    @csrf

                    <div class="mt-4" x-show="! recovery">
                        <x-jet-label for="code" value="{{ __('Código') }}" />
                        <x-jet-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                    </div>

                    <div class="mt-4" x-show="recovery">
                        <x-jet-label for="recovery_code" value="{{ __('Código de recuperación') }}" />
                        <x-jet-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                                        x-show="! recovery"
                                        x-on:click="
                                            recovery = true;
                                            $nextTick(() => { $refs.recovery_code.focus() })
                                        ">
                            {{ __('Usar un código de recuperación') }}
                        </button>

                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                                        x-show="recovery"
                                        x-on:click="
                                            recovery = false;
                                            $nextTick(() => { $refs.code.focus() })
                                        ">
                            {{ __('Usar un código de autenticación') }}
                        </button>

                        <x-jet-button class="ml-4">
                            {{ __('Iniciar sesión') }}
                        </x-jet-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
