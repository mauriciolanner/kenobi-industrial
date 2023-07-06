<template>
    <app-layout title="Consulta Saldo">
        <template #header>
            <h2 class="h4 font-weight-bold">Consulta Saldo</h2>
        </template>


        <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
            <div class="row mb-3">
                <div class="col-md-9">
                    <jet-input-search id="buscar" :class="'mt-3'" type="text" v-model="buscador" @keyup="buscar"
                        placeholder="Buscar..." />
                </div>
                <div class="col-md-3">
                    <button class="btn btn-info"><a :href="asset + 'fardo/pdf/'" target="_blank"><i
                                class="bi bi-printer"></i></a></button>
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
            this.$inertia.get(
                "consultasaldo",
                { busca: this.buscador },
                { preserveState: true }
            );
        },
    },
    props: [
        'consultas', 'asset',
    ],
    data() {
        return {
            buscador: '',
        };
    },
});
</script>