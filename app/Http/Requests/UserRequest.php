<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
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
            'name' => 'required|max:200',
            'user_name' => 'required|max:200',
            //'cnpj' => 'exists:SA1010,A1_CGC',
            'role_id' => 'max:200',
            'email' => 'required|email',
            'password' => 'required|max:200',
            'user_id' => 'max:200',
        ];
    }
}
