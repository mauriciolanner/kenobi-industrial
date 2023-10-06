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
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        $dataReferencia = Carbon::now()->subDays(90)->format('Y-m-d');

        $etiquetas = ImpressaoMecalux::where('IMPRESSO', '0')->where('DtMov', '>', $dataReferencia . ' 00:00:00.000000')
            ->where('RECURSO', $request->recurso)->whereNull('ESTORNO');

        if ($request->busca != '') {
            $etiquetas = ImpressaoMecalux::whereNull('ESTORNO')->where('OP', 'LIKE', '%' . $request->busca . '%')->orWhere('APONTAMENTO_MES', 'LIKE', '%' . $request->busca . '%');
        }

        return response()->json($etiquetas->orderBy('APONTAMENTO_MES', 'DESC')->paginate(10));
    }


    public function apontamentoPdf(Request $request)
    {
        $turnoAgora = '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 05:20:00'), Carbon::now()->format('Y-m-d 13:50:00'))) ? $turnoAgora = 'TURNO 1' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 13:50:00'), Carbon::now()->format('Y-m-d 22:00:00'))) ? $turnoAgora = 'TURNO 2' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 22:00:00'), Carbon::now()->addDay()->format('Y-m-d 05:20:00'))) ? $turnoAgora = 'TURNO 3' : '';

        $linha = ImpressaoMecalux::where('CODIGO_APONTAMENTO', $request->cod)->first();

        if ($linha->IMPRESSO == 1) {
            $user = User::where('user_name', $request->login)->where('status', '1')
                ->orWhere('email', $request->login)->whereIn('role_id', ['10005', '1'])->first();
            if (!($user && Hash::check($request->senha, $user->password))) {
                return response()->json([
                    'status' => false,
                    'title' => 'Erro!',
                    'message' => 'O usuário não pode imprimir essa etiqueta, entrar em contato com um gerente.',
                    'type' => 'alert-danger'
                ]);
            }
        }

        $via = 1;

        if ($linha->created_at != null) {
            $now = Carbon::create($linha->created_at);
            $via = intval(ImpressaoMecalux::where('id', $linha->id)->first()->IMPRESSO) + 1;

            ImpressaoMecalux::where('id', $linha->id)->update([
                'IMPRESSO' => $via
            ]);
        } else {
            ImpressaoMecalux::where('id', $linha->id)->update([
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            $now = Carbon::now();
        }

        $produto = DB::connection('protheus')->select("SELECT * FROM SB1010 WHERE B1_COD = '$linha->PRODUTO' AND B1_FILIAL = '0101' AND D_E_L_E_T_ <> '*'");

        $pdf = new PDFCode128('L', 'mm', [100, 40]);
        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100/*width*/, 4/*height*/, "BOMIX | " . $now->format('d') . ' ' .  $this->mes($now->format('m')) . ' ' .  $now->format('Y') . ' ' . $now->isoFormat('H:mm') . ' | ' . $turnoAgora . ' | QTD: ' . intval($linha->QUANTIDADE)/*String*/, ''/*Border*/, 1/*ln*/, 'C'/*alinhamento*/, false);
        $pdf->Cell(100/*width*/, 4/*height*/, 'RECEITA: ' .  trim($linha->RECEITA) . ' | ' . $turnoAgora . ' | RECURSO: ' . $linha->RECURSO . ' | Via ' . $via/*String*/, 'B'/*Border*/, 1/*ln*/, 'C'/*alinhamento*/, false);

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Code128(10/*x*/, 14/*y*/, $linha->OP . $linha->CODIGO_APONTAMENTO, 80/*width*/, 15/*height*/);
        $pdf->Cell(100, 6, $linha->OP . ' - ' . $linha->APONTAMENTO_MES . ' - ' . $linha->RECEITA, 0, 0, "C");

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(3, 26);
        $pdf->Cell(100, 10, $linha->PRODUTO . ' - ' . $produto[0]->B1_DESC, 0, 1, "");

        $pdf->Output("F", public_path("PDF\\" . $request->cod . ".pdf"));


        if ($request->printer != 'PDF')
            exec('"C:\Program Files (x86)\Foxit Software\Foxit PDF Reader\FoxitPDFReader.exe" /t "C:\xampp\htdocs\bomixKenobi\public\PDF\\' . $request->cod . '.pdf"  \\\192.168.254.71\192.168.255.2' . $request->printer . '');

        ImpressaoMecalux::where('id', $linha->id)->update([
            'IMPRESSO' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
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
