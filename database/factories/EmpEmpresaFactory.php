<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmpEmpresa;
use Carbon\Carbon;

class EmpEmpresaFactory extends Factory
{
    protected $model = EmpEmpresa::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = Carbon::now();
        $emp_nom_empresaArray = ['Boom Digital', 'Auto DNA', 'Maramar', 'Superdent', 'Veggian', 'Asus', 'Itautec', 'Lenovo', 'Epson', 'HP'];
        $emp_nom_empresa = $emp_nom_empresaArray[rand(0, count($emp_nom_empresaArray) - 1)];
        $dataAleatoria = $date->addWeeks(rand(1, 52))->format('Y-m-d H:i:s');
        $especial = rand(1,0);
        return [
            'emp_nom_empresa' => $emp_nom_empresa,
            'emp_dti_atividade' => $dataAleatoria,
            'emp_dtf_atividade' => $dataAleatoria,
            'emp_especial' => $especial
        ];
    }
}
