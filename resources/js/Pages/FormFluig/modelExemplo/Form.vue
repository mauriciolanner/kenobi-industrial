<template>
    <IndexForm #formBody :formFluxo="formInfo.fluxo" :form="formInfo" :aprovacao="formOpem.aprovacao"
        :historico="formOpem.historico">
        <div class="col-md-12" v-if="isLoading">
            <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                <div class="row">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12" v-if="$page.props.flash.form">
            <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1 class="ico-ok"><i class="bi bi-check-circle"></i></h1>
                    </div>
                    <div class="col-md-12 text-center">
                        <h2>{{ $page.props.flash.title }}</h2>
                        <h4>
                            <Link :href="route('form.' + formInfo.slung + '.ver', $page.props.flash.form_number)">
                            Processo número {{ $page.props.flash.form_number }}
                            </Link>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" v-else>
            <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                <div class="row" v-if="formOpem.id != ''">
                    <div class="col">
                        <h4 v-if="formOpem.status == 1" class="text-danger">Formulário cancelado</h4>
                        <h4 v-if="formOpem.status == 2">Formulário não enviado a aprovação</h4>
                        <h4 v-if="formOpem.status == 3">Formulário em aprovação</h4>
                        <h4 v-if="formOpem.status == 4">Formulário finalizado</h4>
                    </div>
                </div>

                <!-- Formulário inicio -->
                <div class="row">
                    <div class="col-md-6 mt-3">
                        <jet-label for="usuarioAbertura" value="Aberto por" />
                        <jet-input id="usuarioAbertura" type="text" v-model="form.usuarioAbertura" :readonly="true" />

                        <small class="form-text text-danger">
                            {{ form.errors.usuarioAbertura }}
                        </small>
                    </div>
                    <div class="col-md-6 mt-3">
                        <jet-label for="dataAbertura" value="Data" />
                        <jet-input id="dataAbertura" type="date" v-model="form.dataAbertura" :readonly="true" />

                        <small class="form-text text-danger">
                            {{ form.errors.dataAberturadataAbertura }}
                        </small>
                    </div>

                </div>
                <div class="divider"></div>
                <div class="row">
                    <div class="col-md-4 mt-3">
                        <jet-label for="seila" value="Sei la" />
                        <jet-input id="seila" type="text" v-model="form.seila" :readonly="readonlyVerific" />

                        <small class="form-text text-danger">
                            {{ form.errors.seila }}
                        </small>
                    </div>

                    <div class="col-md-4 mt-3">
                        <jet-label for="seilaDenovo" value="Sei la de novo:" />
                        <jet-input id="seilaDenovo" type="text" v-model="form.seilaDenovo" :readonly="readonlyVerific" />

                        <small class="form-text text-danger">
                            {{ form.errors.seilaDenovo }}
                        </small>
                    </div>

                    <div class="col-md-4 mt-3">
                        <jet-label for="solicitante" value="Solicitante:" />
                        <VueMultiselect v-model="solicitanteSelecionado" label="CHAPA" :options="funcionarios"
                            @select="selectSolicitante" :placeholder="'Escolha..'" :selectedLabel="''"
                            :custom-label="labelFuncionario" :disabled="readonlyVerific" :deselectLabel="'remover'"
                            :selectLabel="''">
                            <span slot="noResult">Carregando...</span>
                        </VueMultiselect>

                        <small class="form-text text-danger">
                            {{ form.errors.solicitante }}
                        </small>
                    </div>
                </div>

                <!--Formulário fim-->
                <div class="loading" v-if="carregaAnexo">Loading</div>
                <!--Controladores-->
                <div class="divider"></div>
                <!-- <div class="row mb-3" v-if="formOpem.aprovacao.length == 0">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-info" @click="store(2)">Salvar</button>
                    </div>
                </div> -->
                <div class="row mb-3" v-if="formOpem.form_number">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-info" @click="toPdf" type="button">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="row mb-3" v-if="formOpem.aprovacao.length == 0">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-success" @click="store(3)">Enviar para aprovação</button>
                    </div>
                </div>

                <div class="row mb-3" v-if="readonlyVerific && approver && (formOpem.status != 4 && formOpem.status != 1)">
                    <div class="col-md-12 text-end">
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Opções
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" @click="toAprove">Aprovar</button></li>
                                <li v-if="false"><button class="dropdown-item" @click="abreModalOcorrencia">Revisar</button>
                                </li>
                                <li><button class="dropdown-item" @click="abreModalCancel">Cancelar</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ModalDetalhes -->
                <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" v-if="detalhesModal">
                                <div class="row">
                                    <div class="col"
                                        :class="{ 'bg-success': detalhesModal.aprovacaoDiretor == 1, 'bg-secondary': detalhesModal.aprovacaoDiretor == 0 }"
                                        style="padding: 22px;">
                                        <h4>Aprovação Diretoria
                                            <i class="bi bi-check-circle" v-if="detalhesModal.aprovacaoDiretor == 1"></i>
                                            <i class="bi bi-x-circle" v-else></i>
                                        </h4>
                                    </div>
                                    <div class="col" style="padding: 22px;"
                                        :class="{ 'bg-success': detalhesModal.aprovacaoPresidencia == 1, 'bg-secondary': detalhesModal.aprovacaoPresidencia == 0 }">
                                        <h4>Aprovação Presidencia
                                            <i class="bi bi-check-circle"
                                                v-if="detalhesModal.aprovacaoPresidencia == 1"></i>
                                            <i class="bi bi-x-circle" v-else></i>
                                        </h4>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-2">
                                        <h5>Fornecedor:</h5>
                                        {{ detalhesModal.E2_NOMFOR }}
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Emissão:</h5>
                                        {{ formataData(detalhesModal.E2_EMISSAO) }}
                                    </div>
                                    <div class="col-md-2" v-if="detalhesModal.E2_PARCELA">
                                        <h5>Parcela:</h5>
                                        {{ detalhesModal.E2_PARCELA }}
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Tipo:</h5>
                                        <div class="badge w-100" :class="{
                                            'bg-success': ['DAF', 'DAE', 'DAR', 'DES'].indexOf(detalhesModal.E2_TIPO.trim()) >= 0,
                                            'bg-warning': detalhesModal.E2_TIPO.trim() == 'FOL',
                                            'bg-info': ['FT', 'NF'].indexOf(detalhesModal.E2_TIPO.trim()) >= 0,
                                            'bg-secondary': true
                                        }">
                                            {{ detalhesModal.E2_TIPO.replace(" ", "") }}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Valor:</h5>
                                        <div class="badge-anexo bg-success w-100 mb-1 text-center"
                                            style="padding-right: 12px;">
                                            <b> {{
                                                parseFloat(detalhesModal.E2_VALOR).toLocaleString('pt-br',
                                                    {
                                                        style: 'currency', currency:
                                                            'BRL'
                                                    }) }}</b>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Fluig:</h5>
                                        {{ parseInt(detalhesModal.FLUIG) }}
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5>Anexos Fluig:</h5>
                                    </div>
                                </div>
                                <div class="row mt-4" v-if="carregaAnexo">
                                    <div class="col-md-12 text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="accordion" id="fluig">
                                        <div v-for="(anexo, index) in detalhesModal.anexos.fluig" class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" :data-bs-target="'#itemFluig' + index"
                                                    aria-expanded="false" :aria-controls="'itemFluig' + index">
                                                    {{ anexo.ARQUIVO }}
                                                </button>
                                            </h2>
                                            <div :id="'itemFluig' + index" class="accordion-collapse collapse"
                                                data-bs-parent="#fluig">
                                                <div class="accordion-body">
                                                    <iframe
                                                        :src="'http://192.168.254.65:8090/' + anexo.PASTA_RAIZ + '/' + anexo.SUBPASTA + '/' + anexo.ARQUIVO"
                                                        height="450px" width="100%"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div v-for="anexo in detalhesModal.anexos.fluig" class="col-md-6">
                                        <div class="badge-anexo bg-info w-100 mb-1">
                                            <div class="row">
                                                <div class="col-10" @click="downloadFluig(anexo)">{{ anexo.ARQUIVO }}</div>
                                                <div class="col-2 text-end">
                                                    <button @click="downloadFluig(anexo)" class="btn">
                                                        <i class="bi bi-arrow-90deg-down"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="col-md-12">
                                    <h5>Anexos Banco Conhecimento:</h5>
                                </div>
                                <div class="row">
                                    <div class="accordion" id="bancoConhecimento">
                                        <div v-for="(anexo, index) in detalhesModal.anexos.protheus" class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" :data-bs-target="'#itemDoc' + index"
                                                    aria-expanded="false" :aria-controls="'itemDoc' + index">
                                                    {{ anexo.ARQUIVO }}
                                                </button>
                                            </h2>
                                            <div :id="'itemDoc' + index" class="accordion-collapse collapse"
                                                data-bs-parent="#bancoConhecimento">
                                                <div class="accordion-body">
                                                    <iframe :src="'http://192.168.254.61:8081/' + anexo.ARQUIVO"
                                                        height="450px" width="100%"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!--offcanvas-->
                <div class="offcanvas offcanvas-start offcanvas-w-50" tabindex="-1" id="canvaCliente"
                    aria-labelledby="canvaClienteLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="canvaClienteLabel">CANVA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        ...
                    </div>
                </div>

                <!-- ModalAnexos -->
                <div class="modal fade" id="modalAnexos" tabindex="-1" aria-labelledby="modalAnexosLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        ...
                    </div>
                </div>

                <!--modal ocorrencias -->
                <div class="modal fade" id="modalOcorrencias" tabindex="-1" aria-labelledby="modalOcorrenciasLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Motivo da revisão</h5>
                                        <jet-input id="taskOccurrence" type="textarea" v-model="form.taskOccurrence" />
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-info" @click="toRevise"
                                    :disabled="form.taskOccurrence.length < 3">
                                    Enviar para revisão
                                </button>
                                <button type="button" class="btn btn-secondary"
                                    @click="fechaModalOcorrencia">Fechar</button>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" v-html="visaoArquivo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!--modal Cancela -->
                <div class="modal fade" id="modalDelet" tabindex="-1" aria-labelledby="modalDeletLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Motivo do cancelamento</h5>
                                        <jet-input id="taskOccurrence" type="textarea" v-model="form.taskOccurrence" />
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-info" @click="toCancel"
                                    :disabled="form.taskOccurrence.length < 3">
                                    Cancelar processo
                                </button>
                                <button type="button" class="btn btn-secondary" @click="fechaModalCancel">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </IndexForm>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import IndexForm from '../Index.vue';
