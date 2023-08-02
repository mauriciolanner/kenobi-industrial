<template>
    <app-layout title="Etiqueta Mecalux">
        <template #header>
            <h2 class="h4 font-weight-bold">Etiqueta Mecalux</h2>
        </template>


        <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
            <div class="row mb-3">
                <div class="col-md-3">
                    <VueMultiselect v-model="selectRecurso" :placeholder="'Por recurso'" :selectedLabel="'Selecionado'"
                        :deselectLabel="'remover'" @select="dadosConsulta(page)" :selectLabel="'Selecionar'"
                        :options="recursos">
                    </VueMultiselect>
                </div>
                <div class="col-md-6">
                    <jet-input-search id="buscar" type="text" v-model="buscador" placeholder="Buscar..." />
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success" @click="buscar"><i class="bi bi-search"></i></button>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-padrao table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">OP</th>
                                <th scope="col">Cod Apont.</th>
                                <th scope="col">MES</th>
                                <th scope="col">Data Mov</th>
                                <th scope="col">Quant</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Receita</th>
                                <th scope="col">Recurso</th>
                                <th scope="col">Opções</th>
                            </tr>
                        </thead>
                        <tbody v-if="true">
                            <tr v-for="consulta in consultas.data" :class="{ 'loading-tabel': loading }">
                                <td>{{ consulta.OP }}</td>
                                <td>{{ consulta.CODIGO_APONTAMENTO }}</td>
                                <td>{{ consulta.APONTAMENTO_MES }}</td>
                                <td>{{ consulta.DtMov }}</td>
                                <td>{{ parseInt(consulta.QUANTIDADE) }}</td>
                                <td>{{ consulta.PRODUTO }}</td>
                                <td>{{ consulta.RECEITA }}</td>
                                <td>{{ consulta.RECURSO }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item fs-3" href="#"
                                                    @click="goPrint(consulta.CODIGO_APONTAMENTO, 38)">Impressora 38</a></li>
                                            <li><a class="dropdown-item fs-3" href="#"
                                                    @click="goPrint(consulta.CODIGO_APONTAMENTO, 39)">Impressora 39</a></li>
                                            <li><a class="dropdown-item fs-3" href="#"
                                                    @click="goPrint(consulta.CODIGO_APONTAMENTO, 40)">Impressora 40</a></li>
                                            <li><a class="dropdown-item fs-3" href="#"
                                                    @click="goPrint(consulta.CODIGO_APONTAMENTO, 41)">Impressora 41</a></li>
                                        </ul>
                                    </div>
                                    <!-- <a :href="route('mecalux.apontamentoPdf', consulta.CODIGO_APONTAMENTO.trim())"
                                        @click="dadosConsulta(page)" target="blank" class="btn btn-info"><i
                                            class="bi bi-printer"></i></a> -->
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <th scope="col text-center" colspan="9">
                                    <div class="text-center">
                                        <div class="spinner-border text-info" style="width: 5rem; height: 5rem;"
                                            role="status">
                                            <span class="visually-hidden">Loading....</span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tbody>
                    </table>

                    <div class="col-md-12">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-end">
                                <li v-for="(link, index) in consultas.links" class="page-item"
                                    v-bind:class="{ active: link.active }">
                                    <a href="#" @click="dadosConsulta(link.label)"
                                        v-if="Number.isInteger(parseInt(link.label))" class="page-link">
                                        {{ link.label }}
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div v-if="erroPrint" class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erro na impressão!</strong> Ouve um erro ao tentar imprimir, tente novamente.
                        <button type="button" class="btn-close" @click="erroPrint = false"></button>
                    </div>
                    <div v-if="successPrint" class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sucesso!</strong> Etiqueta impressa com msucesso.
                        <button type="button" class="btn-close" @click="successPrint = false"></button>
                    </div>

                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetInputSearch from "@/Jetstream/Input.vue";
import VueMultiselect from 'vue-multiselect';

export default defineComponent({
    components: {
        AppLayout,
        JetInputSearch,
        VueMultiselect
    },
    mounted() {
        this.dadosConsulta(this.page);
        setInterval(() => this.dadosConsulta(this.page), 1000 * 10);
    },
    methods: {
        buscar() {
            this.dadosConsulta(this.page)
        },
        goPrint(cod, printer) {
            this.erroPrint = false
            this.successPrint = false
            axios.get(route('mecalux.apontamentoPdf', [cod, printer])).then(response => {
                this.successPrint = true
            }).catch(function (error) {
                this.erroPrint = true
            })
        },
        formataData(dateIni) {
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

            return date.getDate() + ' ' + mes + ', ' + date.getFullYear();
        },
        async dadosConsulta(page) {
            this.page = page
            this.loading = true
            await axios.get(route('mecalux.apiEtiquetas'), {
                params: {
                    busca: this.buscador,
                    page: this.page,
                    recurso: this.selectRecurso
                }
            }).then(response => {
                this.consultas = response.data;
                this.loading = false
            });
        }
    },
    props: [],
    data() {
        return {
            erroPrint: false,
            successPrint: false,
            page: 1,
            loading: false,
            consultas: [],
            buscador: '',
            selectRecurso: '',
            recursos: [
                '180-03', '380-22', '550-13', '380-06', '180-02', '380-21', '160-03', '220-04', '180-04', '180-07', '380-10', '380-14', '160-02', '550-14', '380-02',
                '550-08', '550-09', '180-05', '550-07', '550-05', '380-01', '550-06', '380-16', '550-21', '160-01', '180-06', '380-08', '380-12', '220-01', '550-18',
                '550-19', '550-17', '380-05', '550-12', '380-18', '380-20', '380-11', '550-20', '550-22', '380-07', '550-16', '380-03', '380-09', '380-15', '380-04',
                '180-01', '380-19', '160-04', '220-02', '220-03', '380-17', '550-15', '550-10', '380-13', '680-01', '550-11',
            ]
        };
    },
});
</script>