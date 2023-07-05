<template>
    <app-layout title="Usuários">
        <template #header>
            <h2 class="h4 font-weight-bold">Usuários do sistema</h2>
        </template>
        <div>
            <nova v-if="criarNovo" v-on:voltaParaLista="criaNovo" :roles="roles" />
            <lista v-else v-on:voltaParaLista="criaNovo" v-bind:allUsers="allUsers" />

        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import Nova from "./New";
import Lista from "./List";

export default defineComponent({
    components: {
        AppLayout,
        Nova,
        JetButton,
        Lista,
    },
    methods: {
        criaNovo() {
            this.criarNovo == false
                ? (this.criarNovo = true)
                : (this.criarNovo = false);
        },
    },
    props: [
        'allUsers',
        'roles',
        'customers',
        'verCustomer',
        'comerciais'
    ],
    data() {
        return {
            criarNovo: false,
        };
    },
});
</script>