<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Form;
use App\Models\StatusForm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::create([
            'name' => 'Administrador Master',
            'status' => 1,
        ]);

        User::create([
            'name' => 'Admin Master',
            'email' => 'admin@bomix.com.br',
            'password' => Hash::make('admin'),
            'user_name' => 'admin',
            'status' => 1,
            'role_id' => $roleAdmin->id,
        ]);

        Form::create([
            'name' => 'Colicitação de compras',
            'status' => '1',
            'slung' => 'SolicitacaoCompras'
        ]);

        Role::create([
            'name' => 'Usuario',
            'status' => 1,
        ]);

        StatusForm::create([
            'name' => 'Cancelado'
        ]);

        StatusForm::create([
            'name' => 'Salvo esperando envio para aprovação'
        ]);

        StatusForm::create([
            'name' => 'Enviado para aprovação, aguardando fluxo'
        ]);

        StatusForm::create([
            'name' => 'Finalizado'
        ]);
    }
}
