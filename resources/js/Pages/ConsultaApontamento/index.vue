<template>
    <app-layout title="Consulta Apontamento">
        <template #header>
            <h2 class="h4 font-weight-bold">Consulta Apontamento</h2>
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
                                <th scope="col">OP</th>
                                <th scope="col">T</th>
                                <th scope="col">Emissão</th>
                                <th scope="col">ID</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Lote</th>
                                <th scope="col">Produzida</th>
                                <th scope="col">Saldo</th>
                                <th scope="col">NumSeq</th>
                                <th scope="col">Etiqueta</th>
                            </tr>
                        </thead>
                        <tbody v-if="!loading">
                            <tr v-for="consulta in consultas">
                                <th>{{ consulta.OrdemProducao }}</th>
                                <td>{{ consulta.Turno }}</td>
                                <td>{{ consulta.Emissao }}</td>
                                <td>{{ consulta.Produto_ID }}</td>
                                <td>{{ consulta.Produto }}</td>
                                <td>{{ consulta.Lote }}</td>
                                <td>{{ parseInt(consulta.QtdProduzida) }}</td>
                                <td>{{ parseInt(consulta.Saldo) }}</td>
                                <td>{{ consulta.NumSeq }}</td>
                                <td>
                                    <button v-if="consulta.Via != '0'" @click="
                                        imprimir(
                                            consulta.Recno,
                                            consulta.OrdemProducao
                                        )
                                        " class="btn btn-info">
                                        <i class="bi bi-printer"> </i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <th scope="col text-center" colspan="10">
                                    <div class="text-center">
                                        <div class="spinner-border text-info" style="width: 5rem; height: 5rem"
                                            role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tbody>
                    </table>
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
        this.dadosConsulta();
        //setInterval(() => this.dadosConsulta(), 1000 * 5);
    },
    methods: {
        mostrarConsole(recno, op) {
            console.log(recno, op);
        },
        buscar() {
            this.dadosConsulta();
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

            return date.getDate() + " " + mes + ", " + date.getFullYear();
        },
        imprimir(recno, op) {
            op = op.trim();
            recno = recno.trim();
            axios
                .get(
                    route("apontamento.criar.pdf", {
                        recnoEnviado: recno,
                        opEnviada: op,
                    }),
                    {
                        responseType: "arraybuffer",
                    }
                )
                .then((response) => {
                    console.log(response);
                    let blob = new Blob([response.data], {
                        type: "application/pdf",
                    });
                    let link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download = "etiqueta-apontamento.pdf";
                    link.click();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        async dadosConsulta() {
            this.loading = true;
            await axios
                .get(route("apontamento.APIconsulta"), {
                    params: {
                        busca: this.buscador,
                    },
                })
                .then((response) => {
                    this.consultas = response.data;
                    this.loading = false;
                });
        },
    },
    props: [],
    data() {
        return {
            loading: false,
            consultas: [],
            buscador: "",
        };
    },
});
</script>