import { Link } from '@inertiajs/inertia-vue3';
import VueMultiselect from 'vue-multiselect';

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetLabel,
        JetValidationErrors,
        IndexForm,
        VueMultiselect,
        Link
    },
    props: [
        'formInfo',
        'formNumber',
        'formOpem',
        'atualTask',
        'readonlyVerific',
        'approver',
        'asset'
    ],
    mounted() {
        this.modalOcorrencias = new bootstrap.Modal(document.getElementById("modalOcorrencias"), {});
        this.modalDetalhes = new bootstrap.Modal(document.getElementById("modalDetalhes"), {});
        this.modalDelet = new bootstrap.Modal(document.getElementById("modalDelet"), {});
        this.buscasFuncionarios();
    },
    methods: {
        labelFuncionario({ NOME, CHAPA }) {
            return CHAPA + ' - ' + NOME;
        },
        selectSolicitante(item) {
            this.form.chapaSolicitante = item.CHAPA;
            this.form.solicitante = item.NOME;
        },
        async buscasFuncionarios() {
            this.loding = true
            await axios.get(route('API.utei.funcionarios'), {
                params: {
                    buscar: ''
                }
            }).then(response => {
                this.funcionarios = response.data;
                this.loding = false
            }).catch(function (error) {
                console.error(error);
                this.loding = false
            })
            this.loding = false
        },
        formataData(dateIni) {
            if (dateIni.indexOf("-") == -1) {
                var fomataData = dateIni.substring(0, 4) + '-' + dateIni.substring(4, 6) + '-' + dateIni.substring(6, 8);
                dateIni = fomataData;
            }

            if (dateIni.indexOf(":") == -1) {
                dateIni = dateIni + " 00:00:0000";
            }

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
        abreModalOcorrencia() {
            this.modalOcorrencias.show()
        },
        fechaModalOcorrencia() {
            this.modalOcorrencias.hide()
        },
        abreModalCancel() {
            this.modalDelet.show()
        },
        fechaModalCancel() {
            this.modalDelet.hide()
        },
        forData(data) {
            return data.substr(6, 2) + "/" + data.substr(4, 2) + "/" + data.substr(0, 4);
        },
        async toPdf() {
            this.$inertia.get(route('form.' + this.formInfo.slung + '.pdf', this.formOpem.form_number), {
            })
        },
        toAprove() {
            this.form.post(route('form.' + this.formInfo.slung + '.toAprove'), {
            })
        },
        toCancel() {
            this.$inertia.post(route('form.' + this.formInfo.slung + '.toCancel'), {
                form_number: this.formOpem.form_number,
                taskOccurrence: this.form.taskOccurrence
            })
            this.fechaModalCancel();
        },
        toRevise() {
            this.$inertia.post(route('form.' + this.formInfo.slung + '.toRevise'), {
                form_number: this.formOpem.form_number,
                taskOccurrence: this.form.taskOccurrence
            })
            this.fechaModalOcorrencia();
        },
        store(status) {
            if (this.formOpem.id == null) {
                this.form.status = status;
                this.form.post(route('form.' + this.formInfo.slung + '.criar'), {
                    //erroBag: "UserRequest",
                    preserveScroll: true,
                    onSuccess: (result) => {
                    },
                });
            } else {
                this.form.status = status;
                this.form.post(route('form.' + this.formInfo.slung + '.update'), {
                    //erroBag: "UserRequest",
                    preserveScroll: true,
                    onSuccess: (result) => {
                    },
                });
            }

        },
    },
    data() {
        return {
            funcionarios: [],
            solicitanteSelecionado: {
                NOME: this.formOpem.solicitante,
                CHAPA: this.formOpem.chapaSolicitante
            },
            modalDetalhes: '',
            apenasReprovado: false,
            carregaAnexo: false,
            loading: false,
            loadingModal: false,
            isLoading: false,
            detalhesModal: null,
            bordero: {
                periodo: {
                    dataInicio: '',
                    dataFim: ''
                }
            },
            visaoArquivo: '',
            form: this.$inertia.form({
                id: this.formOpem.id,
                dataAbertura: this.formOpem.dataAbertura,
                usuarioAbertura: this.formOpem.usuarioAbertura,
                solicitante: this.formOpem.solicitante,
                chapaSolicitante: this.formOpem.chapaSolicitante,
                seila: this.formOpem.seila,
                seilaDenovo: this.formOpem.seilaDenovo,
                //camposPadrao
                taskOccurrence: '',
                childs: this.formOpem.childs,
                form_number: this.formOpem.form_number,
                create_user_id: this.formOpem.create_user_id,
                form_id: this.formOpem.form_id,
                status: this.formOpem.status,
                //anexos
                anexos: this.formOpem.anexos
            }),
        };
    },
});
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>