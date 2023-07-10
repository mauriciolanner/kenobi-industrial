<template>
    <app-layout title="Etiqueta Mecalux">
        <template #header>
            <h2 class="h4 font-weight-bold">Etiqueta Mecalux</h2>
        </template>


        <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
            <div class="row mb-3">

                <div class="col-md-12">
                    <jet-input-search id="buscar" :class="'mt-3'" type="text" v-model="buscador" @keyup="buscar"
                        placeholder="Buscar..." />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-padrao">
                        <thead>
                            <tr>
                                <th scope="col">Cod Apont.</th>
                                <th scope="col">MES</th>
                                <th scope="col">Data Mov</th>
                                <th scope="col">Quant</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Receita</th>
                                <th scope="col">OP</th>
                                <th scope="col">Recurso</th>
                                <th scope="col">Opções</th>
                            </tr>
                        </thead>
                        <tbody v-if="!loading">
                            <tr v-for="consulta in consultas.data">

                                <td>{{ consulta.CODIGO_APONTAMENTO }}</td>
                                <td>{{ consulta.APONTAMENTO_MES }}</td>
                                <td>{{ consulta.DtMov }}</td>
                                <td>{{ consulta.QUANTIDADE }}</td>
                                <td>{{ consulta.PRODUTO }}</td>
                                <td>{{ consulta.RECEITA }}</td>
                                <td>{{ consulta.OP }}</td>
                                <td>{{ consulta.RECURSO }}</td>
                                <td>
                                    <a :href="route('apontamento.pdf.matriz', consulta.CODIGO_APONTAMENTO.trim())"
                                        target="blank" class="btn btn-info"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <th scope="col text-center" colspan="9">
                                    <div class="text-center">
                                        <div class="spinner-border text-info" style="width: 5rem; height: 5rem;"
                                            role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-12">

                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                            <li v-for="(link, index) in consultas.links" class="page-item"
                                v-bind:class="{ active: link.active }">
                                <Link href="#" @click="dadosConsulta(link.label)"
                                    v-if="Number.isInteger(parseInt(link.label))" class="page-link">
                                {{ link.label }}
                                </Link>
                            </li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetInputSearch from "@/Jetstream/Input.vue";

export default defineComponent({
    components: {
        AppLayout,
        JetInputSearch,
    },
    mounted() {
        this.dadosConsulta(this.page);
        //setInterval(() => this.dadosConsulta(), 1000 * 15);
    },
    methods: {
        buscar() {
            this.dadosConsulta(this.page)
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
                    page: this.page
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
            page: 1,
            loading: false,
            consultas: [],
            buscador: '',
        };
    },
});
</script>