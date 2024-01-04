<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success w-100 mt-4 btn-camera" @click="camera = !camera, pallet = null">
                    <i class="bi bi-camera-fill"></i></button>
            </div>
            <div class="col-md-12">
                <button class="btn btn-info w-100 mt-4 btn-camera" @click="comparar = !comparar" :class="{ 'btn-success': comparar }">
                    <h4>
                        <div v-if="comparar">Desativar Comparar</div>
                        <div v-else>Ativar Comparar</div>
                    </h4>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-white card-login shadow-sm p-3 mt-4">
                    <div class="row">
                        <div class="col-10">
                            <label class="form-label">Num etiqueta / Num palete</label>
                            <input type="text" class="form-control" v-model="codigo" v-on:keyup.enter="modoCompara()">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-info w-100" @click="modoCompara()" style="margin-top: 29px;">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div v-if="camera" class="card bg-white card-login shadow-sm p-3 mt-4">
                    <qrcode-stream width="100%" :formats="formato" :track="paintOutline" @detect="onDetect"
                        @error="onError">
                        <!-- <button @click="switchCamera">
                            <img :src="'http://localhost/bomixKenobi/img/camera.svg'" alt="switch camera" />
                        </button> -->
                    </qrcode-stream>
                </div>
                <div class="card bg-white card-login shadow-sm p-3 mt-4 mb-4">
                    <div class="row" v-if="loadingDados">
                        <div class="col-md-12 text-center">
                            <div class="spinner-border text-info" style="width: 5rem; height: 5rem" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="pallet && pallet.pallet">
                        <!-- <h3>{{ codigo }}</h3> -->
                        <div v-if="pallet.status" class="col-md-12">
                            <div class="card card-login shadow-sm p-3 mt-4 mb-4 text-center" :class="{
                                'bg-success text-light': success.includes(pallet.pallet[0].SOLUCAO),
                                'bg-warning': warning.includes(pallet.pallet[0].SOLUCAO),
                                'bg-info text-light': info.includes(pallet.pallet[0].SOLUCAO),
                                'bg-danger text-light': danger.includes(pallet.pallet[0].SOLUCAO)
                            }">
                                <h4>{{ pallet.pallet[0].SOLUCAO }}</h4>
                            </div>
                            <ul class="list-group" v-for="  pallet   in   pallet.pallet  ">
                                <li class="list-group-item"><b>Apontamento MES: </b>{{ pallet.APONTAMENTO_MES }}</li>
                                <li class="list-group-item"><b>OP: </b>{{ pallet.OP }}</li>
                                <li class="list-group-item"><b>Integrado Mecalux: </b>{{ pallet.RESULTADO }}</li>
                                <li class="list-group-item"><b>Status Mecalux: </b>{{ pallet.StatusMecalux }}</li>
                                <li class="list-group-item"><b>Localização: </b>{{ pallet.LOCATIONCODE }}</li>
                                <li class="list-group-item"><b>Histórico: </b>{{ pallet.Historico }}</li>
                                <li class="list-group-item"><b>Id Mecalux: </b>{{ pallet.ZF9_FSCMID }}</li>
                                <li class="list-group-item"><b>Data integração Mecalux: </b>{{ pallet.ZF9_DATA }}</li>
                                <li class="list-group-item"><b>Hora integração mecalux: </b>{{ pallet.ZF9_HORA }}</li>
                                <li class="list-group-item"><b>Erro de integração MES: </b>{{ pallet.ERRO_INTEGRACAO }}</li>
                                <li class="list-group-item"><b>Estorno MES: </b>{{ pallet.ESTORNO_MES }}</li>
                                <li class="list-group-item"><b>Status: </b>{{ pallet.STATUS }}</li>
                                <li class="list-group-item"><b>Cod ST: </b>{{ pallet.COD_ST }}</li>
                                <li class="list-group-item"><b>Data/Hora: </b>{{ pallet.DATA_HORA }}</li>
                                <li class="list-group-item"><b>Produto: </b>{{ pallet.PRODUTO }}</li>
                                <li class="list-group-item"><b>Descrição: </b>{{ pallet.DESCRICAO }}</li>
                                <li class="list-group-item"><b>Qtd: </b>{{ pallet.QUANTIDADE }}</li>
                                <li class="list-group-item"><b>Receita: </b>{{ pallet.RECEITA }}</li>
                                <li class="list-group-item"><b>Recurso: </b>{{ pallet.RECURSO }}</li>
                                <li class="list-group-item"><b>Etiqueta Palet: </b>{{ pallet.ST_PALETE }}</li>
                                <li class="list-group-item"><b>Etiqueta Deletada: </b>{{ pallet.ST_DELETADA }}</li>
                                <li class="list-group-item"><b>Status: </b>{{ pallet.ZF9_STATUS }}</li>
                            </ul>
                        </div>
                        <div v-if="!pallet.status" class="col-md-12">
                            <h4>Nada encontrado</h4>
                        </div>
                    </div>
                    <div class="row" v-if="pallet && pallet.op && pallet.status">
                        <div class="col-md-12">
                            <b>OP: {{ pallet.op[0].OP }}</b><br>
                            <b>Cod Prod: {{ pallet.op[0].PRODUTO }}</b><br>
                            <b>Desc: {{ pallet.op[0].DESCRICAO }}</b><br>
                            <b>Pallets:</b>
                            <ul class="list-group mt-3" v-for="  op   in   pallet.op  ">
                                <li class="list-group-item"><b>Cod apontamento: </b>{{ op.CODIGO_APONTAMENTO }}</li>
                                <li class="list-group-item"><b>MES: </b>{{ op.APONTAMENTO_MES }}</li>
                                <li class="list-group-item"><b>Solução: </b>{{ op.SOLUCAO }}</li>
                                <li class="list-group-item"><b>Status Mecalux: </b>{{ op.STATIONBASECODE }}</li>
                                <li class="list-group-item"><b>Integração MES: </b>{{ op.ID_INTEGRACAO_MES }}</li>
                                <li class="list-group-item"><b>Data Movimento: </b>{{ dateTask(op.DtMov) }}</li>
                                <li class="list-group-item"><b>Recurso: </b>{{ op.RECURSO }}</li>
                                <li class="list-group-item"><b>Qtd: </b>{{ parseInt(op.QUANTIDADE) }}</li>
                                <li class="list-group-item"><b>Data/Hora apontamento: </b>{{ dateTask(op.DATA_HORA) }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row" v-if="resultComprar != null">
                        <div class="col-md-12">
                            <div class="card card-login shadow-sm p-3 mt-4 mb-4 text-center" :class="{
                                'bg-success': resultComprar,
                                'bg-danger text-light': !resultComprar
                            }">
                                <h4 v-if="resultComprar">Pallet Liberado</h4>
                                <h4 v-else>Pallet divergente</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
  
