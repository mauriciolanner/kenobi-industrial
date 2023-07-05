<template>
    <app-layout :title="user_profile.name">
        <template #header>
            <h2 class="h4 font-weight-bold">
                {{ user_profile.name }} - Detalhes do pedido {{ pedido.C5_NUM }}
            </h2>
        </template>
        <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
            <div class="row">
                <div class="col-md-4">
                    <strong>Numero do pedido:</strong> {{ pedido.C5_NUM }}
                </div>
                <div class="col-md-4">
                    <strong>Data:</strong> {{ pedido.C5_EMISSAO }}
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong> {{ pedido.C5_FSSTBI }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Nota</th>
                                <th scope="col">Qtd</th>
                                <th scope="col">Valor</th>
                                <th scope="col" class="text-end" v-if="$page.props.user.role_id == 3">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in pedido.itens" :key="item.C6_ITEM">
                                <th scope="row">{{ item.C6_ITEM }}</th>
                                <td>{{ item.C6_DESCRI }}</td>
                                <td>{{ item.C6_NOTA }}</td>
                                <td>{{ item.C6_QTDEMP }}</td>
                                <td>{{ parseFloat(item.C6_VALOR).toLocaleString('pt-br', {
                                        style: 'currency', currency:
                                            'BRL'
                                    })
                                }}
                                </td>
                                <td class="text-end" v-if="$page.props.user.role_id == 3">
                                    <Link :href="'#'">
                                    <button class="btn-icon btn-info">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    </Link>
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
import JetButton from "@/Jetstream/Button.vue";
import JetModalDialog from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Link } from '@inertiajs/inertia-vue3';
import Pedidos from '@/Components/Pedidos.vue';

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        JetModalDialog,
        JetInput,
        JetLabel,
        Link,
        Pedidos
    },
    methods: {

    },
    props: ["user_profile", "pedido"],
    data() {
        return {

        };
    },
});
</script>