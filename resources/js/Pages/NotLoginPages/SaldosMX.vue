<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-white card-login shadow-sm p-3 mt-4">
                    <div class="row">
                        <div class="col-10">
                            <label class="form-label">Código do produto</label>
                            <input type="text" class="form-control" v-model="codigo" v-on:keyup.enter="carregaDados">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-info w-100" @click="carregaDados" style="margin-top: 29px;">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <i v-else class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-white card-login shadow-sm p-3 mt-4 mb-4">
                    <div v-if="dados.length > 0">
                        <h5>{{ dados[0].DESCRICAO }}</h5>
                    </div>
                    <div class="card card-login shadow-sm p-3 mt-4 bg-success" v-if="dados.length > 0">
                        <h5>SALDO MECALUX: {{ parseInt(dados[0].QUANT_MECALUX) }}</h5>
                    </div>
                    <div class="card card-login shadow-sm p-3 mt-4 mb-4 bg-success" v-if="dados.length > 0">
                        <h5>SALDO PROTHEUS: {{ total }}</h5>
                    </div>

                    <table v-if="dados.length > 0" class="table table-striped table-padrao">
                        <thead>
                            <tr>
                                <th scope="col">Lote</th>
                                <th scope="col">Validade</th>
                                <th scope="col">Armazém</th>
                                <th scope="col">Saldo</th>
                                <th scope="col">Empenho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lote in dados">
                                <td>{{ lote.LOTE }}</td>
                                <td>{{ lote.DT_VALIDADE }}</td>
                                <td>{{ lote.ARMAZEM }}</td>
                                <td>{{ lote.QUANT_PROTHEUS }}</td>
                                <td>{{ lote.EMPENHO }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
  
  
<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    created: () => {
    },
    components: {
        Head,
        Link
    },

    props: {},

    data() {
        return {
            codigo: '',
            dados: [],
            loading: false,
            total: 0
        }
    },
    methods: {
        somaMx() {
            this.total = 0
            this.dados.forEach((nodo, index) => {
                if (nodo.ARMAZEM == 'MX')
                    this.total += parseInt(nodo.QUANT_PROTHEUS);
            })
        },
        async carregaDados() {
            this.loading = true
            await axios.get(route('API.conuslta.saldoMX'), {
                params: {
                    codigo: this.codigo
                }
            }).then(response => {
                this.dados = response.data;
                this.loading = false
                this.somaMx()
            });
        }
    }
})
</script>
  