<script>
import { defineComponent } from 'vue'
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
import { Head, Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    created: () => {
    },
    components: {
        Head,
        Link,
        QrcodeStream,
        QrcodeDropZone,
        QrcodeCapture
    },

    props: {},

    data() {
        return {
            comparar: false,
            etiquetaUm: null,
            etiquetaDois: null,
            resultComprar: null,
            camera: false,
            codigo: '',
            formato: ["code_128", "code_39", "code_93", "qr_code"],
            loadingDados: false,
            pallet: null,
            success: [
                'Palete Liberado para recepção.',
                'LOGISTICA: Palete Liberado para recepção.'
            ],
            warning: [
                'PRODUÇÃO: Possível etiqueta duplicada, se for o caso, reetiquetar.',
                'PRODUÇÃO: Palete com pendência de insumo.',
                'PRODUÇÃO: Palete em movimentação ou com etiqueta duplicada, se for o caso, re-etiquetar.',
                'PRODUÇÃO: Possível etiqueta duplicada, se for o caso, re-etiquetar.',
                'LOGISTICA: Palete vindo do PS(doca), necessário solicitação de transferência manual.',
                'LOGISTICA: Palete em movimentação ou com etiqueta duplicada, se for o caso, re-etiquetar.',
                'PRODUÇÃO: Apontado a maior, necessário estornar e re-apontar com a quantidade base correta.',
                'PRODUÇÃO: Etiqueta errada, necessário re-etiquetar.',
                'PRODUÇÃO: Etiqueta errada, necessário reetiquetar.',
                'PRODUÇÃO: Integração MES pendente.',
                'PRODUÇÃO: Palete com pendência de insumo.',
                'PRODUÇÃO: Apontado a maior, necessário estornar e reapontar com a quantidade base correta.',
                'LOGISTICA: Palete vindo da doca, necessário solicitação de transferência manual.',
                'Palete em área não mapeada, possível movimentação, entrar em contato com operador Mecalux.'
            ],
            info: [
                'Palete vindo da área de picking, liberado para recepção.',
                'Palete em Movimentação, entrar em contato com operador Mecalux.',
            ],
            danger: [
                'TI: Necessário analise de TI.',
                'TI: Pendência no cadastro do produto.'
            ]
        }
    },
    methods: {
        modoCompara() {
            if (this.comparar)
                this.compararEtiqueta()
            else
                this.carregaDados()
        },
        async carregaDados() {
            this.mensagemRetorno = ''
            this.loadingDados = true
            await axios.get(route('API.conuslta.OP'), {
                params: {
                    op: this.codigo
                }
            }).then(response => {
                this.pallet = response.data;
                this.loadingDados = false
            });
        },
        compararEtiqueta() {
            if (this.etiquetaUm == null)
                this.etiquetaUm = this.codigo
            else if (this.etiquetaDois == null)
                this.etiquetaDois = this.codigo
            if (this.etiquetaUm != null && this.etiquetaDois != null)
                this.resultComprar = (this.etiquetaUm.substring(0, 8) == this.etiquetaDois.substring(0, 8))

        },
        paintOutline(detectedCodes, ctx) {
            for (const detectedCode of detectedCodes) {
                const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

                ctx.strokeStyle = 'red'

                ctx.beginPath()
                ctx.moveTo(firstPoint.x, firstPoint.y)
                for (const { x, y } of otherPoints) {
                    ctx.lineTo(x, y)
                }
                ctx.lineTo(firstPoint.x, firstPoint.y)
                ctx.closePath()
                ctx.stroke()
            }
        },
        dateTask(dateIni) {
            var date = new Date(dateIni);
            var mes = '';
            (date.getMonth() == 0) ? mes = 'Jan' : '';
            (date.getMonth() == 1) ? mes = 'Fev' : '';
            (date.getMonth() == 2) ? mes = 'Mar' : '';
            (date.getMonth() == 3) ? mes = 'Abr' : '';
            (date.getMonth() == 4) ? mes = 'Mai' : '';
            (date.getMonth() == 5) ? mes = 'Jun' : '';
            (date.getMonth() == 6) ? mes = 'Jul' : '';
            (date.getMonth() == 7) ? mes = 'Ago' : '';
            (date.getMonth() == 8) ? mes = 'Set' : '';
            (date.getMonth() == 9) ? mes = 'Out' : '';
            (date.getMonth() == 10) ? mes = 'Nov' : '';
            (date.getMonth() == 11) ? mes = 'Dez' : '';

            var retorno = date.getDate() + ' ' + mes +
                ', ' + date.getFullYear() +
                ' ' + (date.getHours() + 3) +
                ':' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + '';

            return retorno;
        },
        onDetect(detectedCodes) {
            if ((this.etiquetaUm != null && this.etiquetaDois != null) || !this.comparar) {
                this.etiquetaUm = null
                this.etiquetaDois = null
                this.resultComprar = null
            }

            this.codigo = detectedCodes[0].rawValue;
            this.camera = false;
            this.modoCompara();
        },
        onError(detectedCodes) {
            this.codigo = detectedCodes;
        },
    }
})
</script>
  