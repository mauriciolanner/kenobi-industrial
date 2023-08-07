<template>
    <app-layout title="Etiqueta Mecalux">
        <template #header>
            <h2 class="h4 font-weight-bold">Etiqueta Mecalux - {{ selectRecurso }}</h2>
        </template>

        <p class="d-inline-flex gap-1">
            <button class="btn " type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                aria-expanded="false" aria-controls="collapseExample">
                <i style="font-size: 17px;" class="bi bi-arrows-expand"></i> Escolher recurso
            </button>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="row">
                <div class="col-md-1" v-for="rec in recursos">
                    <button @click="trocaRecurso(rec)" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                        aria-expanded="false" aria-controls="collapseExample" class="btn btn-info w-100 mb-2"
                        style="border-radius: 9px;">
                        {{ rec }}
                    </button>
                </div>
            </div>
        </div>


        <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top">
            <div class="row mb-3">
                <div class="col-md-3">
                    <VueMultiselect v-model="selectRecurso" :placeholder="'Por recurso'" :selectedLabel="'Selecionado'"
                        :deselectLabel="'remover'" @select="dadosConsulta(page)" :selectLabel="'Selecionar'"
                        :options="recursos">
                    </VueMultiselect>
                </div>
                <div class="col-6">
                    <jet-input-search id="buscar" v-on:keyup.enter="buscar" type="text" v-model="buscador"
                        placeholder="Buscar..." />
                </div>
                <div class="col-1">
                    <button class="btn btn-success" @click="buscar">
                        <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <i v-else class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-1">
                    <button class="btn w-100 btn-info" @click="buscar">
                        <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <i v-else class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-padrao">
                        <thead>
                            <tr>
                                <th scope="col">OP</th>
                                <th scope="col">MES</th>
                                <th scope="col">Data Mov</th>
                                <th scope="col">Quant</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Receita</th>
                                <th scope="col">Recurso</th>
                                <th scope="col">Impresso em</th>
                                <th scope="col">Opções</th>
                            </tr>
                        </thead>
                        <tbody v-if="true">
                            <tr v-for="consulta in consultas.data">
                                <td>{{ consulta.OP }}</td>
                                <td>{{ consulta.APONTAMENTO_MES }}</td>
                                <td>{{ dateTask(consulta.DtMov) }}</td>
                                <td>{{ parseInt(consulta.QUANTIDADE) }}</td>
                                <td>{{ consulta.PRODUTO }}</td>
                                <td>{{ consulta.RECEITA }}</td>
                                <td>{{ consulta.RECURSO }}</td>
                                <td>
                                    <div v-if="consulta.IMPRESSO == 1" class="impresso bg-success text-center">
                                        <i class="bi bi-printer-fill" style="font-size: 19px;"></i><br>
                                        {{ dateTask(consulta.updated_at) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-printer" style="font-size: 19px;"></i>
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
                                            <li v-if="$page.props.user.user_name != 'producao'"><a
                                                    class="dropdown-item fs-3" href="#"
                                                    @click="goPrint(consulta.CODIGO_APONTAMENTO, 'PDF')">EM PDF</a></li>
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
                                            <span class="visually-hidden">Loading.....</span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tbody>
                    </table>

                    <!-- ModalAnexos -->
                    <div class="modal fade" id="modalAnexos" tabindex="-1" aria-labelledby="modalAnexosLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header text-end">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe :src="attSrc" height="450px" width="100%"></iframe>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                        <strong>Sucesso!</strong> <i class="bi bi-printer"></i> Etiqueta impressa com sucesso.
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
        setInterval(() => this.dadosConsulta(this.page), 1000 * 20);
    },
    methods: {
        buscar() {
            this.dadosConsulta(this.page)
        },
        async goPrint(cod, printer) {
            this.loading = true
            this.erroPrint = false
            this.successPrint = false
            await axios.get(route('mecalux.apontamentoPdf', [cod, printer]))
                .then(response => {
                    this.successPrint = true
                    this.loading = false
                    this.dadosConsulta(this.page)
                    if (printer == 'PDF')
                        this.viewDoc(cod)
                }).catch(function (error) {
                    this.erroPrint = true
                    this.loading = false
                })
        },
        dateTask(dateIni) {
            if (dateIni == null)
                return '';

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
                ' ' + date.getHours() +
                ':' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + '';

            return retorno;
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
        trocaRecurso(rec) {
            this.selectRecurso = rec
            console.log(this.selectRecurso)
            this.dadosConsulta(this.page)
        },
        cancelaAxios() {
            for (var i = 0; i < this.source.length; i++)
                this.source[i].cancel('Operação ' + i + ' cancelada pelo usuário');
        },
        async dadosConsulta(page) {
            if (this.source.length > 0)
                this.cancelaAxios();

            this.CancelToken = axios.CancelToken;
            this.source.push(this.CancelToken.source());
            var position = this.source.length - 1
            this.page = page
            this.loading = true

            await axios.get(route('mecalux.apiEtiquetas'), {
                cancelToken: this.source[position].token,
                params: {
                    busca: this.buscador,
                    page: this.page,
                    recurso: this.selectRecurso
                }
            }).then(response => {
                this.consultas = response.data;
                this.loading = false
            }).catch(function (thrown) {
                if (axios.isCancel(thrown)) {
                    console.log('AXIOS CANCELADO', thrown.message);
                    this.loading = false
                } else {
                    // manipulando erro
                }
            });
        },
        viewDoc(arquivo) {
            console.log(this.asset + "public/PDF/" + arquivo + '.pdf')
            this.attSrc = this.asset + "public/PDF/" + arquivo + '.pdf';
            var modalAtt = new bootstrap.Modal(document.getElementById("modalAnexos"), {});
            modalAtt.show()
        },
    },
    props: ['recurso', 'recursos', 'asset'],
    data() {
        return {
            CancelToken: '',
            source: [],
            attSrc: '',
            erroPrint: false,
            successPrint: false,
            page: 1,
            loading: false,
            consultas: [],
            buscador: '',
            selectRecurso: this.recurso,
            // recursos: [
            //     '180-03', '380-22', '550-13', '380-06', '180-02', '380-21', '160-03', '220-04', '180-04', '180-07', '380-10', '380-14', '160-02', '550-14', '380-02',
            //     '550-08', '550-09', '180-05', '550-07', '550-05', '380-01', '550-06', '380-16', '550-21', '160-01', '180-06', '380-08', '380-12', '220-01', '550-18',
            //     '550-19', '550-17', '380-05', '550-12', '380-18', '380-20', '380-11', '550-20', '550-22', '380-07', '550-16', '380-03', '380-09', '380-15', '380-04',
            //     '180-01', '380-19', '160-04', '220-02', '220-03', '380-17', '550-15', '550-10', '380-13', '680-01', '550-11',
            // ]
        };
    },
});
</script>