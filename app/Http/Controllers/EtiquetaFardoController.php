<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Force\PDFCode128;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\EtiquetaFardo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;


class EtiquetaFardoController extends Controller
{
    public function APIProgramacaoMaquina($recurso)
    {
        return response()->json(DB::connection('bomixbi')->select(DB::raw("Exec BomixBI.dbo.MES_SP_02_ListarPlanoProducao '$recurso'")));
    }
    //
    public function APIEtiquetaFardo(Request $request)
    {
        if ($request->printer == '' || $request->op == '') {
            return [
                'status' => false,
                'login' => false,
                'mensagem' => 'Falta dados para impressão desta etiqueta.'
            ];
        }

        $loginData = json_decode($request->login);

        if ($loginData->login == '' || $loginData->senha == '') {
            return [
                'status' => false,
                'login' => true,
                'mensagem' => 'O usuário deve estar logado para imprimir essa etiqueta.'
            ];
        }

        $user = User::where('user_name', $loginData->login)->where('status', '1')->orWhere('email', $loginData->login)->first();
        if (!($user && Hash::check($loginData->senha, $user->password))) {
            return [
                'status' => false,
                'login' => true,
                'mensagem' => 'O usuário não pode imprimir.'
            ];
        }

        $dadosOp = null;
        $login = false;
        $verifica = EtiquetaFardo::where('OP', $request->op)->orderBy('id', 'desc')->first();

        if ($request->op != '') {
            $dadosOp = DB::connection('protheus')->select("SELECT TOP 1 C2_NUM + C2_ITEM + C2_SEQUEN as OP_REAL, C2_QTDCARR/C2_QTDEMB as FARDO_PALLET, * FROM SC2010 (NOLOCK) WHERE C2_NUM + C2_ITEM + C2_SEQUEN  = '$request->op'");

            if (count($dadosOp) > 0) {
                if ($verifica != null) {
                    if (Carbon::create($verifica->created_at)->diffInMinutes(Carbon::now()) < 30) {
                        $user = User::where(fn ($query) => $query->where('user_name', $loginData->login)->orWhere('email', $loginData->login))
                            ->where('status', '1')
                            ->whereIn('role_id', ['10005', '1'])->first();
                        if (!($user && Hash::check($loginData->senha, $user->password))) {
                            return [
                                'status' => false,
                                'login' => true,
                                'mensagem' => 'O usuário não pode imprimir essa etiqueta, aguarde o tempo ou entre em contato com um gerente.'
                            ];
                        }
                        $login = true;
                    }
                }

                $printDados = $this->toPrint($dadosOp[0], $login, $request->printer, $user->name);
            } else {
                $printDados = [
                    'status' => false,
                    'login' => false,
                    'mensagem' => 'OP não encontrada'
                ];
            }
        }

        return response()->json($printDados);
    }

