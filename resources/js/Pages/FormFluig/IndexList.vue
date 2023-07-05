<template>
    <app-layout title="Requisições">
        <template #header>
            <h2 class="h4 font-weight-bold">Requisições</h2>
        </template>

        <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
            <div class="row mb-3">
                <div class="col-md-12">
                    <jet-input-search id="buscar" :class="'mt-3'" type="text" v-model="buscar" @keyup="search"
                        placeholder="Buscar..." />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-striped table-padrao">
                                <thead>
                                    <tr>
                                        <th scope="col">Cod</th>
                                        <th scope="col">Formulário</th>
                                        <th scope="col">Solicitante</th>
                                        <th scope="col">Abertura</th>
                                        <th scope="col">Ultima Movimentação</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="forms in allForms.data">
                                        <td>{{ forms.id }}</td>
                                        <td>{{ forms.form.name }}</td>
                                        <td>{{ forms.user.name }}</td>
                                        <td>{{ dateTask(forms.created_at) }}</td>
                                        <td>{{ dateTask(forms.updated_at) }}</td>
                                        <td>
                                            <span class="badge w-100" :class="{
                                                'bg-danger': forms.status_form == 1,
                                                'bg-info': forms.status_form == 2 || forms.status_form == 4,
                                                'bg-success': forms.status_form == 3
                                            }">
                                                {{ forms.task }}
                                            </span>
                                        </td>
                                        <td>
                                            <Link :href="'/form/' + forms.form.slung + '/detalhes/' + forms.id">
                                            <button class="btn-icon btn-info">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            </Link>
                                            <Link :href="'/form/' + forms.form.slung + '/detalhes/' + forms.id">
                                            <button class="btn-icon btn-info">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">

                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-end">
                                    <li v-for="(link, index) in allForms.links" class="page-item"
                                        v-bind:class="{ active: link.active }">
                                        <Link href="#" @click="search(link.label)"
                                            v-if="Number.isInteger(parseInt(link.label))" class="page-link">
                                        {{ link.label }}
                                        </Link>
                                    </li>
                                </ul>
                            </nav>

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
import { Link } from '@inertiajs/inertia-vue3';
import JetInputSearch from "@/Jetstream/Input.vue";

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        Link,
        JetInputSearch
    },
    props: ["allForms"],
    methods: {
        dateTask(dateIni) {
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

            var retorno = date.getDate() + ' ' + mes +
                ', ' + date.getFullYear() +
                ' ' + date.getHours() +
                ':' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + '';

            return retorno;
        },
        search(page) {
            this.$inertia.get(
                this.$inertia.page.url,
                {
                    page: page,
                    buscar: this.buscar.toUpperCase()
                },
                { preserveState: true }
            );
        },
        destroy(id) {
            this.$inertia.post(route('form.destroy', id));
        }
    },
    data() {
        return {
        };
    },
});
</script>