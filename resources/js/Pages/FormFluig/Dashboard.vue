<template>
    <app-layout title="Dashboard">
        <template #header>
            <!-- <h2 class="h4 font-weight-bold">Dashboard</h2> -->
        </template>

        <div class="row">
            <div class="col-md-12 mb-3">
                <FormList />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import FormList from "./Componentes/List.vue"
import { Link } from '@inertiajs/inertia-vue3';
import JetInputSearch from "@/Jetstream/Input.vue";

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        FormList,
        Link,
        JetInputSearch
    },
    props: [],
    methods: {
    },
    data() {
        return {
        };
    },
});
</script>