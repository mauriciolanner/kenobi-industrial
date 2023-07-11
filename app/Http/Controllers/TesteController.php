<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ConsultaFardo;
use App\Models\LogEtiquetaFardo;
use App\Force\PDFCode39;
use Illuminate\Support\Facades\Validator;

class TesteController extends Controller
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
            'ConsultaFardo/Index',
            [
                'asset' => asset(''),
                'consultas' => $this->consulta($request->busca)
            ]
        );
    }

    public function consultaNumeroDeImpressoes($op)
    {
        $numeroImpressoes = DB::connection('sqlsrv')->select("SELECT top 1 num_impressoes FROM log_etiqueta_fardos WHERE op LIKE '%$op%' ORDER BY num_impressoes DESC");
        if ($numeroImpressoes != []) {
            return response()->json($numeroImpressoes);
        }
    }

    public function fardoPdfReimpressao(Request $request)
    {

        Validator::make($request->all(), [
            "motivoImpressao" => 'required',
            "reimprecaoEtiqueta" => 'required',
        ])->validate();

        $numeroImpressoes = LogEtiquetaFardo::where('op', 'LIKE', '%' . $request->reimprecaoOp . '%')
            ->orderBy('num_impressoes', 'desc')->first();

        $linha = $this->consultaProduto(substr($request->reimprecaoId, 0, 9), $request->reimprecaoOp);
        $qtdOP = $linha[0]->Quantidade;
        $Date = date('d/m/Y', strtotime($linha[0]->Validade));
        $QtdPorEmbalagem = $linha[0]->QtdPorEmbalagem;
        $quantMaxFardos = floor($qtdOP / $QtdPorEmbalagem);
        $etiquetaReimp = $request->reimprecaoEtiqueta;

        if ($etiquetaReimp > $quantMaxFardos) {
            $etiquetaReimp = $quantMaxFardos;
        }

        //$pdf = new Fpdf;
        $pdf = new PDFCode39('L', 'mm');

        $numImpressoesConvertido = intval($numeroImpressoes->num_impressoes);
        $numeroImpressoes = $numImpressoesConvertido + 1;
        $pdf->AddPage();
        //$pdf->Cell (113,55,"",1,1);
        $pdf->Cell(250, 116, "", 1, 1);
        $pdf->Code39(16, 75, substr($request->reimprecaoOp, 0, 8));
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 30);
        $pdf->Cell(-320, -15, "Bomix Divisao Sopro", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 20);

        // Remoção do campo de matricula como pedido na reunião por Fabio/Sopro em 29/06/2023 - Alterado por Vinícius Evangelista - 30/06/2023
        // $pdf->Cell(-120, -10, "Matricula:  " . $matricula, 0, 0, "C");

        $pdf->SetFont("", "B", 22);
        $pdf->Cell(-100, -215, $linha[0]->Produto, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-310, -200, substr($request->reimprecaoId, 0, 9), 0, 0, "C");

        $pdf->SetFont("", "B", 27);
        $pdf->Cell(-340, -220, $linha[0]->Produto, 0, 0, "C");
        $pdf->Cell(0, -58, "", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-465, -170, "OP:  " . $request->reimprecaoOp, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-476, -145, "Lote:  " . $linha[0]->Lote, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-453, -120, "Validade:  " . $Date, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        //	$pdf->Cell (-160,-145,"Operador: ",0,0,"C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-295, -120, "Contem:  " . intval($QtdPorEmbalagem), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 20);
        $pdf->Cell(-130, -170, "Identificação", 0, 0, "C");
        //$pdf->Image(asset('img/etiqueta_identificacao.png'), 195, 35, 55, 55, 'PNG');
        $pdf->Image(public_path() . "/img/etiqueta_identificacao.png", 195, 35, 55, 55, 'PNG');
        $pdf->SetFont("", "B", 20);
        $pdf->Cell(135, -55, "ETIQUETA", 0, 0, "C");
        $pdf->SetFont("", "B", 20);
        $pdf->Cell(-135, -35, "$etiquetaReimp/$quantMaxFardos", 0, 0, "C");

        // Remoção do campo Turno como pedido na reunião por Fabio/Sopro em 29/06/2023 - Alterado por Vinícius Evangelista - 30/06/2023
        // $pdf->Cell(-70, -185, $turno, 0, 0, "C");

        $mensagemLogImpressao = "A etiqueta $etiquetaReimp da OP: $request->reimprecaoOp foi reimpressa";
        LogEtiquetaFardo::create(
            [
                'usuario' => auth()->user()->name,
                'log' => $mensagemLogImpressao,
                'op' => $request->reimprecaoOp,
                'num_impressoes' => $numeroImpressoes,
                'motivo_da_impressao' => $request->motivoImpressao
            ]
        );

        $pdf->Output();
        exit;
    }

    public function fardoPdf(Request $request)
    {
        $numeroImpressoes = LogEtiquetaFardo::where('op', 'LIKE', '%' . $request->imprecaoOp . '%')
            ->orderBy('num_impressoes', 'desc')->first();


        // Antigas variáveis da função: ($id, $op, $matricula, $turno)
        //dd($id,$op,$matricula,$turno);
        $linha = $this->consultaProduto(substr($request->imprecaoId, 0, 9), $request->imprecaoOp);
        $qtdOP = $linha[0]->Quantidade;
        $Date = date('d/m/Y', strtotime($linha[0]->Validade));
        $QtdPorEmbalagem = $linha[0]->QtdPorEmbalagem;
        $quantMaxFardos = floor($qtdOP / $QtdPorEmbalagem);
        //$pdf = new Fpdf;
        $pdf = new PDFCode39('L', 'mm');

        if ($numeroImpressoes == []) {
            $numeroImpressoes = 1;
            for ($i = $quantMaxFardos; $i > 0; $i--) {
                $pdf->AddPage();
                //$pdf->Cell (113,55,"",1,1);
                $pdf->Cell(250, 116, "", 1, 1);
                $pdf->Code39(16, 75, substr($request->imprecaoOp, 0, 8));
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->SetFont("", "B", 30);
                $pdf->Cell(-320, -15, "Bomix Divisao Sopro", 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->SetFont("", "B", 25);

                // Remoção do campo de matricula como pedido na reunião por Fabio/Sopro em 29/06/2023 - Alterado por Vinícius Evangelista - 30/06/2023
                // $pdf->Cell(-120, -10, "Matricula:  " . $matricula, 0, 0, "C");

                $pdf->SetFont("", "B", 22);
                // $pdf->Cell(-100, -215, $linha[0]->Produto, 0, 0, $align = "C");
                $pdf->Cell(-100, -215, $linha[0]->Produto, 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->Cell(-310, -200, substr($request->imprecaoId, 0, 9), 0, 0, "C");

                $pdf->SetFont("", "B", 27);
                $pdf->Cell(-360, -200, substr($linha[0]->Produto, 74, 111), 0, 0, "C");
                $pdf->Cell(-340, -220, $linha[0]->Produto, 0, 0, "C");
                $pdf->Cell(0, -58, "", 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->Cell(-465, -170, "OP:  " . $request->imprecaoOp, 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->Cell(-476, -145, "Lote:  " . $linha[0]->Lote, 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->Cell(-453, -120, "Validade:  " . $Date, 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                //	$pdf->Cell (-160,-145,"Operador: ",0,0,"C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->Cell(-295, -120, "Contem:  " . intval($QtdPorEmbalagem), 0, 0, "C");
                $pdf->Cell(0, 0, "", 0, 0, "C");
                $pdf->SetFont("", "B", 20);
                $pdf->Cell(-130, -170, "Identificação", 0, 0, "C");
                // $pdf->Image(asset('img/etiqueta_identificacao.png'), 195, 35, 55, 55, 'PNG');
                $pdf->Image(public_path() . "/img/etiqueta_identificacao.png", 195, 35, 55, 55, 'PNG');
                $pdf->SetFont("", "B", 20);
                $pdf->Cell(135, -55, "ETIQUETA", 0, 0, "C");
                $pdf->SetFont("", "B", 20);
                $pdf->Cell(-135, -35, ($i) . "/$quantMaxFardos", 0, 0, "C");

                // Remoção do campo Turno como pedido na reunião por Fabio/Sopro em 29/06/2023 - Alterado por Vinícius Evangelista - 30/06/2023
                // $pdf->Cell(-70, -185, $turno, 0, 0, "C");

                $mensagemLogImpressao = "A " . ($i) . "ª etiqueta de $quantMaxFardos da OP: $request->imprecaoOp foi criada";
                LogEtiquetaFardo::create(
                    [
                        'usuario' => auth()->user()->name,
                        'log' => $mensagemLogImpressao,
                        'op' => $request->imprecaoOp,
                        'num_impressoes' => $numeroImpressoes,
                        'motivo_da_impressao' => 'Primeira impressão'
                    ]
                );
            }
        }

        return $pdf->Output();
    }

    //Etiqueta Antiga ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // $pdf->AddPage();
    // //$pdf->Cell (113,55,"",1,1);
    // $pdf->Cell(250, 116, "", 1, 1);
    // $pdf->Code39(13, 80, substr($op, 0, 8));
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->SetFont("", "B", 25);
    // $pdf->Cell(-320, -10, "Bomix Divisao Sopro", 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->SetFont("", "B", 20);
    // $pdf->Cell(-120, -10, "Matricula:  " . $matricula, 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->SetFont("", "B", 25);
    // if (strlen($linha[0]->Produto) > '37') {
    //     $pdf->Cell(-360, -220, substr($linha[0]->Produto, 0, 37), 0, 0, "C");
    //     $pdf->Cell(0, 0, "", 0, 0, "C");
    //     $pdf->Cell(-300, -200, substr($linha[0]->Produto, 37, 74), 0, 0, "C");
    //     $pdf->Cell(0, 0, "", 0, 0, "C");
    //     $pdf->Cell(-360, -200, substr($linha[0]->Produto, 74, 111), 0, 0, "C");
    // } else {
    //     $pdf->Cell(-360, -220, $linha[0]->Produto, 0, 0, "C");
    // }
    // $pdf->Cell(-390, -220, $linha[0]->Produto, 0, 0, "C");
    // $pdf->Cell(0, -58, "", 0, 0, "C");
    // $pdf->Cell(-480, -145, $id, 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-314, -145, "OP:  " . $op, 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-480, -120, "Lote:  " . $linha[0]->Lote, 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-306, -120, "Validade:  " . $Date, 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // //	$pdf->Cell (-160,-145,"Operador: ",0,0,"C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-135, -120, "Contem:  " . intval($QtdPorEmbalagem), 0, 0, "C");
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-175, -70, date("d/m/Y H:i:s"), 0, 0, "C");
    // $pdf->SetFont("", "", 80); //Times
    // $pdf->Cell(0, 0, "", 0, 0, "C");
    // $pdf->Cell(-70, -185, $turno, 0, 0, "C");
    // $pdf->Output();
    // exit;

    static function consulta($busca)
    {
        $sql = "SELECT top 20
        (C2_NUM + C2_ITEM + C2_SEQUEN) AS OrdemProducao, CONVERT(Date, OrdemProducao.C2_EMISSAO) AS Emissao, RTRIM(C2_PRODUTO) + ' - P1' AS ID, C2_PRODUTO AS Produto_ID,
        B1_DESC AS Produto, C2_QUANT AS Quantidade, B1_BRQEM AS QtdPorEmbalagem , C2_NUM + C2_ITEM AS Lote, CONVERT(Datetime, OrdemProducao.C2_EMISSAO, 112) + B1_PRVALID AS Validade, OrdemProducao.R_E_C_N_O_ AS Recno
    FROM 
        P12OFICIAL.dbo.SC2010 OrdemProducao (NOLOCK)
    INNER JOIN P12OFICIAL.dbo.SB1010 Produto (NOLOCK)
                ON C2_PRODUTO = B1_COD
                AND Produto.B1_FILIAL in ('0201')
                AND Produto.D_E_L_E_T_ <> '*'
                AND Produto.B1_MSBLQL<>'1'
    INNER JOIN P12OFICIAL.dbo.SG2010 (NOLOCK) AS Operacao 
        ON	Operacao.G2_PRODUTO = C2_PRODUTO
        AND Operacao.G2_FILIAL = '020101' 
        AND Operacao.D_E_L_E_T_ <> '*'
        AND Operacao.G2_PRODUTO = OrdemProducao.C2_PRODUTO 
        AND Operacao.G2_CODIGO = OrdemProducao.C2_ROTEIRO 
        
    Inner Join P12OFICIAL.dbo.SBM010 BM ON BM.D_E_L_E_T_ <> '*' 
                                       AND BM.BM_FILIAL = '0201'
                                       AND BM.BM_GRUPO = B1_GRUPO
    
    Inner Join P12OFICIAL.dbo.SZL010 ZL WITH (nolock) ON ZL.D_E_L_E_T_ <> '*'	
                                        AND BM.BM_FILIAL = ZL.ZL_FILIAL 
                                         AND BM.BM_GRUPOBX = ZL_COD 
    Inner Join (
                                Select 
                                Substring(X5_FILIAL,1,4) as Empresa, 
                                X5_CHAVE as TipoProduto_ID,
                                X5_DESCRI as TipoProduto,
                                R_E_C_N_O_ as TipoProduto_Recno
                                from P12OFICIAL.dbo.SX5010 (nolock)
                                Where 1=1
                                AND X5_FILIAL = '020101'
                                AND D_E_L_E_T_ <> '*'
                                AND X5_TABELA = 'XT' 							
                ) TipoProduto ON TipoProduto.Empresa = Substring(ZL_FILIAL,1,4)
                             AND TipoProduto.TipoProduto_ID = ZL_TIPO
    WHERE
                (OrdemProducao.C2_FILIAL = '020101') 
                AND (OrdemProducao.D_E_L_E_T_ <> '*')
                AND OrdemProducao.C2_QUANT <= 99999
                AND C2_BRSTATU NOT IN ('FINALIZADA', 'SUSPENSA', 'PREVISTA')
                AND (C2_NUM + C2_ITEM + C2_SEQUEN) Like '%%' ";

        $consulta = " AND C2_PRODUTO LIKE '%$busca%'
                    OR (C2_NUM + C2_ITEM + C2_SEQUEN) LIKE '%$busca%' ";

        if ($busca != '') {
            $sql =  $sql . $consulta;
        }

        $sql = $sql . " ORDER BY Recno desc ";


        $dados = DB::connection('protheus')->select($sql);
        return $dados;
    }
    static function consultaProduto($id, $op)
    {
        $sql = "SELECT top 50
        (C2_NUM + C2_ITEM + C2_SEQUEN) AS OrdemProducao, CONVERT(Date, OrdemProducao.C2_EMISSAO) AS Emissao, RTRIM(C2_PRODUTO) + ' - P1' AS ID, C2_PRODUTO AS Produto_ID,
        B1_DESC AS Produto, C2_QUANT AS Quantidade, B1_BRQEM AS QtdPorEmbalagem , C2_NUM + C2_ITEM AS Lote, CONVERT(Datetime, OrdemProducao.C2_EMISSAO, 112) + B1_PRVALID AS Validade, OrdemProducao.R_E_C_N_O_ AS Recno
    FROM 
        P12OFICIAL.dbo.SC2010 OrdemProducao (NOLOCK)
    INNER JOIN P12OFICIAL.dbo.SB1010 Produto (NOLOCK)
                ON C2_PRODUTO = B1_COD
                AND Produto.B1_FILIAL in ('0201')
                AND Produto.D_E_L_E_T_ <> '*'
                AND Produto.B1_MSBLQL<>'1'
    WHERE
                (OrdemProducao.C2_FILIAL = '020101') 
                AND (OrdemProducao.D_E_L_E_T_ <> '*')
                AND OrdemProducao.C2_QUANT <= 99999
                AND C2_BRSTATU NOT IN ('FINALIZADA', 'SUSPENSA', 'PREVISTA')
                AND C2_PRODUTO LIKE '%$id%'
                AND (C2_NUM + C2_ITEM + C2_SEQUEN) LIKE '%$op%'
        ORDER BY 
        Recno desc";

        $dados = DB::connection('protheus')->select($sql);

        if ($dados != []) {
            return $dados;
        }
    }

    public function etiquetaduplaPdf($id, $op, $matricula, $turno)
    {
        $linha = $this->consultaProduto(substr($id, 0, 9), $op);
        $Date = date('d/m/Y', strtotime($linha[0]->Validade));
        $QtdPorEmbalagem = $linha[0]->QtdPorEmbalagem;

        $pdf = new PDFCode39('L', 'mm');
        //Etiqueta da esquerda
        $pdf->AddPage();
        $pdf->SetY(5);
        $pdf->SetX(10);
        $pdf->Cell(114, 65, "", 0, 1);
        $pdf->Code39(12, 18, substr($op, 0, 8), true, false, 0.4, 10, true);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(-495, -10, "Bomix Divisao Sopro", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(-370, -10, "Matricula:  " . $matricula, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        if (strlen($linha[0]->Produto) > '35') {
            $pdf->Cell(-450, -120, substr($linha[0]->Produto, 0, 30), 0, 0, "C");
            $pdf->Cell(0, 0, "", 0, 0, "C");
            $pdf->Cell(-415, -109, substr($linha[0]->Produto, 30, 60), 0, 0, "C");
        } else {
            $pdf->Cell(-450, -120, $linha[0]->Produto, 0, 0, "C");
        }
        $pdf->Cell(0, -58, "", 0, 0, "C");
        $pdf->Cell(-509, -65, $id, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-372, -65, "OP:  " . $op, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-508, -30, "Lote:  " . $linha[0]->Lote, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-496, -50, "Validade:  " . $Date, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-360, -50, "Contem:  " . intval($QtdPorEmbalagem), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-380, -30, date("d/m/Y H:i:s"), 0, 0, "C");
        $pdf->SetFont("", "", 60); //Times
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-338, -105, $turno, 0, 0, "C");
        //Etiqueta da direita
        $pdf->SetY(5);
        $pdf->SetX(145);
        $pdf->Cell(114, 65, '', 0, 1, 'R');
        $pdf->Code39(147, 18, substr($op, 0, 8), true, false, 0.4, 10, true);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(-227, -10, "Bomix Divisao Sopro", 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(-100, -10, "Matricula:  " . $matricula, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->SetFont("", "B", 16);
        $pdf->Cell(0, 0, "", 0, 0, "C");
        if (strlen($linha[0]->Produto) > '37') {
            $pdf->Cell(-180, -120, substr($linha[0]->Produto, 0, 30), 0, 0, "C");
            $pdf->Cell(0, 0, "", 0, 0, "C");
            $pdf->Cell(-145, -109, substr($linha[0]->Produto, 30, 60), 0, 0, "C");
        } else {
            $pdf->Cell(-180, -120, $linha[0]->Produto, 0, 0, "C");
        }
        $pdf->Cell(0, -58, "", 0, 0, "C");
        $pdf->Cell(-241, -65, $id, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-102, -65, "OP:  " . $op, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-240, -30, "Lote:  " . $linha[0]->Lote, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-228, -50, "Validade:  " . $Date, 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-88, -50, "Contem:  " . intval($QtdPorEmbalagem), 0, 0, "C");
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-107, -30, date("d/m/Y H:i:s"), 0, 0, "C");
        $pdf->SetFont("", "", 60); //Times
        $pdf->Cell(0, 0, "", 0, 0, "C");
        $pdf->Cell(-68, -105, $turno, 0, 0, "C");


        $pdf->Output();


        exit;
    }
}
