<template>
    <app-layout title="Etiquetas Impressas">
        <template #header>
            <h2 class="h4 font-weight-bold">Etiquetas Impressas</h2>
        </template>
        <div
            class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive"
        >
            <div class="row mb-3">
                <div class="col-sm-9">
                    <label for="Pesquisa" class="form-label inputImpressas"
                        >Pesquisa</label
                    >
                    <jet-input-search
                        id="buscar"
                        type="text"
                        v-model="buscador"
                        placeholder="Buscar..."
                        class="inputImpressas"
                    />
                </div>
                <div class="col-sm-2">
                    <button
                        type="button"
                        v-on:click="buscar"
                        class="btn btn-success mt-30"
                    >
                        Buscar
                    </button>
                    <button
                        type="button"
                        style="margin-left: 20px"
                        v-on:click="limpar"
                        class="btn btn-danger mt-30"
                    >
                        Limpar
                    </button>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-12 d-flex justify-content-center">
                    <table class="table table-striped table-padrao">
                        <thead>
                            <tr>
                                <th
                                    class="colunasAbaImpressas colIni"
                                    scope="col"
                                >
                                    NumSeq
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Ordem
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    T
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Data Início
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Hora início
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Hora Final
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Emissão
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Lote
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    ID
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Produto
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Produzida
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Saldo
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Operador
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Via
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center"
                                    scope="col"
                                >
                                    Etiqueta
                                </th>
                                <th
                                    class="colunasAbaImpressas text-center colFim"
                                    scope="col"
                                >
                                    Info
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="consulta in consultas">
                                <th class="text-center">
                                    {{ consulta.NumSeq }}
                                </th>
                                <td class="text-center">
                                    {{ consulta.OrdemProducao }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.Turno }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.DataIni }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.HoraIni }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.HoraFin }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.Emissao }}
                                </td>
                                <td class="text-center">{{ consulta.Lote }}</td>
                                <td class="text-center">
                                    {{ consulta.Produto_ID }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.Produto }}
                                </td>
                                <td class="text-center">
                                    {{ parseInt(consulta.QtdProduzida) }}
                                </td>
                                <td class="text-center">
                                    {{ parseInt(consulta.Falta) }}
                                </td>
                                <td class="text-center">
                                    {{ consulta.Operador }}
                                </td>
                                <td class="text-center">
                                    {{ parseInt(consulta.Via) }}
                                </td>
                                <td class="text-center">
                                    <a
                                        :href="
                                            asset +
                                            'impressas/pdf/' +
                                            consulta.Recno +
                                            '/' +
                                            consulta.OrdemProducao +
                                            '/' +
                                            consulta.NumSeq +
                                            '/ApontamentoImpresso'
                                        "
                                        target="blank"
                                        class="btn btn-info"
                                        ><i class="bi bi-printer"></i
                                    ></a>
                                </td>
                                <td>
                                    <button
                                        v-if="btnbuscar"
                                        type="button"
                                        data-bs-toggle="modal"
                                        :data-bs-target="
                                            '#exampleModal' + consulta.NumSeq
                                        "
                                        class="btn btn-info"
                                    >
                                        <i class="bi bi-info-square"></i>
                                    </button>
                                    <div
                                        class="modal fade"
                                        :id="'exampleModal' + consulta.NumSeq"
                                        tabindex="-1"
                                        aria-labelledby="exampleModalLabel"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5
                                                        class="modal-title"
                                                        id="exampleModalLabel"
                                                    >
                                                        NumSeq:
                                                        {{ consulta.NumSeq }}
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table
                                                                class="table table-striped table-padrao"
                                                            >
                                                                <thead>
                                                                    <tr>
                                                                        <th
                                                                            scope="col"
                                                                        >
                                                                            Saldo
                                                                            PR
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr
                                                                        v-for="saldoPR in saldopr"
                                                                    >
                                                                        <th>
                                                                            {{
                                                                                parseInt(
                                                                                    saldoPR.SaldoPR
                                                                                )
                                                                            }}
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table
                                                                class="table table-striped table-padrao"
                                                            >
                                                                <thead>
                                                                    <tr>
                                                                        <th
                                                                            scope="col"
                                                                        >
                                                                            Saldo
                                                                            LO
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr
                                                                        v-for="saldoLO in saldolo"
                                                                    >
                                                                        <th>
                                                                            {{
                                                                                parseInt(
                                                                                    saldoLO.SaldoLO
                                                                                )
                                                                            }}
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="row">
                                                            <div
                                                                class="col-md-12"
                                                            >
                                                                <table
                                                                    class="table table-striped table-padrao"
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th
                                                                                scope="col"
                                                                            >
                                                                                Saldo
                                                                                QL
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr
                                                                            v-for="saldo in saldoQL"
                                                                        >
                                                                            <th>
                                                                                {{
                                                                                    parseInt(
                                                                                        saldo.SaldoQL
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div
                                                                class="col-md-12"
                                                            >
                                                                <table
                                                                    class="table table-striped table-padrao"
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th
                                                                                scope="col"
                                                                            >
                                                                                OP
                                                                            </th>
                                                                            <th
                                                                                scope="col"
                                                                            >
                                                                                Qtd
                                                                                da
                                                                                OP
                                                                            </th>
                                                                            <th
                                                                                scope="col"
                                                                            >
                                                                                Apontado
                                                                            </th>
                                                                            <th
                                                                                scope="col"
                                                                            >
                                                                                Falta
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr
                                                                            v-for="consulta in consulta2"
                                                                        >
                                                                            <th>
                                                                                {{
                                                                                    consulta.OrdemProducao
                                                                                }}
                                                                            </th>
                                                                            <td>
                                                                                {{
                                                                                    parseInt(
                                                                                        consulta.QtdOrdem
                                                                                    )
                                                                                }}
                                                                            </td>
                                                                            <td>
                                                                                {{
                                                                                    parseInt(
                                                                                        consulta.Apontado
                                                                                    )
                                                                                }}
                                                                            </td>
                                                                            <td>
                                                                                {{
                                                                                    parseInt(
                                                                                        consulta.Falta
                                                                                    )
                                                                                }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div
                                                                class="modal-footer"
                                                            >
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-secondary"
                                                                    data-bs-dismiss="modal"
                                                                >
                                                                    Fechar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
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
    methods: {
        buscar() {
            console.log(this.buscador, this.dataInicio, this.datafinal);
            this.$inertia.get(
                route("consulta.impressas"),
                {
                    busca: this.buscador,
                    dataini: this.dataInicio,
                    datafinal: this.dataFim,
                },
                { preserveState: true },
                (this.btnbuscar = true)
            );
        },
        limpar() {
            this.$inertia.get(route("consulta.impressas"));
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
    },
    props: ["consultas", "saldoQL", "saldopr", "saldolo", "consulta2", "asset"],
    data() {
        return {
            buscador: "",
            btnbuscar: false,
        };
    },
});
</script>
