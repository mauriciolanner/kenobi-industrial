<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Robson',
            'email' => 'robson@bomix.com.br',
            'password' => Hash::make('123'),
            'user_name' => 'ROBSON',
            'status' => 1,
            'role_id' => '2',
            'filial' => '0101'
        ]);

        User::create([
            'name' => 'A P ARAGAO LOPES IND. DE ARGAMASSA EIREL',
            'email' => 'financeiroaparagao@gmail.com',
            'password' => Hash::make('123'),
            'user_name' => 'DANTAS',
            'status' => 1,
            'cnpj' => '28321470000176',
            'role_id' => '3',
            'filial' => '0101'
        ]);
    }
}
