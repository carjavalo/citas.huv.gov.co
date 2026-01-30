<div>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
    </style>
    <x-guest-layout>
        <div style="background-image: url('{{ asset('img/huv_fondo_2.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;" class="flex items-center justify-center py-12">
            <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="flex justify-center mb-6">
                    <img src="https://citas.huv.gov.co/huv-icon.png" alt="Logo HUV" class="w-20 h-20">
                </div>

                <x-jet-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombres -->
                        <div>
                            <x-jet-label for="name" value="{{ __('Nombres') }}" />
                            <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="off" />
                            @error('name') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Primer Apellido -->
                        <div>
                            <x-jet-label for="apellido1" value="{{ __('Primer apellido') }}" />
                            <x-jet-input id="apellido1" class="block mt-1 w-full" type="text" name="apellido1" :value="old('apellido1')" required autocomplete="off" />
                            @error('apellido1') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Segundo Apellido -->
                        <div>
                            <x-jet-label for="apellido2" value="{{ __('Segundo apellido') }}" />
                            <x-jet-input id="apellido2" class="block mt-1 w-full" type="text" name="apellido2" :value="old('apellido2')" autocomplete="off" />
                            @error('apellido2') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Tipo de Documento -->
                        <div>
                            <x-jet-label for="tdocumento" value="{{ __('Tipo de documento') }}" />
                            <select id="tdocumento" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="tdocumento" autocomplete="off">
                                <option value="">Seleccione...</option>
                                @foreach ($tipo_idenficacion as $tipo_id)
                                    <option value="{{ $tipo_id->id }}" {{ old('tdocumento') == $tipo_id->id ? 'selected' : '' }}>{{ $tipo_id->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tdocumento') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Número de Documento -->
                        <div>
                            <x-jet-label for="ndocumento" value="{{ __('Número de documento') }}" />
                            <x-jet-input id="ndocumento" class="block mt-1 w-full" type="number" name="ndocumento" :value="old('ndocumento')" required autocomplete="off"/>
                            @error('ndocumento') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- EPS -->
                        <div>
                            <x-jet-label for="eps" value="{{ __('Seleccione su EPS') }}" />
                            <select id="eps" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="eps" autocomplete="off">
                                <option value="">Seleccione...</option>
                                @foreach ($aseguradoras as $eps)
                                    <option value="{{ $eps->id }}" {{ old('eps') == $eps->id ? 'selected' : '' }}>{{ $eps->nombre }}</option>
                                @endforeach
                            </select>
                            @error('eps') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Teléfono 1 -->
                        <div>
                            <x-jet-label for="telefono1" value="{{ __('Teléfono 1') }}" />
                            <x-jet-input id="telefono1" class="block mt-1 w-full" type="number" name="telefono1" :value="old('telefono1')" autocomplete="off" />
                            @error('telefono1') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Teléfono 2 -->
                        <div>
                            <x-jet-label for="telefono2" value="{{ __('Teléfono 2') }}" />
                            <x-jet-input id="telefono2" class="block mt-1 w-full" type="number" name="telefono2" :value="old('telefono2')" autocomplete="off" />
                            @error('telefono2') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>
                    </div>

                    <!-- Correo -->
                    <div class="mt-4">
                        <x-jet-label for="email" value="{{ __('Correo electrónico') }}" />
                        <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" oncopy="return false" onpaste="return false" required autocomplete="off"/>
                        @error('email') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                    </div>

                    <!-- Confirmar Correo -->
                    <div class="mt-4">
                        <x-jet-label for="email_confirmation" value="{{ __('Confirmar correo') }}" />
                        <x-jet-input id="email_confirmation" class="block mt-1 w-full" type="email" name="email_confirmation" oncopy="return false" onpaste="return false" :value="old('email_confirmation')" required autocomplete="off"/>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Contraseña -->
                        <div>
                            <x-jet-label for="password" value="{{ __('Contraseña') }}" />
                            <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" oncopy="return false" onpaste="return false" required autocomplete="new-password" />
                            @error('password') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <x-jet-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
                            <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" oncopy="return false" onpaste="return false" required autocomplete="off" />
                            @error('password_confirmation') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <x-jet-label for="terms">
                                <div class="flex items-center">
                                    <x-jet-checkbox name="terms" id="terms"/>

                                    <div class="ml-2">
                                        {!! __('Acepto los terminos :terms_of_service y :privacy_policy', [
                                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Términos del servicio').'</a>',
                                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Política de Privacidad').'</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-jet-label>
                        </div>
                        @error('terms') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                    @endif

                    <div class="flex items-center justify-end mt-4">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                            {{ __('¿Ya estás registrado?') }}
                        </a>

                        <x-jet-button class="ml-4">
                            {{ __('Registrarme') }}
                        </x-jet-button>
                    </div>
                </form>
            </div>
        </div>
    </x-guest-layout>
</div>
