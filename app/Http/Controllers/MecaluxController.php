<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ImpressaoMecalux;
use Illuminate\Support\Facades\DB;
use App\Force\PDFCode39;
use App\Force\PDFCode128;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Carbon;

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
        DB::statement("EXEC dbo.KenobiAtualizaImpressoesMecalux");

        $etiquetas = ImpressaoMecalux::where('IMPRESSO', '0')
            ->where('RECURSO', $request->recurso)
            ->orderBy('APONTAMENTO_MES', 'DESC')->paginate(10);

        return response()->json($etiquetas);
    }

    public function apontamentoPdf($cod)
    {
        $linha = ImpressaoMecalux::where('CODIGO_APONTAMENTO', $cod)->first();

        $now = Carbon::now();
        $produto = DB::connection('protheus')->select("SELECT * FROM SB1010 WHERE B1_COD = '$linha->PRODUTO'");

        $pdf = new PDFCode128('L', 'mm', [100, 40]);
        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(100/*width*/, 8/*height*/, "BOMIX | " . $now->format('d') . ' ' .  $this->mes($now->format('m')) . ' ' .  $now->format('Y') . ' ' . $now->isoFormat('h:mm') . ' | RECEITA: ' .  $linha->RECEITA/*String*/, 'B'/*Border*/, 1/*ln*/, 'C'/*alinhamento*/, false);

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Code128(10/*x*/, 14/*y*/, $linha->OP . $linha->CODIGO_APONTAMENTO, 80/*width*/, 15/*height*/);
        $pdf->Cell(100, 6, $linha->OP . $linha->CODIGO_APONTAMENTO, 0, 0, "C");

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(3, 30);
        $pdf->Cell(100, 10, $produto[0]->B1_DESC, 0, 1, "");

        ImpressaoMecalux::where('id', $linha->id)->update([
            'IMPRESSO' => 1
        ]);
        
        $pdf->Output();
        exit;
    }

    static function mes($mes)
    {
        $retorno = '';
        ($mes == '01') ? $retorno = 'Jan' : '';
        ($mes == '02') ? $retorno = 'Fev' : '';
        ($mes == '03') ? $retorno = 'Mar' : '';
        ($mes == '04') ? $retorno = 'Abr' : '';
        ($mes == '05') ? $retorno = 'Mai' : '';
        ($mes == '06') ? $retorno = 'Jun' : '';
        ($mes == '07') ? $retorno = 'Jul' : '';
        ($mes == '08') ? $retorno = 'Ago' : '';
        ($mes == '09') ? $retorno = 'Set' : '';
        ($mes == '10') ? $retorno = 'Out' : '';
        ($mes == '11') ? $retorno = 'Nov' : '';
        ($mes == '12') ? $retorno = 'Dez' : '';

        return $retorno;
    }
}
