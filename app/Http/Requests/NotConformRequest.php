<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class NotConformRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lote' => 'required|max:200',
            'note' => 'required',
            'nota' => 'required',
            'qtd' => 'required|integer',
            'item' => 'required|max:200',
            //'user' => 'exists:users,id'
        ];
    }
}
