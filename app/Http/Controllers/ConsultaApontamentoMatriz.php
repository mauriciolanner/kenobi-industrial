<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\ConsultaApontamento;
use App\Force\PDFCode39;
use App\Force\PDFCode128;

class ConsultaApontamentoMatriz extends Controller
{
    public function index(Request $request)
    {

        if (auth()->user()->role_id == 3 or auth()->user()->role_id == 4) {
            return back(303)->with([
                'title' => 'Usuário sem acesso.',
                'message' => 'O seu usuário não possui acesso a este modulo.',
                'type' => 'alert-danger'
            ]);
            return redirect('/');
        }

        return Inertia::render(
            'ConsultaApontamento/indexMatriz',
            [
                //'consultas' => $this->consulta($request->busca)
            ]
        );
    }

    public function indexMatriz(Request $request)
    {

        if (auth()->user()->role_id == 3 or auth()->user()->role_id == 4) {
            return back(303)->with([
                'title' => 'Usuário sem acesso.',
                'message' => 'O seu usuário não possui acesso a este modulo.',
                'type' => 'alert-danger'
            ]);
            return redirect('/');
        }

        return Inertia::render(
            'ConsultaApontamento/index',
            [
                //'consultas' => $this->consulta($request->busca)
            ]
        );
    }

    public function APIconsulta(Request $request)
    {
        $dados = DB::connection('protheus')->select("select * from BMX_VW_ROBOPAC where UMA LIKE '%" . $request->busca . "%'");
        return response()->json($dados);
    }


    public function apontamentoPdf($uma)
    {
        $linha = $dados = DB::connection('protheus')->select("select * from BMX_VW_ROBOPAC where UMA = '$uma'");

        //$this->updateInicial($recno);
        $pdf = new PDFCode128('L', 'mm', [100, 40]);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        //$pdf->SetTopMargin(0, 0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Code128(8, 4, '' . $linha[0]->UMA, 80, 15);
        $pdf->Cell(0, 25, $linha[0]->UMA, 0, 0, "C");
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(77, 38, $linha[0]->DESCRICAO, 0, 0, "R");
        //$pdf->MultiCell(-50, 38, $linha[0]->DESCRICAO, 50, 50, 'C');
        //$pdf->MultiCell(-100, 38, $linha[0]->DESCRICAO, 1, 'J', FALSE, 1, 100, 40);
        //$pdf->Cell(-535, 70, 'MATRIZ', 0, 0, "C");
        $pdf->Output();

        //MultiCell(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)


        exit;
    }

    static function consultaProduto($recno, $op)
    {
        $sql = "SELECT Top 20
        H6_CICLO as CicloDigitado, Ltrim(Rtrim(H6_OPERADO)) as Operador, H6_FSDOSAG AS Dosagem,	H6_FSPESOI AS PesoDigitado, 
        D3_NUMSEQ as NumSeq, Apontamento.H6_FILIAL as Empresa, Apontamento.H6_ZVIA AS Via,
        CASE 
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 01' THEN '1'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 02' THEN '2'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 03' THEN '3'
            WHEN Rtrim(H6_FSTURNO) = 'ADMINISTRATIVO' THEN '1'
        END AS Turno, H6_FSTURNO as TurnoDescricao, RTRIM(H6_PRODUTO) as Produto_ID, 
        CONVERT(VARCHAR, CAST(Apontamento.H6_DTAPONT AS DATE), 103) AS Emissao,
        CONVERT(datetime, Apontamento.H6_DATAINI, 112) AS DataIni,
        Apontamento.H6_HORAINI AS HoraIni,
                            DATEDIFF(MINUTE,  
                                    CONVERT(Datetime, CONVERT(Datetime, Apontamento.H6_DATAINI, 112) + CONVERT(Time, Apontamento.H6_HORAINI, 112), 112), 
                                    CONVERT(Datetime, CONVERT(Datetime, Apontamento.H6_DATAFIN, 112) + CONVERT(Time, Apontamento.H6_HORAFIN, 112), 112) 
                            ) AS TempoProducao,   
        CONVERT(Datetime, Apontamento.H6_DATAFIN, 112) AS DataFin,
        Apontamento.H6_HORAFIN AS HoraFin, RTRIM(H6_OP) as OrdemProducao,
        SUBSTRING(H6_OP, 1, 6) AS OPEtiqueta, RTRIM(Produto.B1_DESC) as Produto, 
        RTRIM(Apontamento.H6_LOTECTL) AS Lote, ROUND(Apontamento.H6_QTDPROD, 2) AS QtdProduzida, ROUND(Apontamento.H6_QTDPERD, 2) AS QtdPerda, 
        --Case when C2_DATRF = '' then C2_FSSALDO else 0 end AS Saldo, 
        C2_QUANT - C2_QUJE AS 'Saldo',
        Apontamento.R_E_C_N_O_ as Recno,
        RTRIM(LTRIM(D3_OP))+RTRIM(LTRIM(D3_IDMOV)) as CODE
    
        From P12OFICIAL.dbo.SC2010 (nolock) OrdemProducao 
        Inner Join P12OFICIAL.dbo.SH6010 (nolock) Apontamento ON C2_FILIAL = '010101'
                               AND Apontamento.D_E_L_E_T_ <> '*'
                               AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP 
        Inner Join P12OFICIAL.dbo.SB1010 (nolock) Produto ON Produto.D_E_L_E_T_ <> '*'
            AND Produto.B1_COD = H6_PRODUTO 
            AND B1_FILIAL  = '0101'
        Inner Join P12OFICIAL.dbo.SD3010 (nolock) Mov ON Mov.D3_FILIAL = '010101'
                                                 AND Mov.D_E_L_E_T_ <> '*'
                                                 AND Mov.D3_ESTORNO <> 'S'
                                                 AND Mov.D3_TM = '010'
                                                 AND Mov.D3_IDENT = H6_IDENT
                                                 AND Mov.D3_COD = H6_PRODUTO
        Where OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '010101'
        AND (Apontamento.H6_FSTURNO <> '') 
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U','S')
        AND Apontamento.H6_ZVIA = 0
        AND H6_OP='" . $op . "' AND Apontamento.R_E_C_N_O_='" . $recno . "' AND H6_QTDPROD > 0 Order by Apontamento.R_E_C_N_O_ desc";
        //dd($sql);
        $dados = DB::connection('protheus')->select($sql);

        if ($dados != []) {
            return $dados;
        }
    }

    static function updateInicial($recno)
    {
        // $updateinicial = "UPDATE SH6010 SET H6_ZVIA = 1 WHERE R_E_C_N_O_ = '$recno'";
        // $update = DB::connection('protheus')->update($updateinicial);
    }
}
