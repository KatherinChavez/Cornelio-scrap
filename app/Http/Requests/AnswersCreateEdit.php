<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswersCreateEdit extends FormRequest
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
            'page_id' =>  'required',
            'respuesta' =>  'required',
        ];

    }
    public function messages()
    {
        return [
            'page_id.required' =>  'Seleccioná una página',
            'respuesta.required' =>  'Ingresá una respuesta',
        ];
    }
}
