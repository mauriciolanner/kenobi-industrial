<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\ApontamentoImpresso;
use App\Force\PDFCode39;
use Carbon\Carbon;



class ApontamentoImpressoController extends Controller
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

            'ApontamentoImpresso/Index',
            [
                'asset' => asset(''),
                'consultas' => $this->consulta($request->busca, $request->dataini, $request->datafinal),
                'saldopr' => $this->consultaSaldoPR($request->busca, $request->dataini, $request->datafinal),
                'saldolo' => $this->consultaSaldoLO($request->busca, $request->dataini, $request->datafinal),
                'consulta2' => $this->consulta2($request->busca, $request->dataini, $request->datafinal)
            ]
        );
    }


    public function impressasPdf($recno, $op, $numseq)
    {
        $this->updateVia($recno);
        $linha = $this->consultaProduto($recno, trim($op), $numseq);
        //dd($linha);
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
            $pdf->Cell(-130, 75, substr($linha[0]->Produto, 30, 60), 0, 0, "C");
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
        $pdf->Cell(-350, 165, intval($linha[0]->Via), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-325, 165, "Via", 0, 0, "C");
        $pdf->SetFont("Times", "B", 20);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-220, 165, "PRODUCAO - SOPRO", 0, 0, "C");
        $pdf->SetFont("", "B", 80); //Times
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-100, 150, "" . $linha[0]->Turno, 0, 0, "C");
        $pdf->Output();
    }

    static function consulta($busca)
    {

        if ($busca != '') {
            ini_set('max_execution_time', 300);
            $sql = "SELECT
        D3_NUMSEQ AS 'NumSeq',
        H6_OP AS 'OrdemProducao',
        CONVERT(VARCHAR, CAST(H6_DATAINI AS DATE), 103) AS 'DataIni',
        H6_HORAINI AS 'HoraIni',
        H6_HORAFIN AS 'HoraFin',
        CONVERT(VARCHAR, CAST(H6_DTAPONT AS DATE), 103) AS 'Emissao',
        H6_LOTECTL AS 'Lote',
        H6_PRODUTO AS 'Produto_ID',
        B1_DESC AS 'Produto',
        H6_QTDPROD AS 'QtdProduzida',
        H6_QTDPERD AS 'QtdPerda',
        H6_OPERADO AS 'Operador',
        C2_QUANT AS 'QtdOrdem',
        C2_QUJE AS 'Apontado',
        C2_QUANT - C2_QUJE AS 'Falta',
        H6_ZVIA as 'Via',
        Apontamento.R_E_C_N_O_ as 'Recno',
        CASE
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 01' THEN '1'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 02' THEN '2'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 03' THEN '3'
            WHEN Rtrim(H6_FSTURNO) = 'ADMINISTRATIVO' THEN '1'
        END AS 'Turno',
        H6_FSTURNO as TurnoDescricao,
        RTRIM(H6_PRODUTO) as Produto_ID
        FROM
        P12OFICIAL.dbo.SC2010 (NOLOCK) AS OrdemProducao
        INNER JOIN P12OFICIAL.dbo.SH6010 (NOLOCK) AS Apontamento
         ON C2_FILIAL = '020101'
        AND Apontamento.D_E_L_E_T_ <> '*'
        AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP
        INNER JOIN P12OFICIAL.dbo.SD3010 (NOLOCK) AS Mov 
        ON Mov.D3_FILIAL = '020101'
        AND Mov.D_E_L_E_T_ <> '*'
        AND Mov.D3_ESTORNO <> 'S'
        AND Mov.D3_IDENT = H6_IDENT
        AND Mov.D3_COD = H6_PRODUTO
        INNER JOIN P12OFICIAL.dbo.SB1010 (NOLOCK) AS Produto
        ON Produto.D_E_L_E_T_ <> '*'
        AND Produto.B1_COD = H6_PRODUTO
        AND B1_FILIAL = '0201'
        Inner JOIN P12OFICIAL.dbo.SG2010 (NOLOCK) AS ProdutoOperacao 
        ON G2_PRODUTO = OrdemProducao.C2_PRODUTO
        AND ProdutoOperacao.D_E_L_E_T_ <> '*'
        AND G2_FILIAL = '020101'
        AND OrdemProducao.C2_ROTEIRO = ProdutoOperacao.G2_CODIGO
        WHERE
        OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '020101'
        AND (Apontamento.H6_FSTURNO <> '')
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U', 'S')
        AND Apontamento.H6_ZVIA > 0
        AND H6_QTDPROD > 0 ";
            $consulta = "  AND (
        H6_OP LIKE '%$busca%'
        OR D3_NUMSEQ LIKE '%$busca%'
        OR H6_PRODUTO LIKE '%$busca%'
        ) ";

            if ($busca != '') {
                $sql =  $sql . $consulta;
            }

            $sql = $sql . " Order by
        Apontamento.R_E_C_N_O_ desc";

            $dados = DB::connection('protheus')->select($sql);

            //dd($sql);
            if ($dados != []) {
                return $dados;
            }
        }
    }

    static function consultaProduto($recno, $op, $numseq)
    {
        $sql = "SELECT
        D3_NUMSEQ AS 'NumSeq',
        H6_OP AS 'OrdemProducao',
        CONVERT(VARCHAR, CAST(H6_DATAINI AS DATE), 103) AS 'DataIni',
        H6_HORAINI AS 'HoraIni',
        CONVERT(VARCHAR, CAST(H6_DTAPONT AS DATE), 103) AS 'Emissao',
        H6_LOTECTL AS 'Lote',
        H6_PRODUTO AS 'Produto_ID',
        B1_DESC AS 'Produto',
        H6_QTDPROD AS 'QtdProduzida',
        H6_QTDPERD AS 'QtdPerda',
        H6_OPERADO AS 'Operador',
        C2_QUANT AS 'QtdOrdem',
        C2_QUJE AS 'Apontado',
        C2_QUANT - C2_QUJE AS 'Falta',
        H6_ZVIA as 'Via',
        Case when C2_DATRF = '' then C2_FSSALDO else 0 end AS Saldo,
        Apontamento.H6_HORAFIN AS HoraFin,
        Apontamento.R_E_C_N_O_ as 'Recno',
        CASE
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 01' THEN '1'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 02' THEN '2'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 03' THEN '3'
            WHEN Rtrim(H6_FSTURNO) = 'ADMINISTRATIVO' THEN '1'
        END AS 'Turno',
        H6_FSTURNO as TurnoDescricao,
        RTRIM(H6_PRODUTO) as Produto_ID
        FROM
        P12OFICIAL.dbo.SC2010 (NOLOCK) AS OrdemProducao
        INNER JOIN P12OFICIAL.dbo.SH6010 (NOLOCK) AS Apontamento ON C2_FILIAL = '020101'
        AND Apontamento.D_E_L_E_T_ <> '*'
        AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP
        INNER JOIN P12OFICIAL.dbo.SD3010 (NOLOCK) AS Mov ON Mov.D3_FILIAL = '020101'
        AND Mov.D_E_L_E_T_ <> '*'
        AND Mov.D3_ESTORNO <> 'S'
        AND Mov.D3_IDENT = H6_IDENT
        AND Mov.D3_COD = H6_PRODUTO
        INNER JOIN P12OFICIAL.dbo.SB1010 (NOLOCK) AS Produto ON Produto.D_E_L_E_T_ <> '*'
        AND Produto.B1_COD = H6_PRODUTO
        AND B1_FILIAL = '0201'
        Inner JOIN P12OFICIAL.dbo.SG2010 (nolock) AS ProdutoOperacao 
        ON G2_PRODUTO = OrdemProducao.C2_PRODUTO
        AND ProdutoOperacao.D_E_L_E_T_ <> '*'
        AND G2_FILIAL = '020101'
        AND OrdemProducao.C2_ROTEIRO = ProdutoOperacao.G2_CODIGO
        WHERE
        OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '020101'
        AND (Apontamento.H6_FSTURNO <> '')
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U', 'S')
        AND Apontamento.H6_ZVIA > 0
        AND H6_OP = '" . $op . "'
        AND D3_NUMSEQ  = '" . $numseq . "'
        AND Apontamento.R_E_C_N_O_='" . $recno . "'
        AND H6_QTDPROD > 0
        Order by
        Apontamento.R_E_C_N_O_ desc";

        $dados = DB::connection('protheus')->select($sql);

        return $dados;
    }

    static function updateVia($recno)
    {
        $updatevia = "UPDATE P12OFICIAL.dbo.SH6010 SET H6_ZVIA = H6_ZVIA+1 WHERE R_E_C_N_O_='$recno'";
        $update = DB::connection('protheus')->update($updatevia);
    }

    public function consultaSaldoPR($busca, $dataini, $datafinal)
    {
        $dados = $this->consulta($busca, $dataini, $datafinal);
        if ($dados != '') {
            $consulta_saldo_pr = "SELECT B8_SALDO AS 'SaldoPR' from SB8010 (NOLOCK) WHERE B8_PRODUTO = '{$dados[0]->Produto_ID}' AND B8_LOCAL='PR' AND B8_LOTECTL = '{$dados[0]->Lote}' AND B8_FILIAL = '020101' AND D_E_L_E_T_=''";
            $resPR = DB::connection('protheus')->select($consulta_saldo_pr);

            return $resPR;
        }
    }

    public function consultaSaldoLO($busca, $dataini, $datafinal)
    {
        $dados = $this->consulta($busca, $dataini, $datafinal);
        if ($dados != '') {
            $consulta_saldo_lo = "SELECT B8_SALDO AS 'SaldoLO' from SB8010 (NOLOCK) WHERE B8_PRODUTO = '{$dados[0]->Produto_ID}' AND B8_LOCAL='LO' AND B8_LOTECTL = '{$dados[0]->Lote}' AND B8_FILIAL = '020101' AND D_E_L_E_T_=''";
            $resLO = DB::connection('protheus')->select($consulta_saldo_lo);

            return $resLO;
        }
    }

    static function consulta2($busca)
    {
        if ($busca != '') {
            ini_set('max_execution_time', 300);
            $sql = "SELECT
        D3_NUMSEQ AS 'NumSeq',
        H6_OP AS 'OrdemProducao',
        CONVERT(VARCHAR, CAST(H6_DATAINI AS DATE), 103) AS 'DataIni',
        H6_HORAINI AS 'HoraIni',
        H6_HORAFIN AS 'HoraFin',
        CONVERT(VARCHAR, CAST(H6_DTAPONT AS DATE), 103) AS 'Emissao',
        H6_LOTECTL AS 'Lote',
        H6_PRODUTO AS 'Produto_ID',
        B1_DESC AS 'Produto',
        H6_QTDPROD AS 'QtdProduzida',
        H6_QTDPERD AS 'QtdPerda',
        H6_OPERADO AS 'Operador',
        C2_QUANT AS 'QtdOrdem',
        C2_QUJE AS 'Apontado',
        C2_QUANT - C2_QUJE AS 'Falta',
        H6_ZVIA as 'Via',
        Apontamento.R_E_C_N_O_ as 'Recno',
        CASE
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 01' THEN '1'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 02' THEN '2'
            WHEN Rtrim(H6_FSTURNO) = 'TURNO 03' THEN '3'
            WHEN Rtrim(H6_FSTURNO) = 'ADMINISTRATIVO' THEN '1'
        END AS 'Turno',
        H6_FSTURNO as TurnoDescricao,
        RTRIM(H6_PRODUTO) as Produto_ID
        FROM
        P12OFICIAL.dbo.SC2010 (NOLOCK) AS OrdemProducao
        INNER JOIN P12OFICIAL.dbo.SH6010 (NOLOCK) AS Apontamento
         ON C2_FILIAL = '020101'
        AND Apontamento.D_E_L_E_T_ <> '*'
        AND (C2_NUM + C2_ITEM + C2_SEQUEN) = Apontamento.H6_OP
        INNER JOIN P12OFICIAL.dbo.SD3010 (NOLOCK) AS Mov 
        ON Mov.D3_FILIAL = '020101'
        AND Mov.D_E_L_E_T_ <> '*'
        AND Mov.D3_ESTORNO <> 'S'
        AND Mov.D3_IDENT = H6_IDENT
        AND Mov.D3_COD = H6_PRODUTO
        INNER JOIN P12OFICIAL.dbo.SB1010 (NOLOCK) AS Produto
        ON Produto.D_E_L_E_T_ <> '*'
        AND Produto.B1_COD = H6_PRODUTO
        AND B1_FILIAL = '0201'
        Inner JOIN P12OFICIAL.dbo.SG2010 (NOLOCK) AS ProdutoOperacao 
        ON G2_PRODUTO = OrdemProducao.C2_PRODUTO
        AND G2_FILIAL = '020101'
        AND OrdemProducao.C2_ROTEIRO = ProdutoOperacao.G2_CODIGO
        WHERE
        OrdemProducao.D_E_L_E_T_ <> '*'
        AND Apontamento.H6_FILIAL = '020101'
        AND (Apontamento.H6_FSTURNO <> '')
        AND (Apontamento.H6_TIPO = 'P')
        AND Year(Apontamento.H6_DTAPONT) >= Year(GETDATE()) - 1
        AND OrdemProducao.C2_STATUS not in ('U', 'S')
        AND Apontamento.H6_ZVIA > 0
        AND H6_QTDPROD > 0 ";
            $consulta = "  AND (
        H6_OP LIKE '%$busca%'
        OR D3_NUMSEQ LIKE '%$busca%'
        OR H6_PRODUTO LIKE '%$busca%'
        ) ";

            $consulta = "  AND (
        H6_OP LIKE '%$busca%'
        OR D3_NUMSEQ LIKE '%$busca%'
        OR H6_PRODUTO LIKE '%$busca%'
        ) ";

            if ($busca != '') {
                $sql =  $sql . $consulta;
            }

            $sql = $sql . " Order by
        Apontamento.R_E_C_N_O_ desc ";
            $dados = DB::connection('protheus')->select($sql);

            if ($dados != []) {
                return $dados;
            }
        }
    }
}
