<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ImpressaoMecalux;

class MecaluxController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render(
            'EtiquetaMecalux/Index',
            [
                //'consultas' => $this->consulta($request->busca)
            ]
        );
    }

    public function APIMecaluxRecurso(Request $request)
    {
        $etiquetas = ImpressaoMecalux::where('IMPRESSO', '0')->orderBy('APONTAMENTO_MES', 'DESC')->paginate(10);
        return response()->json($etiquetas);
    }
}
