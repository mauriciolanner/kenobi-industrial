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
        $recursos = [];
        $todosRecursos = DB::connection('protheus')->select('select Code from PCF4.dbo.TBLResource where FlgEnable = 1 order by Code');

        foreach ($todosRecursos as $rec) {
            array_push($recursos, $rec->Code);
        }

        return Inertia::render(
            'EtiquetaMecalux/Index',
            [
                'recurso' => ($request->recurso != '') ? $request->recurso : '',
                'recursos' => $recursos,
                'asset' => asset('')
            ]
        );
    }

    public function APIMecaluxRecurso(Request $request)
    {
        // $todosApontamentos = DB::connection('protheus')
        //     ->select("SELECT * from P12OFICIAL.dbo.BMX_VW_APONTAMENTO_MES
        //     where CODIGO_APONTAMENTO");


        // foreach ($todosApontamentos as $apontamento) {
        //     ImpressaoMecalux::firstOrCreate(
        //         ["CODIGO_APONTAMENTO" => $apontamento->CODIGO_APONTAMENTO],
        //         [
        //             "APONTAMENTO_MES" => $apontamento->APONTAMENTO_MES,
        //             "ID_INTEGRACAO_MES" => $apontamento->ID_INTEGRACAO_MES,
        //             "DtMov" => $apontamento->DtMov,
        //             "QUANTIDADE" => $apontamento->QUANTIDADE,
        //             "PRODUTO" => $apontamento->PRODUTO,
        //             "RECEITA" => $apontamento->RECEITA,
        //             "OP" => $apontamento->OP,
        //             "ARMAZEM" => $apontamento->ARMAZEM,
        //             "ErrDescription" => $apontamento->ErrDescription,
        //             "IDPCFACTORY" => $apontamento->IDPCFACTORY,
        //             "RECURSO" => $apontamento->RECURSO,
        //             "IMPRESSO" => "0"
        //         ]
        //     );
        // }

        $etiquetas = ImpressaoMecalux::where('IMPRESSO', '0')
            ->where('RECURSO', $request->recurso);

        if ($request->busca != '') {
            $etiquetas = ImpressaoMecalux::where('OP', 'LIKE', '%' . $request->busca . '%')->orWhere('APONTAMENTO_MES', 'LIKE', '%' . $request->busca . '%');
        }

        return response()->json($etiquetas->orderBy('APONTAMENTO_MES', 'DESC')->paginate(10));
    }

    public function apontamentoPdf($cod, $printer)
    {
        $turnoAgora = '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 05:20:00'), Carbon::now()->format('Y-m-d 13:50:00'))) ? $turnoAgora = 'T1' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 13:50:00'), Carbon::now()->format('Y-m-d 22:00:00'))) ? $turnoAgora = 'T2' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 22:00:00'), Carbon::now()->addDay()->format('Y-m-d 05:20:00'))) ? $turnoAgora = 'T3' : '';

        $linha = ImpressaoMecalux::where('CODIGO_APONTAMENTO', $cod)->first();

        $now = Carbon::now();
        $produto = DB::connection('protheus')->select("SELECT * FROM SB1010 WHERE B1_COD = '$linha->PRODUTO'");

        $pdf = new PDFCode128('L', 'mm', [100, 40]);
        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(100/*width*/, 8/*height*/, "BOMIX | " . $now->format('d') . ' ' .  $this->mes($now->format('m')) . ' ' .  $now->format('Y') . ' ' . $now->isoFormat('H:mm') . ' | RECEITA: ' .  trim($linha->RECEITA) . ' | ' . $turnoAgora . ' | QTD: ' . intval($linha->QUANTIDADE) /*String*/, 'B'/*Border*/, 1/*ln*/, 'C'/*alinhamento*/, false);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Code128(10/*x*/, 14/*y*/, $linha->OP . $linha->CODIGO_APONTAMENTO, 80/*width*/, 15/*height*/);
        $pdf->Cell(100, 6, $linha->OP . $linha->CODIGO_APONTAMENTO, 0, 0, "C");

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(3, 26);
        $pdf->Cell(100, 10, $linha->PRODUTO . ' - ' . $produto[0]->B1_DESC, 0, 1, "");

        $pdf->Output("F", public_path("PDF\\" . $cod . ".pdf"));

        ImpressaoMecalux::where('id', $linha->id)->update([
            'IMPRESSO' => 1
        ]);


        if ($printer != 'PDF')
            exec('"C:\Program Files (x86)\Foxit Software\Foxit PDF Reader\FoxitPDFReader.exe" /t "C:\xampp\htdocs\bomixKenobi\public\PDF\\' . $cod . '.pdf"  \\\192.168.254.71\192.168.255.2' . $printer . '');

        //exec('DEL /F /Q /A C:\xampp\htdocs\bomixKenobi\public\PDF\\' . $cod . '.pdf');

        return response()->json(['status' => true]);
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
