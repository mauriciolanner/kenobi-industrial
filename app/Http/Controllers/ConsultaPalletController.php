<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaPalletController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render(
            'NotLoginPages/ConsultaOP',
            []
        );
    }

    public function indexSaldo(Request $request)
    {
        return Inertia::render(
            'NotLoginPages/SaldosMX',
            []
        );
    }

    public function APIVerificaPalet(Request $request)
    {
        $dados = null;
        $pallet = null;

        if ($request->op != '') {
            if (strlen($request->op) > 6)
                $pallet = substr($request->op, 11, 6);
            else
                $pallet = $request->op;

            $dados =  DB::connection('protheus')->select(DB::raw("Exec P12Oficial.dbo.[Bomix_Rastreia_Palete] '$pallet'"));

            return response()->json([
                'pallet' => $dados,
                'status' => true
            ]);
        }

        return response()->json([
            'pallet' => $dados,
            'status' => false
        ]);
    }

    public function APIConsultaSaldoMX(Request $request)
    {
        $dados =  DB::connection('protheus')->select(DB::raw("SELECT * FROM BMX_VW_Compara_Lotes_Protheus_Mecalux where CODIGO = '$request->codigo'"));

        return response()->json($dados);
    }
}
