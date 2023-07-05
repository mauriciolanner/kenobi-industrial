<template>
    <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
        <div class="row">
            <div class="col-12">
                <h3><i class="bi bi-cash-coin"></i> Boletos:</h3>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-md-6">
                <label for="boleto" class="form-label">Busca boleto</label>
                <input type="text" v-model="busca" class="form-control" id="boleto">
            </div>
            <div class="col-md-2">
                <label for="dataInicio" class="form-label">Data inicio</label>
                <input type="date" v-model="dataInicio" class="form-control" id="dataInicio">
            </div>
            <div class="col-md-2">
                <label for="dataFim" class="form-label">Data fim</label>
                <input type="date" v-model="dataFim" class="form-control" id="dataFim">
            </div>
            <div class="col-md-2">
                <button type="button" @click="search()" class="btn btn-success mt-30">Buscar</button>
                <button type="button" @click="limpar()" style="margin-left: 10px"
                    class="btn btn-info mt-30">Limpar</button>
            </div>
        </div> -->
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Documento</th>
                            <th scope="col">Parcela</th>
                            <th scope="col">Emissão</th>
                            <th scope="col">Vencimento</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Opções</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="boleto in boletosNatural" :key="boletos.R_E_C_N_O_">
                            <th scope="row">{{  boleto.E1_NUM  }}</th>
                            <td>{{  boleto.E1_PARCELA  }}</td>
                            <td>{{  forData(boleto.E1_EMISSAO)  }}</td>
                            <td>{{  forData(boleto.E1_VENCTO)  }}</td>
                            <td>{{  parseFloat(boleto.E1_VALOR).toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            })

                                }}
                            </td>
                            <td>
                                <span v-if="boleto.E1_BAIXA.trim() == '' && verificaBoleto(boleto.E1_VENCTO) == false"
                                    class="badge bg-success">
                                    ABERTO
                                </span>
                                <span v-if="verificaBoleto(boleto.E1_VENCTO)" class="badge bg-danger">
                                    VENCIDO
                                </span>
                            </td>
                            <td class="text-end">
                                <!--Link :href="'/boleto/download/' + boleto.E1_NUM + '/' + boleto.E1_PARCELA"-->
                                <button class="btn-icon btn-info" @click="download(boleto.E1_NUM, boleto.E1_PARCELA)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <!--/Link-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="col-md-12" id="erro">
                    <div class="alert alert-dismissible fade show" v-show="erro" :class="'alert-danger'" role="alert">
                        <strong>Arquivo não encontrado!</strong> <br> Entre em contado com o setor comercial.
                        <button type="button" class="btn-close" @click="erro = false"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    components: {
        Link,
    },
    props: ['boletos', 'id_cliente'],
    methods: {
        forData(data) {
            return data.substr(6, 2) + "." + data.substr(4, 2) + "." + data.substr(0, 4);
        },
        search() {
            axios.get("/boletos/" + this.id_cliente + "/?busca=" + this.busca + "&dataInicio=" + this.dataInicio + "&dataFim=" + this.dataFim)
                .then(response => {
                    console.log(response.data.boletos);
                    this.boletosNatural = response.data.boletos;
                });
        },
        limpar() {
            this.busca = '';
            this.dataInicio = '';
            this.dataFim = '';
            this.search();
        },
        verificaBoleto(data) {
            var retorno = false;
            var compara = new Date(data.substr(0, 4) + "-" + data.substr(4, 2) + "-" + data.substr(6, 2))
            var hoje = new Date();

            if (compara < hoje)
                retorno = true

            return retorno;
        },
        download(boleto, parcela) {

            axios.get('/boleto/download/' + boleto + '/' + parcela, { responseType: 'blob' })
                .then(response => {
                    console.log(response);
                    let blob = new Blob([response.data], { type: 'application/pdf' }),
                        url = window.URL.createObjectURL(blob)
                    window.open(url);
                }).catch((error) => {
                    this.erro = true
                });

            // console.log(this.$inertia + '/boleto/download/' + boleto + '/' + parcela);
            // this.$inertia.get('/boleto/download/' + boleto + '/' + parcela,
            //     { preserveState: true }
            // );
        },
    },
    data() {
        return {
            busca: '',
            dataInicio: '',
            dataFim: '',
            page: 1,
            erro: false,
            boletosNatural: this.boletos
        };
    }
})
</script>