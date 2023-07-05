<template>
    <app-layout :title="cliente.A1_NOME">
        <template #header>
            <h2 class="h4 font-weight-bold">
                {{  cliente.A1_NOME  }}
                <i v-if="user_profile != null && user_profile.status == 1" class="bi bi-check-circle  text-success"></i>
                <i v-else class="bi bi-dash-circle  text-danger"></i>
            </h2>
        </template>
        <div class="row">
            <div class="col-md-6">
                <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
                    <div class="row">
                        <div class="col-12">
                            <h3><i class="bi bi-archive"></i> Pedidos ultimos 12 meses</h3>
                        </div>
                        <div class="col-md-12">
                            <apexchart width="100%" height="200px" type="line" :options="chartPedidos.chartOptions"
                                :series="chartPedidos.series">
                            </apexchart>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12">
                <Pedidos :pedidos="pedidos" :user_profile="user_profile" :dataInicioCarbon="dataInicioCarbon"
                    :dataFimCarbon="dataFimCarbon" />
            </div>
            <div class="col-md-6">
                <Titulos :titulos="titulos" :id_cliente="cliente.A1_COD" />
            </div>
            <div class="col-md-6">
                <ProdutosCompra :produtosCompra="produtosCompra" />
            </div>
            <div class="col-md-12">
                <Boletos :boletos="boletos" :id_cliente="cliente.A1_COD" />
            </div>
            <div class="col-md-12">
                <Rotulos :rolulos="rolulos" />
            </div>
        </div>
        <div class="row" v-if="user_profile != null">
            <div class="col-md-4">
                <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
                    <div class="row">
                        <div class="col-8">
                            <h3><i class="bi bi-archive"></i> Documentos:</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
                    <div class="row">
                        <div class="col-8">
                            <h3><i class="bi bi-clipboard2-x"></i> Não conformidade</h3>
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
import JetButton from "@/Jetstream/Button.vue";
import JetModalDialog from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Link } from '@inertiajs/inertia-vue3';
import Pedidos from '@/Components/Pedidos.vue';
import Boletos from '@/Components/Boletos.vue';
import Rotulos from '@/Components/Rotulos.vue';
import Titulos from '@/Components/Titulos.vue';
import ProdutosCompra from '@/Components/ProdutosCompra.vue';
import VueApexCharts from "vue3-apexcharts";

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        JetModalDialog,
        JetInput,
        JetLabel,
        Link,
        Pedidos,
        Boletos,
        Rotulos,
        Titulos,
        apexchart: VueApexCharts,
        ProdutosCompra
    },
    methods: {

    },
    props: [
        "user_profile",
        "pedidos",
        "cliente",
        "boletos",
        "dataInicioCarbon",
        "dataFimCarbon",
        "chartPedidos",
        "rolulos",
        "produtosCompra",
        "titulos"
    ],
    data() {
        return {

        };
    },
});
</script>