    static function toPrint($dadosOp, $login, $printer, $userName)
    {
        $verifica = EtiquetaFardo::where('OP', $dadosOp->OP_REAL)->orderBy('id', 'desc')->first();

        $via = 1;
        $totalEtiqueta = (intval($dadosOp->FARDO_PALLET) + intval($dadosOp->FARDO_PALLET) % 2) + 4;
        $totalFor = $totalEtiqueta / 2;

        if ($verifica == null) {
            $verifica =  EtiquetaFardo::create([
                'OP' => $dadosOp->OP_REAL,
                'VIA' => 1,
                'PRODUTO' => $dadosOp->C2_BRPROD,
                'QTD_ETIQUETAS' => $totalEtiqueta,
                'user_id' => Auth()->user()->id,
                'user_name' => Auth()->user()->name
            ]);
        } else {
            if (Carbon::create($verifica->created_at)->diffInMinutes(Carbon::now()) > 30 || $login) {
                $verificaNovo = EtiquetaFardo::create([
                    'OP' => $dadosOp->OP_REAL,
                    'VIA' => (intval($verifica->VIA) + 1),
                    'PRODUTO' => $dadosOp->C2_BRPROD,
                    'QTD_ETIQUETAS' => $totalEtiqueta,
                    'user_id' => Auth()->user()->id,
                    'user_name' => Auth()->user()->name
                ]);

                $via = (intval($verifica->VIA) + 1);
                $verifica = $verificaNovo;
            } else {
                return [
                    'status' => false,
                    'login' => true,
                    'mensagem' => 'Não pode imprimir agora, aguardar o tempo entre impressões.'
                ];
            }
        }

        $turnoAgora = '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 05:20:00'), Carbon::now()->format('Y-m-d 13:50:00'))) ? $turnoAgora = 'TURNO 1' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 13:50:00'), Carbon::now()->format('Y-m-d 22:00:00'))) ? $turnoAgora = 'TURNO 2' : '';
        (Carbon::create(Carbon::now()->format('Y-m-d H:i:s'))->between(Carbon::now()->format('Y-m-d 22:00:00'), Carbon::now()->addDay()->format('Y-m-d 05:20:00'))) ? $turnoAgora = 'TURNO 3' : '';

        $caminhoImagemPNG = 'C:\xampp\htdocs\bomixKenobi\storage\app\public\PNG\\' . $dadosOp->OP_REAL . '.png';
        $caminhoImagemSVG = 'PNG\\' . $dadosOp->OP_REAL . '.svg';
        Storage::disk('public')->put($caminhoImagemSVG, QrCode::format('svg')->size(550)->generate($dadosOp->OP_REAL));
        Browsershot::html(file_get_contents('C:\xampp\htdocs\bomixKenobi\storage\app\public\\' . $caminhoImagemSVG))
            ->noSandbox()->setScreenshotType('png')
            ->save($caminhoImagemPNG);

        $lote = substr($dadosOp->OP_REAL, 0, -3) . substr($dadosOp->OP_REAL, -2);


        $pdf = new PDFCode128('L', 'mm', [96, 48]);
        $pdf->SetMargins(1, 1, 1, 1);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('helvetica', 'B', 5.5);


        $pdf->Image($caminhoImagemPNG, 32, 9, -1100);
        $pdf->Image($caminhoImagemPNG, 78, 9, -1100);

        $pdf->SetXY(1, 1);
        $pdf->MultiCell(48, 2, $dadosOp->C2_FSPRODC . '-' . $dadosOp->C2_BRPROD, 0, 1);
        $pdf->SetXY(47, 1);
        $pdf->MultiCell(48, 2, $dadosOp->C2_FSPRODC . '-' . $dadosOp->C2_BRPROD, 0, 1);
        $pdf->SetFont('helvetica', 'B', 5.5);

        $pdf->SetXY(1, 9);
        $pdf->Cell(1, 1, $turnoAgora, 0, 0, "L");
        $pdf->SetXY(47, 9);
        $pdf->Cell(1, 1, $turnoAgora, 0, 0, "L");

        $pdf->SetXY(1, 12);
        $pdf->Cell(1, 1, 'VIA ' . $via . '/' . $totalEtiqueta . ' - ' . mb_strimwidth($userName, 0, 8), 0, 0, "L");
        $pdf->SetXY(47, 12);
        $pdf->Cell(1, 1, 'VIA ' . $via . '/' . $totalEtiqueta . ' - ' . mb_strimwidth($userName, 0, 8), 0, 0, "L");

        $pdf->SetXY(1, 15);
        $pdf->Cell(1, 1, 'LOTE:' . $lote, 0, 0, "L");
        $pdf->SetXY(47, 15);
        $pdf->Cell(1, 1, 'LOTE:' . $lote, 0, 0, "L");

        $pdf->SetXY(1, 18);
        $pdf->Cell(1, 1, 'DT:' . Carbon::now()->format('d/m/Y H:i:s'), 0, 0, "L");
        $pdf->SetXY(47, 18);
        $pdf->Cell(1, 1, 'DT:' . Carbon::now()->format('d/m/Y H:i:s'), 0, 0, "L");

        $pdf->SetXY(1, 21);
        $pdf->Cell(1, 1, 'QTD:' . $dadosOp->C2_QTDCARR, 0, 0, "L");
        $pdf->SetXY(47, 21);
        $pdf->Cell(1, 1, 'QTD:' . $dadosOp->C2_QTDCARR, 0, 0, "L");

        $pdf->SetXY(1, 25);
        $pdf->SetFont('helvetica', 'B', 5);
        $pdf->Cell(48, 1, 'BOMIX INDUSTRIA DE EMBALAGENS LTDA', 0, 0, "C");
        $pdf->SetXY(47, 25);
        $pdf->Cell(48, 1, 'BOMIX INDUSTRIA DE EMBALAGENS LTDA', 0, 0, "C");

        $pdf->Output("F", public_path("\storage\PDF\\" . $dadosOp->OP_REAL . ".pdf"));

        if ($printer != 'PDF') {
            for ($i = 0; $i < $totalFor; $i++) {
                if ($printer == 'GRANDE')
                    exec('"C:\Program Files (x86)\Foxit Software\Foxit PDF Reader\FoxitPDFReader.exe" /t "C:\xampp\htdocs\bomixKenobi\public\storage\PDF\\' . $dadosOp->OP_REAL . '.pdf"  \\\192.168.254.71\192.168.254.236');
                else
                    exec('"C:\Program Files (x86)\Foxit Software\Foxit PDF Reader\FoxitPDFReader.exe" /t "C:\xampp\htdocs\bomixKenobi\public\storage\PDF\\' . $dadosOp->OP_REAL . '.pdf"  \\\192.168.254.71\192.168.255.2' . $printer . '');
            }
        }

        return  [
            'status' => true,
            'login' => false,
            'mensagem' => 'Impresso com sucesso.'
        ];
    }
}
