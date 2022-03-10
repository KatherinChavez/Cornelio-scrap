<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Companies_usersEditProfile extends FormRequest
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
            'email' =>  'email:rfc,dns',
        ];

    }
    public function messages()
    {
        return [
            'email.email' =>  'Debés ingresar un email de usuario con el formato correcto',
        ];
    }
}
