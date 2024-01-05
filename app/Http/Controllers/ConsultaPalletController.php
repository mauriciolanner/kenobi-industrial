<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ImpressaoMecalux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
        $opDados = null;
        $etiquetaToco = [];
        $dataReferencia = Carbon::now()->subDays(90)->format('Y-m-d');

        if ($request->op != '') {
            if (strlen($request->op) == 11) {
                $etiquetaToco = ImpressaoMecalux::where('IMPRESSO', '0')->where('DtMov', '>', $dataReferencia . ' 00:00:00.000000')
                    ->whereNull('ESTORNO')->where('OP', $request->op)->get();

                $opDados =  DB::connection('protheus')
                    ->select(DB::raw("SELECT 
                        /*Obs: Necessário seguir a ordem colocada */
                        SOLUCAO		= (CASE	
                                            WHEN ESTORNO_MES IS NOT NULL THEN 'PRODUÇÃO: Etiqueta errada, necessário re-etiquetar.'
                                            WHEN ERRO_INTEGRACAO LIKE '%saldos por lote suficientes%'  THEN 'PRODUÇÃO: Palete com pendência de insumo.'
                                            WHEN ERRO_INTEGRACAO LIKE '%9999-TOTVS%'  THEN 'PRODUÇÃO: Integração MES pendente.'
                                            WHEN ISNULL(CAST(CAST(ZF9_RESULT AS VARBINARY(8000)) AS VARCHAR(8000)),'') LIKE '%Esperava-se que o contentor%' THEN 'PRODUÇÃO: Possível etiqueta duplicada, se for o caso, re-etiquetar.'
                                            WHEN ISNULL(CAST(CAST(ZF9_RESULT AS VARBINARY(8000)) AS VARCHAR(8000)),'') LIKE '%quantidade de completo de tipo de contentor da conversao de artigo foi ultrapassada%'  THEN 'PRODUÇÃO: Apontado a maior, necessário estornar e re-apontar com a quantidade base correta.' --'TI: Necessário análise de TI.'
                                            WHEN ISNULL(CAST(CAST(ZF9_RESULT AS VARBINARY(8000)) AS VARCHAR(8000)),'') LIKE '%codigo de artigo%' THEN 'TI: Pendência no cadastro do produto.'
                                            
                                            WHEN LOCATIONCODE='PUL_PICKING'  THEN 'LOGISTICA: Palete Liberado para recepção.'
                                            WHEN LOCATIONCODE='Mov'  THEN 'PRODUÇÃO: Palete em movimentação ou com etiqueta duplicada, se for o caso, re-etiquetar.'
                                            WHEN LOCATIONCODE='LostFound' AND CONTAINERSTATUS='Available' AND ISACTIVE='1'  THEN 'LOGISTICA: Palete Liberado para recepção.'
                                            
                                            WHEN (LOCATIONCODE='LostFound' OR (CONTAINERSTATUS='PendingReception' AND ISACTIVE='1' AND LOCATIONCODE='Asn'))  THEN 'LOGISTICA: Palete Liberado para recepção.'
                                            WHEN LOCATIONCODE not in ('LostFound','','Asn','PUL_PICKING') AND ISNUMERIC(LOCATIONCODE)=0 AND CONTAINERSTATUS = 'Available' AND ISACTIVE='1'  THEN 'PRODUÇÃO: Palete em movimentação ou com etiqueta duplicada, se for o caso, re-etiquetar.'
                                            WHEN ISACTIVE='0'  THEN 'LOGISTICA: Palete vindo do PS(doca), necessário solicitação de transferência manual.'
                                            WHEN ISNUMERIC(LOCATIONCODE)=1 AND CONTAINERSTATUS='Available' THEN 'PRODUÇÃO: Possível etiqueta duplicada, se for o caso, re-etiquetar.'
                                            Else 'TI: Necessário análise de TI.'
                                        END),
                        --MECALUX
                        StatusMecalux		= (CASE
                                            WHEN CONTAINERSTATUS = 'Available' AND ISACTIVE='1'         THEN 'Disponível'
                                            WHEN CONTAINERSTATUS = 'PendingReception' AND ISACTIVE='1'  THEN 'Pendente de recepção'
                                            WHEN CONTAINERSTATUS = 'Available' AND ISACTIVE='0'         THEN ''
                                            Else CONTAINERSTATUS
                                        END)
                        ,LOCATIONCODE = (CASE
                                            WHEN ISACTIVE='1' THEN LOCATIONCODE --Se container estiver ativo exibe a localização
                                            Else ''
                                        END),
                        Historico = (CASE
                                            WHEN ISACTIVE='0' THEN 'SIM'
                                            Else ''
                                        END),
                
                        --INTEG. PROTHEUSxMECALUX
                        ZF9_STATUS, ZF9_FSCMID, ZF9_DATA, ZF9_HORA,ZF9_FSSTA as STATUS,MX.D_E_L_E_T_ as ZF9_DELETE,
                        ISNULL(CAST(CAST(ZF9_REQORI AS VARBINARY(8000)) AS VARCHAR(8000)),'') as REQ_ORIGEM,
                        ISNULL(CAST(CAST(ZF9_RESULT AS VARBINARY(8000)) AS VARCHAR(8000)),'') as RESULTADO, 
                        ISNULL(CAST(CAST(ZF9_ROTINA AS VARBINARY(8000)) AS VARCHAR(8000)),'') as ROTINA,
                        --VIEW MES
                        DATA_HORA, RECURSO, OP, APONTAMENTO_MES, RECEITA, QUANTIDADE, PRODUTO, DESCRICAO, ERRO_INTEGRACAO, LOWER(IDPCFACTORY) as IDPCFACTORY, ESTORNO_MES,
                        --ST
                        NNS_COD as COD_ST,ST.NNS_FSCPAL AS ST_PALETE, ST.D_E_L_E_T_ AS ST_DELETADA,
                        STATIONBASECODE,
                        --pallet
                        PALETES.CODIGO_APONTAMENTO,
                        PALETES.APONTAMENTO_MES,
                        PALETES.ID_INTEGRACAO_MES,
                        PALETES.DtMov,
                        PALETES.QUANTIDADE,
                        PALETES.PRODUTO,
                        PALETES.DESCRICAO,
                        PALETES.OP,
                        PALETES.ARMAZEM,
                        PALETES.RECURSO,
                        PALETES.DATA_HORA,
                        PALETES.COD_INTEG_MES,
                        PALETES.ESTORNO_MES
                      
                FROM
                    P12OFICIAL.dbo.BMX_VW_APONTAMENTO_MES PALETES 
                    LEFT JOIN P12OFICIAL.dbo.NNS010 ST ON PALETES.APONTAMENTO_MES=SUBSTRING( ST.NNS_FSCPAL,12,6)  
                    LEFT JOIN P12OFICIAL.dbo.ZF9010 MX ON ISNULL(CAST(CAST(ZF9_ROTINA AS VARBINARY(8000)) AS VARCHAR(8000)),'') LIKE '%ForASNErp%' AND
                    ISNULL(CAST(CAST(ZF9_REQORI AS VARBINARY(8000)) AS VARCHAR(8000)),'') LIKE '%'+CONVERT(varchar,PALETES.APONTAMENTO_MES,103)+'%'
                    LEFT JOIN db_read.dbo.Container ON CODE=ST.NNS_FSCPAL collate Latin1_General_CI_AS  AND WAREHOUSECODE='Bomix'
                WHERE 
                    OP='$request->op'
                ORDER BY
                    MX.ZF9_STATUS desc"));

                return response()->json([
                    'pallet' => $dados,
                    'status' => true,
                    'etiquetaMecalux' => $etiquetaToco,
                    'op' => $opDados
                ]);
            }


            if (strlen($request->op) > 6)
                $pallet = substr($request->op, 11, 6);
            else
                $pallet = $request->op;

            $dados =  DB::connection('protheus')->select(DB::raw("Exec P12Oficial.dbo.[Bomix_Rastreia_Palete] '$pallet'"));

            if (count($dados) > 0)
                return response()->json([
                    'pallet' => $dados,
                    'status' => true,
                    'etiquetaMecalux' => $etiquetaToco,
                    'op' => $opDados
                ]);
            else
                return response()->json([
                    'pallet' => [],
                    'status' => false,
                    'etiquetaMecalux' => $etiquetaToco,
                    'op' => $opDados
                ]);
        }

        return response()->json([
            'pallet' => $dados,
            'status' => false,
            'etiquetaMecalux' => $etiquetaToco,
            'op' => $opDados
        ]);
    }

    public function APIConsultaSaldoMX(Request $request)
    {
        $dados =  DB::connection('protheus')->select(DB::raw("SELECT * FROM BMX_VW_Compara_Lotes_Protheus_Mecalux where CODIGO = '$request->codigo'"));

        return response()->json($dados);
    }
}
