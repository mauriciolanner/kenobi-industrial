<template>
    <IndexForm #formBody :formFluxo="formInfo.fluxo" :aprovacao="formOpem.aprovacao" :historico="formOpem.historico">
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
                        <h2>{{  $page.props.flash.title  }}</h2>
                        <h4>
                            <Link :href="'/form/ControleVisita/detalhes/' + $page.props.flash.form_number">Processo
                            número {{  $page.props.flash.form_number  }}
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
                        <h4 v-if="formOpem.status === 1" class="text-danger">Formulário cancelado</h4>
                        <h4 v-if="formOpem.status == 2">Formulário não enviado a aprovação</h4>
                        <h4 v-if="formOpem.status == 3">Formulário em aprovação</h4>
                        <h4 v-if="formOpem.status == 4">Formulário finalizado</h4>
                    </div>
                </div>

                <!-- Formulário inicio -->
                <div class="row mb-3">
                    <div class="col-md-4 mt-4">
                        <jet-label for="vendedor" value="Vendedor" />
                        <jet-input id="vendedor" type="text" v-model="form.vendedor" :readonly="true" />

                        <small class="form-text text-danger">
                            {{  form.errors.vendedor  }}
                        </small>
                    </div>
                </div>
                <!--Formulário fim-->

                <!--Controladores-->
                <div class="divider"></div>
                <div class="row mb-3" v-if="formOpem.aprovacao.length == 0">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-info" @click="store(2)">Salvar</button>
                    </div>
                </div>
                <div class="row mb-3" v-if="formOpem.aprovacao.length == 0">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-success" @click="store(3)">Enviar para aprovação</button>
                    </div>
                </div>
                <div class="row mb-3" v-if="atualTask != null">
                    <div class="col-md-12 text-end"
                        v-if="atualTask.user_id == $page.props.user.id && formOpem.status == 3">
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Opções
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" @click="toAprove">Aprovar</button></li>
                                <li><button class="dropdown-item" @click="toRevise">Revisar</button></li>
                                <li><button class="dropdown-item" @click="toCancel">Cancelar</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalAnexos" tabindex="-1" aria-labelledby="modalAnexosLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" v-html="visaoArquivo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
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
import TextInput from "@/Jetstream/InputText.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import IndexForm from './Index.vue';
import { Link } from '@inertiajs/inertia-vue3'

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        TextInput,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetLabel,
        JetValidationErrors,
        IndexForm,
        Link
    },
    props: ['formInfo', 'formNumber', 'formOpem', 'atualTask', 'editor', 'readonlyVerific', 'verCustomer'],
    created: () => {

    },
    methods: {
        toAprove() {
            this.$inertia.post(route('form.ControleVisita.toAprove'), {
                form: this.formOpem.form_number
            })
        },
        toCancel() {
            this.$inertia.post(route('form.ControleVisita.toCancel'), {
                form: this.formOpem.form_number
            })
        },
        toRevise() {
            this.$inertia.post(route('form.ControleVisita.toRevise'), {
                form: this.formOpem.form_number
            })
        },
        criarPaiFilho(response) {

        },
        deletaPaiFilho(posicao) {
            this.form.despesas.splice(posicao, 1)
        },
        store(status) {
            if (this.formOpem.id == null) {
                this.form.status = status;
                this.form.post(route('form.ControleVisita.criar'), {
                    //erroBag: "UserRequest",
                    preserveScroll: true,
                    onSuccess: (result) => {
                    },
                });
            } else {
                this.form.status = status;
                this.form.post(route('form.ControleVisita.update'), {
                    //erroBag: "UserRequest",
                    preserveScroll: true,
                    onSuccess: (result) => {
                    },
                });
            }

        },
        limpaItens() {
            this.objetoDespesa.tipo = "";
            this.objetoDespesa.descricao = "";
            this.objetoDespesa.valor = "";
            this.objetoDespesa.anexo = "";
            this.objetoDespesa.mime = "";
            this.objetoDespesa.file_name = "";
            this.objetoDespesa.extencion = "";
        },
    },
    data() {
        return {
            modalAnexo: false,
            isLoading: false,
            visaoArquivo: "",
            form: this.$inertia.form({
                id: this.formOpem.id,
            }),
        };
    },
});
</script>