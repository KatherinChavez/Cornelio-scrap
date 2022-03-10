<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Companies_usersEdit extends FormRequest
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
            'companies' =>  'required',
        ];

    }
    public function messages()
    {
        return [
            'name.required' =>  'Ingresá el nombre del usuario',
            'last_name.required' =>  'Ingresá el primer apellido del usuario',
            'companies.required' =>  'Debés seleccionar la empresa del usuario',
        ];
    }
}
