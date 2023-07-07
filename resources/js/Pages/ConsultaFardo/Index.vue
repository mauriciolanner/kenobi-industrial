<template>
    <app-layout title="Consulta Fardo">
        <template #header>
            <h2 class="h4 font-weight-bold">Consulta Fardo</h2>
        </template>

        <div
            class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive"
        >
            <div class="row mb-3">
                <div class="col-md-4">
                    <jet-input-search
                        id="buscar"
                        :class="'mt-3'"
                        type="text"
                        v-model="buscador"
                        @keyup="buscar"
                        placeholder="Buscar por OP..."
                    />
                </div>
                <!-- <div class="col-md-3">
                    <jet-input-search id="buscar" :class="'mt-3'" type="text" v-model="matricula" placeholder="Matricula" />
                </div> -->
                <!-- <div class="col-md-3" style="padding-top: 21px;">
                    <label>Turno:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" v-model="turno" name="turno" type="radio" id="turno1" value="1" />
                        <label class="form-check-label" for="inlineCheckbox1">1</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" v-model="turno" name="turno" type="radio" id="turno2" value="2" />
                        <label class="form-check-label" for="inlineCheckbox2">2</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" v-model="turno" name="turno" type="radio" id="turno3" value="3" />
                        <label class="form-check-label" for="inlineCheckbox3">3</label>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-padrao">
                        <thead>
                            <tr>
                                <th scope="col">OP</th>
                                <th scope="col">Emissão</th>
                                <th scope="col">ID</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Qtd</th>
                                <th scope="col">Etiqueta</th>
                                <!-- <th scope="col">Dupla</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="consulta in consultas">
                                <th>{{ consulta.OrdemProducao }}</th>
                                <td>{{ formataData(consulta.Emissao) }}</td>
                                <td>{{ consulta.ID }}</td>
                                <td>{{ consulta.Produto }}</td>
                                <td>
                                    {{ parseInt(consulta.QtdPorEmbalagem) }}
                                </td>
                                <td>
                                    <button
                                        class="btn btn-info"
                                        @click="
                                            fazerPDF(
                                                consulta.ID,
                                                consulta.OrdemProducao
                                            )
                                        "
                                    >
                                        <i class="bi bi-printer"></i>
                                    </button>
                                </td>
                                <!-- <td>
                                    <a v-if="matricula != '' && turno != ''"
                                        :href="route('etiquetadupla.pdf', [consulta.ID, consulta.OrdemProducao, turno])"
                                        target="_blank" class="btn btn-info"><i class="bi bi-printer"></i></a>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Impressão -->
        <div
            class="modal fade"
            id="modalImpressao"
            tabindex="-1"
            aria-labelledby="modalImpressaoLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-end">
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h1
                                    class="text-success"
                                    style="font-size: 64px"
                                >
                                    <i class="bi bi-printer"></i>
                                </h1>
                                <p>Deseja continuar com a impressão</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            @click="modalAtivo1 = false"
                        >
                            Fechar
                        </button>
                        <button
                            class="btn btn-primary"
                            @click="
                                imprimir(consultasSalvas.id, consultasSalvas.op)
                            "
                        >
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Impressão dois -->
        <div
            class="modal fade"
            id="modalImpressaodois"
            tabindex="-1"
            aria-labelledby="modalImpressaodoisLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-end">
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h1
                                    class="text-success"
                                    style="font-size: 64px"
                                >
                                    <i class="bi bi-printer"></i>
                                </h1>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="motivo"
                                    >Insira o motivo para a reimpressão</label
                                >
                                <input
                                    class="form-control"
                                    :class="{
                                        'is-invalid': validamotivo_impressao,
                                    }"
                                    type="text"
                                    name="motivo_impressao"
                                    id="motivo_impressao"
                                    v-model="motivo_impressao"
                                    @blur="checarMotivo"
                                />
                                <div
                                    v-if="validamotivo_impressao"
                                    class="invalid-feedback"
                                >
                                    Preencha o motivo
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label" for="motivo"
                                    >Insira o número da etiqueta que deve ser
                                    impressa</label
                                >
                                <input
                                    class="form-control"
                                    :class="{
                                        'is-invalid': validaetiquetaReimprimir,
                                    }"
                                    type="text"
                                    name="etiquetaReimprimir"
                                    id="etiquetaReimprimir"
                                    v-model="etiquetaReimprimir"
                                    @blur="checarQuantidade"
                                />
                                <div
                                    v-if="validaetiquetaReimprimir"
                                    class="invalid-feedback"
                                >
                                    Preencha o numero da etiqueta
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            @click="modalAtivo2 = false"
                        >
                            Fechar
                        </button>
                        <div>
                            <button
                                class="btn btn-primary"
                                @click="
                                    reimprimir(
                                        consultasSalvas.id,
                                        consultasSalvas.op
                                    )
                                "
                            >
                                Confirmar impressão
                            </button>
                        </div>
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
import Welcome from "@/Jetstream/Welcome.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        AppLayout,
        JetInputSearch,
        Welcome,
        Link,
    },
    methods: {
        abreModal(id) {
            this.modal = new bootstrap.Modal(document.getElementById(id), {});
            this.modal.show();
        },
        fechaModal() {
            this.modal.hide();
        },
        buscar() {
            this.$inertia.get(
                route("consulta.fardo"),
                { busca: this.buscador },
                { preserveState: true }
            );
        },
        imprimir() {
            this.imprecaoDados.imprecaoId = this.consultasSalvas.id;
            this.imprecaoDados.imprecaoOp = this.consultasSalvas.op;
            let _this = this;

            axios
                .post(route("fardo.pdf"), this.imprecaoDados, {
                    responseType: "arraybuffer",
                })
                .then((response) => {
                    console.log(response);
                    let blob = new Blob([response.data], {
                        type: "application/pdf",
                    });
                    let link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download = "etiquetas.pdf";
                    link.click();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        reimprimir() {
            this.reimprimirDados.reimprecaoEtiqueta = this.etiquetaReimprimir;
            this.reimprimirDados.motivoImpressao = this.motivo_impressao;
            this.reimprimirDados.reimprecaoId = this.consultasSalvas.id;
            this.reimprimirDados.reimprecaoOp = this.consultasSalvas.op;

            this.validaetiquetaReimprimir =
                this.etiquetaReimprimir != "" ? false : true;
            this.validamotivo_impressao =
                this.motivo_impressao != "" ? false : true;

            if (
                this.validamotivo_impressao == false &&
                this.validaetiquetaReimprimir == false
            ) {
                axios
                    .post(route("fardo.reimprimir.pdf"), this.reimprimirDados, {
                        responseType: "arraybuffer",
                    })
                    .then((response) => {
                        console.log(response);
                        let blob = new Blob([response.data], {
                            type: "application/pdf",
                        });
                        let link = document.createElement("a");
                        link.href = URL.createObjectURL(blob);
                        link.download = "etiquetaReimprimir.pdf";
                        link.click();
                    })
                    .catch((error) => {});
            }
        },
        fazerPDF(id, op) {
            console.log("teste");
            this.consultasSalvas.id = id;
            this.consultasSalvas.op = op;

            axios
                .get(route("fardo.impressoes", op))
                .then((response) => {
                    if (response.data === "") {
                        this.quantImpressoes = 0;
                    } else {
                        this.quantImpressoes =
                            response.data[
                                response.data.length - 1
                            ].num_impressoes;
                    }

                    if (this.quantImpressoes === 0) {
                        this.abreModal("modalImpressao");
                    } else {
                        this.abreModal("modalImpressaodois");
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
        },
        formataData(dateIni) {
            var date = new Date(dateIni);
            var mes = "";
            date.getMonth() == 0 ? (mes = "Jan") : "";
            date.getMonth() == 1 ? (mes = "Fev") : "";
            date.getMonth() == 2 ? (mes = "Mar") : "";
            date.getMonth() == 3 ? (mes = "Abr") : "";
            date.getMonth() == 4 ? (mes = "Mai") : "";
            date.getMonth() == 5 ? (mes = "Jun") : "";
            date.getMonth() == 6 ? (mes = "Jul") : "";
            date.getMonth() == 7 ? (mes = "Ago") : "";
            date.getMonth() == 8 ? (mes = "Set") : "";
            date.getMonth() == 9 ? (mes = "Out") : "";
            date.getMonth() == 10 ? (mes = "Nov") : "";
            date.getMonth() == 11 ? (mes = "Dez") : "";

            return date.getDate() + 1 + " " + mes + ", " + date.getFullYear();
        },
    },
    props: ["consultas", "asset", "user"],
    data() {
        return {
            modal: "",
            modalAtivo1: false,
            modalAtivo2: false,
            etiquetaReimprimir: "",
            buscador: "",
            turno: "",
            matricula: "",
            validaetiquetaReimprimir: false,
            validamotivo_impressao: false,
            quantImpressoes: "",
            motivo_impressao: "",
            consultasSalvas: this.$inertia.form({
                id: "",
                op: "",
            }),
            reimprimirDados: {
                reimprecaoId: "",
                reimprecaoOp: "",
                motivoImpressao: "",
                reimprecaoEtiqueta: "",
            },
            imprecaoDados: {
                imprecaoId: "",
                imprecaoOp: "",
            },
        };
    },
});
</script>
