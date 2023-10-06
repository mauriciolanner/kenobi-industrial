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
                                    <div v-if="consulta.IMPRESSO == 1" class="impresso bg-success text-center"
                                        :class="{ 'bg-danger': consulta.ESTORNO == 'E' }">
                                        <i class="bi bi-printer-fill" style="font-size: 19px;"></i><br>
                                        {{ dateTask(consulta.updated_at) }}
                                    </div>
                                    <div v-if="consulta.ESTORNO == 'E'"
                                        class="impresso bg-secondary text-center text-light">
                                        Estornado
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown" v-if="consulta.ESTORNO != 'E' && (!consulta.IMPRESSO == 1)">
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
                                    <div v-if="consulta.ESTORNO != 'E' && consulta.IMPRESSO == 1">
                                        <button class="btn btn-info" type="button"
                                            @click="reprint(consulta.CODIGO_APONTAMENTO)">
                                            <i class="bi bi-printer" style="font-size: 19px;"></i>
                                        </button>
                                    </div>
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

                    <!-- modalReprint -->
                    <div class="modal fade" id="modalReprint" tabindex="-1" aria-labelledby="modalReprintLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header text-end">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h3>Autorização gerencial</h3>
                                    <div class="mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="login" name="login"
                                                placeholder="Nome de usuário" v-model="form.login">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="input-group mb-3">
                                            <input type="password" class="form-control" id="senha" placeholder="Senha"
                                                v-model="form.senha">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="input-group mb-3">
                                            <VueMultiselect v-model="form.impressora" :placeholder="'Esolher impressora'"
                                                :selectedLabel="'Selecionado'" :deselectLabel="'remover'"
                                                :selectLabel="'Selecionar'" :options="impressoras">
                                            </VueMultiselect>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" @click="reprintAxio()" class="btn btn-success">IMPRIMIR</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                    <div v-if="successPrint.status" class="alert alert-dismissible fade show" :class="successPrint.tipo"
                        role="alert">
                        <strong>{{ successPrint.titulo }}</strong> <i class="bi bi-printer"></i>
                        {{ successPrint.mensagem }}
                        <button type="button" class="btn-close" @click="successPrint.status = false"></button>
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
        this.modalReprint = new bootstrap.Modal(document.getElementById("modalReprint"), {});
        this.dadosConsulta(this.page);
        setInterval(() => this.dadosConsulta(this.page), 1000 * 20);
    },
    methods: {
        buscar() {
            this.dadosConsulta(this.page)
        },
        reprint(cod) {
            this.modalReprint.show();
            this.form.apontamento = cod
        },
        async reprintAxio() {
            console.log(this.form.apontamento);
            console.log(this.form.impressora);
            console.log(this.form.login);
            console.log(this.form.senha);
            this.loading = true
            this.erroPrint = false
            this.successPrint.status = false
            this.successPrint.titulo = ''
            this.successPrint.mensagem = ''
            this.successPrint.tipo = ''
            await axios.get(route('mecalux.apontamentoPdf'),
                {
                    params: {
                        cod: this.form.apontamento,
                        printer: this.form.impressora,
                        login: this.form.login,
                        senha: this.form.senha
                    }
                })
                .then(response => {
                    if (response.data.status) {
                        this.successPrint.status = true
                        this.successPrint.titulo = 'Sucesso!'
                        this.successPrint.mensagem = 'Etiqueta Impressa com sucesso'
                        this.successPrint.tipo = 'alert-success'
                        if (this.form.impressora == 'PDF') {
                            this.viewDoc(this.form.apontamento)
                        }
                        this.loading = false
                        this.dadosConsulta(this.page)
                        this.modalReprint.hide();
                        this.form.apontamento = ''
                        this.form.impressora = ''
                        this.form.login = ''
                        this.form.senha = ''
                    } else {
                        this.successPrint.status = true
                        this.successPrint.titulo = response.data.title
                        this.successPrint.mensagem = response.data.message
                        this.successPrint.tipo = response.data.type
                    }
                }).catch(function (error) {
                    this.erroPrint = true
                    this.loading = false
                })
        },
        async goPrint(cod, printer) {
            this.loading = true
            this.erroPrint = false
            this.successPrint.status = false
            this.successPrint.titulo = ''
            this.successPrint.mensagem = ''
            this.successPrint.tipo = ''
            await axios.get(route('mecalux.apontamentoPdf'),
                {
                    params: {
                        cod: cod,
                        printer: printer
                    }
                })
                .then(response => {
                    if (response.data.status) {
                        this.successPrint.status = true
                        this.successPrint.titulo = 'Sucesso!'
                        this.successPrint.mensagem = 'Etiqueta Impressa com sucesso'
                        this.successPrint.tipo = 'alert-success'
                        this.loading = false
                        this.dadosConsulta(this.page)
                        if (printer == 'PDF')
                            this.viewDoc(cod)
                    } else {
                        this.successPrint.status = true
                        this.successPrint.titulo = response.data.title
                        this.successPrint.mensagem = response.data.message
                        this.successPrint.tipo = response.data.type
                    }
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
                ':' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':'
                + (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds()) + '';

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
            console.log(this.asset + "PDF/" + arquivo + '.pdf')
            this.attSrc = this.asset + "PDF/" + arquivo + '.pdf';
            var modalAtt = new bootstrap.Modal(document.getElementById("modalAnexos"), {});
            modalAtt.show()
        },
    },
    props: ['recurso', 'recursos', 'asset'],
    data() {
        return {
            successPrint: {
                status: false,
                titulo: '',
                mensagem: '',
                tipo: ''
            },
            impressoras: [
                '38', '39', '40', '41', 'PDF'
            ],
            modalReprint: '',
            CancelToken: '',
            source: [],
            attSrc: '',
            erroPrint: false,
            //successPrint: false,
            page: 1,
            loading: false,
            consultas: [],
            buscador: '',
            selectRecurso: this.recurso,
            form: {
                apontamento: '',
                impressora: '',
                login: '',
                senha: ''
            }
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