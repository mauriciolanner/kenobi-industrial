<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmpEmpresaRequest extends FormRequest
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
            'emp_nom_empresa' => 'required|max:200',
            'emp_dti_atividade' => 'required|date',
            'emp_dtf_atividade' => 'date',
            'emp_especial' => 'boolean',
        ];
    }
}
