<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\ConsultaApontamento;
use App\Force\PDFCode39;


class ConsultaApontamentoController extends Controller
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
            'ConsultaApontamento/index',
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
        return response()->json(
            $this->consulta($request->busca)
        );
    }


    public function apontamentoPdf(Request $request)
    {
        $recno = $request->recnoEnviado;
        $op = $request->opEnviada;
        $linha = $this->consultaProduto($recno, trim($op));
        $this->updateInicial($recno);
        $pdf = new PDFCode39('L', 'mm');
        $pdf->AddPage();
        $pdf->Cell(0, 0, "", 0, 0);
        $pdf->Code39(3, 4, '' . $linha[0]->Produto_ID);
        $pdf->Code39(3, 35, '' . $linha[0]->Lote);
        $pdf->Code39(3, 66, '' . $linha[0]->NumSeq);
        $pdf->SetFont("", "B", 25);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-255, -6, "" . $linha[0]->Emissao, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-165, -6, "" . $linha[0]->HoraIni, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-135, -9, "-", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-105, -6, "" . $linha[0]->HoraFin, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-245, 13, "" . $linha[0]->OrdemProducao, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 20);
        if (strlen($linha[0]->Produto) > '25') {
            $pdf->Cell(-180, 55, substr($linha[0]->Produto, 0, 30), 0, 0, "C");
            $pdf->Cell(0, 0, "", 0, 0, "C");
            $pdf->Cell(-180, 75, substr($linha[0]->Produto, 30, 60), 0, 0, "C");
        } else {
            $pdf->Cell(-180, 55, $linha[0]->Produto, 0, 0, "C");
        }
        $pdf->SetFont("", "B", 25);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-265, 125, "Produzida:", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-190, 125, "" . intval($linha[0]->QtdProduzida), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-165, 125, "/", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-140, 125, "" . intval($linha[0]->QtdPerda), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-269, 145, "Saldo OP:", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-140, 145, "" . intval($linha[0]->Saldo), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        //$pdf->Cell (-300,90,"3",0,0,"C");
        //$pdf->Cell (0,0,"",0,0,"C");
        //$pdf->Cell (-290,90,"Via",0,0,"C");
        $pdf->SetFont("Times", "B", 20);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-220, 165, "PRODUCAO - SOPRO", 0, 0, "C");
        $pdf->SetFont("", "B", 80); //Times
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-100, 150, "" . $linha[0]->Turno, 0, 0, "C");
        $pdf->Output();


        exit;
    }

    static function consulta($busca)
    {
        $sql = "SELECT TOP 20
        H6_OP AS 'OrdemProducao',
        CASE 
        WHEN Rtrim(H6_FSTURNO) = 'TURNO 01' THEN '1'
        WHEN Rtrim(H6_FSTURNO) = 'TURNO 02' THEN '2'
        WHEN Rtrim(H6_FSTURNO) = 'TURNO 03' THEN '3'
        WHEN Rtrim(H6_FSTURNO) = 'ADMINISTRATIVO' THEN '1'
        END AS Turno,
        Apontamento.H6_ZVIA AS Via,
        CONVERT(VARCHAR, CAST(C2_EMISSAO AS DATE), 103) AS 'Emissao',
        RTRIM(C2_PRODUTO) AS 'Produto_ID',
        B1_DESC AS 'Produto',
        C2_NUM AS Lote,
        H6_QTDPROD AS QtdProduzida,
        C2_QUANT - C2_QUJE AS 'Saldo',
        D3_NUMSEQ as NumSeq,
        Apontamento.R_E_C_N_O_ AS Recno
    FROM
        P12OFICIAL.dbo.SC2010 (NOLOCK) AS OrdemProducao
        INNER JOIN P12OFICIAL.dbo.SH6010 (NOLOCK) AS Apontamento 
        ON C2_FILIAL = '020101'
        AND Apontamento.D_E_L_E_T_ <> '*'
        AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP
        INNER JOIN P12OFICIAL.dbo.SB1010 (NOLOCK) AS Produto 
        ON Produto.D_E_L_E_T_ <> '*'
        AND Produto.B1_COD = H6_PRODUTO
        AND B1_FILIAL = '0201'
        INNER JOIN P12OFICIAL.dbo.SD3010 (NOLOCK) AS Mov ON Mov.D3_FILIAL = '020101'
        AND Mov.D_E_L_E_T_ <> '*'
        AND Mov.D3_ESTORNO <> 'S'
        AND Mov.D3_TM = '010'
        AND Mov.D3_IDENT = H6_IDENT
        AND Mov.D3_COD = H6_PRODUTO
    WHERE
        OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '020101'
        AND (Apontamento.H6_FSTURNO <> '')
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U', 'S')
        AND Apontamento.H6_ZVIA = 0
        AND H6_QTDPROD > 0 ";


        $consulta = " AND (
        H6_OP LIKE '%$busca%'
        OR D3_NUMSEQ LIKE '%$busca%'
        OR H6_PRODUTO LIKE '%$busca%'
    ) ";

        if ($busca != '') {
            $sql =  $sql . $consulta;
        }

        $sql = $sql . " Order by
        Apontamento.R_E_C_N_O_ desc";

        //dd($sql);
        $dados = DB::connection('protheus')->select($sql);

        if ($dados != []) {
            return $dados;
        }
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
        Apontamento.R_E_C_N_O_ as Recno 
    
        From P12OFICIAL.dbo.SC2010 (nolock) OrdemProducao 
        Inner Join P12OFICIAL.dbo.SH6010 (nolock) Apontamento ON C2_FILIAL = '020101'
                               AND Apontamento.D_E_L_E_T_ <> '*'
                               AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP 
        Inner Join P12OFICIAL.dbo.SB1010 (nolock) Produto ON Produto.D_E_L_E_T_ <> '*'
            AND Produto.B1_COD = H6_PRODUTO 
            AND B1_FILIAL  = '0201'
        Inner Join P12OFICIAL.dbo.SD3010 (nolock) Mov ON Mov.D3_FILIAL = '020101'
                                                 AND Mov.D_E_L_E_T_ <> '*'
                                                 AND Mov.D3_ESTORNO <> 'S'
                                                 AND Mov.D3_TM = '010'
                                                 AND Mov.D3_IDENT = H6_IDENT
                                                 AND Mov.D3_COD = H6_PRODUTO
        Where OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '020101'
        AND (Apontamento.H6_FSTURNO <> '') 
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U','S')
        AND Apontamento.H6_ZVIA = 0
        AND H6_OP='" . $op . "' AND Apontamento.R_E_C_N_O_='" . $recno . "' AND H6_QTDPROD > 0 Order by Apontamento.R_E_C_N_O_ desc";

        $dados = DB::connection('protheus')->select($sql);

        if ($dados != []) {
            return $dados;
        }
    }

    static function updateInicial($recno)
    {
        $updateinicial = "UPDATE SH6010 SET H6_ZVIA = 1 WHERE R_E_C_N_O_ = '$recno'";
        $update = DB::connection('protheus')->update($updateinicial);
    }
}
