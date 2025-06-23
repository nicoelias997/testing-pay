<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email'
                ],
            'password'  => [
                'required',
                'min:8'
                ]
        ];
    }

    public function authenticate()
    {
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                // 'login' => __('auth.failed')  ?? 'La contraseña es incorrecta.'
                'login' => __('auth.failed')  ?? 'La contraseña es incorrecta.'
            ]);
        }
    }
    // public function cheackState()
    // {
    //     // Verifica si el usuario tiene acceso según tus condiciones específicas
    //     if (!auth()->user()->state) {
    //         Auth::logout(); // Cierra la sesión si el acceso es denegado
    //         throw ValidationException::withMessages([
    //             'login' => ['Tus permisos de acceso no están concedidos.'],
    //         ]);
    //     }
    // }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Debe ingresar un email válido',
            'email.exists' => 'El email introducido es incorrecto.',
            'password.required' => 'Debe ingresar una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ];
    }
}
