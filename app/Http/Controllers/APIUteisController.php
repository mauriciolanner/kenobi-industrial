<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Funcionario;

class APIUteisController extends Controller
{

    public function usuarios(Request $request)
    {
        $users = User::where('name', 'LIKE', '%' . $request->busca . '%')->where('status', 1)->get();
        return response()->json($users);
    }

    public function roles(Request $request)
    {
        $users = Role::where('name', 'LIKE', '%' . $request->busca . '%')->where('status', 1)->get();
        return response()->json($users);
    }

    public function funcionarios(Request $request)
    {
        $funcionarios = Funcionario::where('CODCOLIGADA', '1')->with('funcao', 'secao', 'horario', 'beneficios', 'centroCusto')->whereNull('DATADEMISSAO'); //->take(10);

        if ($request->buscar != '') {
            $funcionarios->take(10)
                ->where('NOME', 'like', '%' . $request->buscar . '%')
                ->orWhere('CHAPA', 'like', '%' . $request->buscar . '%')->where('CODCOLIGADA', '1');
        }

        return response()->json($funcionarios->orderBy('NOME', 'ASC')->get());
    }
}
