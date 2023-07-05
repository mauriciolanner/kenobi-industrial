<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Form;

class FormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Form::create([
            'name' => 'Controle de visita',
            'status' => '1',
            'slung' => 'ControleVisita'
        ]);

        Form::create([
            'name' => 'Fechamento de Reembolso',
            'status' => '1',
            'slung' => 'FechamentoReembolso'
        ]);
    }
}
