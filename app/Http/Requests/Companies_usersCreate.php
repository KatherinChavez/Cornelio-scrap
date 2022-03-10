<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Companies_usersCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' =>  'required',
            'last_name' =>  'required',
            'email' =>  'required|unique:users|email:rfc,dns',
            'companies' =>  'required',
        ];

    }
    public function messages()
    {
        return [
            'name.required' =>  'Ingresá el nombre del usuario',
            'last_name.required' =>  'Ingresá el primer apellido del usuario',
            'email.required' =>  'Ingresá el email del usuario',
            'email.unique' =>  'Debés ingresar un email de usuario que no esté registrado',
            'email.email' =>  'Debés ingresar un email de usuario con el formato correcto',
            'companies.required' =>  'Debés seleccionar la empresa del usuario',
        ];
    }
}
