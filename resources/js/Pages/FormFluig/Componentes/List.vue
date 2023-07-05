<template>
    <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
        <div class="row mb-3">
            <div class="col-md-5">
                <label class="form-label">Por número</label>
                <jet-input-search id="buscar" type="text" v-model="formbuscar.numero" placeholder="Buscar por numero..." />
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" v-model="formbuscar.aberto" type="checkbox" id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Abertos</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" v-model="formbuscar.finalizado" type="checkbox"
                        id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Finalizado</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" v-model="formbuscar.cancelado" type="checkbox"
                        id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Cancelado</label>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">De:</label>
                <jet-input-search v-model="formbuscar.dataDe" type="date" />
            </div>
            <div class="col-md-2">
                <label class="form-label">Até:</label>
                <jet-input-search v-model="formbuscar.dataAte" type="date" />
            </div>
            <div class="col-md-1">
                <button class="btn btn-info" style="margin-top: 30px;" @click="search">buscar</button>
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
                                    <th scope="col">Tarefa Atual</th>
                                    <th scope="col">Responsável</th>
                                    <th scope="col">Ultima Movimentação</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Opções</th>
                                </tr>
                            </thead>
                            <tbody v-if="!loading">
                                <tr v-for="forms in allForms.data">
                                    <td><b>{{ forms.id }}</b></td>
                                    <td>{{ forms.form.name }}</td>
                                    <td>{{ forms.user.name }}</td>
                                    <td>{{ dateTask(forms.created_at) }}</td>
                                    <td>
                                        {{ forms.task }}
                                    </td>
                                    <td>
                                        <p v-if="forms.group != null">
                                            Atribuido ao grupo:
                                            {{ forms.group.name }}
                                        </p>

                                        <p v-if="forms.atual_aprouver != null">
                                            {{ forms.atual_aprouver.name }}
                                        </p>
                                    </td>
                                    <td>{{ dateTask(forms.updated_at) }}</td>
                                    <td>
                                        <span class="badge w-100" :class="{
                                            'bg-danger': forms.status_form == 1,
                                            'bg-secondary': forms.status_form == 4,
                                            'bg-success': forms.status_form == 3,
                                            'bg-info': forms.status_form == 2
                                        }">
                                            <div v-if="forms.status_form == 1">Cancelado</div>
                                            <div v-if="forms.status_form == 2">Salvo</div>
                                            <div v-if="forms.status_form == 3">Aberto</div>
                                            <div v-if="forms.status_form == 4">Finalizado</div>
                                        </span>
                                    </td>
                                    <td>
                                        <table>
                                            <tr>
                                                <th style="padding: 0px !important;">
                                                    <Link :href="route('form.' + forms.form.slung + '.ver', forms.id)">
                                                    <button class="btn-icon btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    </Link>
                                                </th>
                                                <th style="padding: 0px !important;" v-if="$page.props.user.role_id == 1">
                                                    <button @click="abreModalDelete(), this.idDestroy = forms.id"
                                                        class="btn-icon btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- <tr v-else>
                                    <td class="text-center" colspan="7">
                                        Sem resultado
                                    </td>
                                </tr> -->
                            </tbody>

                            <tbody v-else>
                                <tr>
                                    <th scope="col text-center" colspan="9">
                                        <div class="text-center">
                                            <div class="spinner-border text-info" style="width: 5rem; height: 5rem;"
                                                role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                    <div class="col-md-12">

                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-end">
                                <li v-for="(link, index) in allForms.links" class="page-item"
                                    v-bind:class="{ active: link.active }">
                                    <button @click="search(link.label)" v-if="Number.isInteger(parseInt(link.label))"
                                        class="page-link">
                                        {{ link.label }}
                                    </button>
                                </li>
                            </ul>
                        </nav>

                        <!-- ModalDelete -->
                        <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-content">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Deletar</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h1 class="text-danger" style="font-size: 73px;">
                                            <i class="bi bi-exclamation-diamond"></i>
                                        </h1>
                                        <h3>Deseja mesmo deletar essa requisição?</h3>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Desistir</button>
                                        <button type="button" @click="destroy()" class="btn btn-primary">Deletar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
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
    props: [],
    mounted() {
        this.modalDeletes = new bootstrap.Modal(document.getElementById("modalDelete"), {});
        this.search(1);
    },
    methods: {
        abreModalDelete() {
            this.modalDeletes.show()
        },
        fechaModalDelete() {
            this.modalDeletes.hide()
        },
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
        async search(page) {
            this.loading = true
            await axios.get(route('api.forms.allFormsList'), {
                params: {
                    page: page,
                    buscar: this.formbuscar
                }
            }).then(response => {
                this.allForms = response.data.allForms;
            });
            this.loading = false
        },
        destroy() {
            this.$inertia.post(route('form.destroy', this.idDestroy), {}, {
                onSuccess: () => {
                    this.idDestroy = null;
                    this.fechaModalDelete();
                    this.search(1);
                }
            });
        }
    },
    data() {
        return {
            loading: false,
            allForms: [],
            modalDeletes: '',
            idDestroy: null,
            formbuscar: {
                numero: '',
                dataDe: '',
                dataAte: '',
                aberto: '',
                finalizado: '',
                cancelado: ''
            },
        };
    },
});
</script>