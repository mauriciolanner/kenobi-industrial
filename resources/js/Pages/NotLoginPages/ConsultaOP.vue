<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success w-100 mt-4 btn-camera" @click="camera = !camera, pallet = null"><i
                        class="bi bi-camera-fill"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-white card-login shadow-sm p-3 mt-4">
                    <div class="row">
                        <div class="col-10">
                            <label class="form-label">Num etiqueta / Num palete</label>
                            <input type="text" class="form-control" v-model="codigo" v-on:keyup.enter="carregaDados">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-info w-100" @click="carregaDados" style="margin-top: 29px;">
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
                    <div class="row" v-if="pallet">
                        <!-- <h3>{{ codigo }}</h3> -->
                        <div v-if="pallet.status" class="col-md-12">
                            <div class="card bg-success card-login shadow-sm p-3 mt-4 mb-4" :class="{
                                'bg-success': pallet.pallet[0].LOCATIONCODE == 'LostFound',
                                'bg-success': pallet.pallet[0].LOCATIONCODE == 'LostFound',
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
                            Nada encontrado
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
            camera: false,
            codigo: '',
            formato: ["code_128", "code_39", "code_93"],
            loadingDados: false,
            pallet: null
        }
    },
    methods: {
        async carregaDados() {
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
        onDetect(detectedCodes) {
            this.codigo = detectedCodes[0].rawValue;
            this.camera = false;
            this.carregaDados();
        },
        onError(detectedCodes) {
            this.codigo = detectedCodes;
        },
    }
})
</script>
  