<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Traits\HasRoles;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use HasRoles;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required','max:60','min:2','string','regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+/'],
            'apellido1' => ['required', 'string', 'max:30','min:2'],
            'apellido2' => ['max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users','confirmed'],
            'telefono1' => ['required','digits_between:7,10','numeric'],
            'eps'   => ['required'],
            'tdocumento' => ['required', 'string'],
            'ndocumento' => ['required', 'digits_between:4,20','numeric', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ],[
            'name.required'             => 'Debes digitar tu nombre.',
            'name.regex'                => 'Contiene carácteres inválidos',
            'name.min'                  => 'El nombre debe contener mínimo 2 carácteres.',
            'name.max'                  => 'El nombre no puede superar los 30 carácteres.',
            'apellido1.required'        => 'Escriba su apellido.',
            'apellido1.max'             => 'El apellido no puede superar los 30 carácteres.',
            'apellido1.min'             => 'El apellido debe superar los 2 carácteres.',
            'apellido1.regex'           => 'Contiene carácteres inválidos.',
            'apellido2.'                => 'El apellido no puede superar los 30 caráteres.',
            'email.max'                 => 'El correo no puede superar los 30 carácteres.',
            'email.unique'              => 'Este correo ya se encuentra en uso, por favor selecciona otro.',
            'email.confirmed'           => 'Debe confirmar el correo',
            'telefono1.required'        => 'El télefono es obligatorio.',
            'telefono1.digits_between'  => 'Comprueba que el número sea válido.',
            'telefono1.numeric'         => 'Este campo solo puede contener números.',
            'tdocumentos.required'      => 'Este campo es obligatorio.',
            'ndocumento.required'       => 'Escriba su número de documento.',
            'ndocumento.digits_between' => 'Compruebe su número de documento.',
            'ndocumento.unique'         => 'Este número de documento ya se encuentra en uso.',
            'email.required'            => 'El correo es obligatorio.',
            'eps.required'              => 'Seleccione su eps',
            'password'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'La contraseña no coincide'
        ])->validate();

        $user = User::create([
            'name'          => $input['name'],
            'apellido1'     => $input['apellido1'],
            'apellido2'     => $input['apellido2'],
            'tdocumento'    => $input['tdocumento'],
            'ndocumento'    => $input['ndocumento'],
            'telefono1'     => $input['telefono1'],
            'telefono2'     => $input['telefono2'],
            'eps'           => $input['eps'],
            'email'         => strtolower($input['email']),
            'password'      => Hash::make($input['password']),
        ]);

        $user->assignRole('Usuario');

        // Auto-verificar correo si es antes del 23/12/2025
        if (now()->lt('2025-12-23 00:00:00')) {
            $user->markEmailAsVerified();
        }

        return $user;
    }
}
