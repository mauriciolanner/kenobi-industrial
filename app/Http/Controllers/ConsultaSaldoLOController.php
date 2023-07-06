<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\ConsultaSaldoLO;

class ConsultaSaldoLOController extends Controller
{
    public function index(Request $request)
    {
        //dd($request);
        //dd($this->consulta());
        return Inertia::render(
            'ConsultaSaldo/Index',
            [
                'asset' => asset(''),
                'consultas' => $this->consulta($request->produto_id,$request->lote)         
            ]
        );
    }

    static function consulta($produto_id, $lote)
    {
        $sql = "SELECT B8_SALDO AS 'Saldo' from SB8010 (NOLOCK) WHERE B8_PRODUTO = '{$produto_id}' AND B8_LOCAL='PR' AND B8_LOTECTL = '{$lote}' AND B8_FILIAL = '020101' AND D_E_L_E_T_=''";

        $dados = DB::connection('protheus')->select($sql);

        return $dados;
    }
}
