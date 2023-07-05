<template>
    <app-layout title="Configurações de formulários">
        <template #header>
            <h2 class="h4 font-weight-bold">Configurações de formulários</h2>
        </template>
        <div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button @click="abreModal" class="btn btn-success">CRIAR</button>
                            </div>
                            <div class="col-md-12 mt-3">
                                <table class="table table-striped table-padrao">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nome</th>
                                            <th scope="col">Slung</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" style="width: 90px;" class="text-end"
                                                v-if="$page.props.user.role_id == 1 || $page.props.user.role_id == 2">
                                                Opções
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="form in forms" :key="forms.id">
                                            <td>{{ form.name }}</td>
                                            <td>{{ form.slung }}</td>
                                            <td>
                                                <span v-if="form.status == 1" class="badge bg-primary w-100">Ativo</span>
                                                <span v-if="form.status == 0"
                                                    class="badge bg-danger w-100">Desativado</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn-icon btn-info" @click="irLink(form.id)">
                                                    <i class="bi bi-gear"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalNovo" tabindex="-1" aria-labelledby="modalNovoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-end">
                        <h5>Novo formulário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                <jet-label for="periodoMovimento" value="Nome do formulário" />
                                <jet-input id="periodoMovimento" type="text" v-model="form.name"
                                    :readonly="readonlyVerific" />

                                <small class="form-text text-danger">
                                    {{ form.errors.periodoMovimento }}
                                </small>
                            </div>
                            <div class="col-md-12 mt-3">
                                <jet-label for="periodoMovimento" value="Slung" />
                                <jet-input id="periodoMovimento" type="text" v-model="form.slung"
                                    :readonly="readonlyVerific" />

                                <small class="form-text text-danger">
                                    {{ form.errors.periodoMovimento }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="fechaModal">Fechar</button>
                        <button type="button" @click="storeForm()" class="btn btn-info">
                            Criar form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default defineComponent({
    components: {
        AppLayout,
        JetInput,
        JetLabel,
    },
    mounted() {
        this.modalNovo = new bootstrap.Modal(document.getElementById("modalNovo"), {});
    },
    methods: {
        irLink(id) {
            this.$inertia.get(route('form.config.config', id));
        },
        abreModal() {
            this.modalNovo.show()
        },
        fechaModal() {
            this.modalNovo.hide()
        },
        storeForm() {
            this.form.post(route('form.config.store'), {
                preserveScroll: true,
                onSuccess: (result) => {
                    this.form.name = '';
                    this.form.slung = '';
                    this.fechaModal()
                },
            });
        }
    },
    props: ['forms'],
    data() {
        return {
            criarNovo: false,
            modalNovo: '',
            form: this.$inertia.form({
                name: '',
                slung: ''
            })
        };
    },
});
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>