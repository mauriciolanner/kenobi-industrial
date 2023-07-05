<template>
    <app-layout title="Configurações de formulários">
        <template #header>
            <h2 class="h4 font-weight-bold">
                {{ formInfo.name }}
            </h2>
        </template>
        <div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button class="btn btn-info" @click="abreModal('modalNovaTarefa')">
                                    Criar tarefa
                                </button>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div id="tracking-pre"></div>
                                <div id="tracking">
                                    <div class="text-center tracking-status-intransit">
                                        <p class="tracking-status text-tight">Fluxo do formulário</p>
                                    </div>
                                    <div class="tracking-list">

                                        <div class="tracking-item" v-for="task in formInfo.fluxo">

                                            <button class="btn" @click="abreModal('editaTarefa'); formEditeOpem(task)">
                                                <div v-if="task.ordem == 1" class="tracking-icon status-inforeceived">
                                                    <i class="bi bi-arrow-right-short"></i>
                                                </div>
                                                <div v-if="task.ordem == formInfo.fluxo.length"
                                                    class="tracking-icon status-outfordelivery">
                                                    <i class="bi bi-check2-circle"></i>
                                                </div>
                                                <div v-if="task.type == 2" class="tracking-icon status-delivered">
                                                    <i class="bi bi-check-all"></i>
                                                </div>
                                                <!--div class="tracking-date">09 Julho, 20022<span>11:04</span></div-->
                                                <div class="tracking-content">{{ task.name }}
                                                    <span>por: Usuário iniciador</span>
                                                </div>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--modal edita-->
            <div class="modal fade" id="editaTarefa" tabindex="-1" aria-labelledby="editaTarefaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-end">
                            Editar tarefa {{ formEdit.name }}
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row text-start">
                                <div class="col-md-12 mt-4">
                                    <jet-label for="name" value="Nome da tarefa" />
                                    <jet-input id="name" type="text" v-model="formEdit.name" />

                                    <small class="form-text text-danger">
                                        {{ formEdit.errors.name }}
                                    </small>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <jet-label for="ordem" value="Ordem" />
                                    <jet-input id="ordem" type="text" v-model="formEdit.ordem" />

                                    <small class="form-text text-danger">
                                        {{ formEdit.errors.ordem }}
                                    </small>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <jet-label for="user_id" value="Usuário aprovador" />
                                    <!-- <jet-input id="user_id" type="text" v-model="formEdit.user_id" /> -->
                                    <VueMultiselect v-model="userSelect" label="name" :placeholder="'Escolha uma opção'"
                                        @select="selectedUSer" :selectedLabel="'Selecionado'" :deselectLabel="'remover'"
                                        :selectLabel="'Selecionar'" :options="users">
                                    </VueMultiselect>

                                    <small class="form-text text-danger">
                                        {{ formEdit.errors.user_id }}
                                    </small>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" @click="edit()">SALVAR</button>
                            <button type="button" class="btn btn-secondary" @click="fechaModal()">FECHAR</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--modal novo-->
            <div class="modal fade" id="modalNovaTarefa" tabindex="-1" aria-labelledby="modalNovaTarefaLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row text-start">
                                <div class="col-md-12 mt-4">
                                    <jet-label for="name" value="Nome da tarefa" />
                                    <jet-input id="name" type="text" v-model="form.name" />

                                    <small class="form-text text-danger">
                                        {{ form.errors.name }}
                                    </small>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <jet-label for="ordem" value="Ordem" />
                                    <jet-input id="ordem" type="text" v-model="form.ordem" />

                                    <small class="form-text text-danger">
                                        {{ form.errors.ordem }}
                                    </small>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <jet-label for="type" value="Tipo" />
                                    <!-- <jet-input id="type" type="text" v-model="form.type" /> -->

                                    <VueMultiselect v-model="tipoSelect" label="nome" :placeholder="'Escolha uma opção'"
                                        @select="selectedTipo" :selectedLabel="'Selecionado'" :deselectLabel="'remover'"
                                        :selectLabel="'Selecionar'" :options="tipos">
                                    </VueMultiselect>

                                    <small class="form-text text-danger">
                                        {{ form.errors.type }}
                                    </small>
                                </div>
                                <div class="col-md-12 mt-4" v-if="form.type == 2">
                                    <jet-label for="user_id" value="Usuário aprovador" />
                                    <!-- <jet-input id="user_id" type="text" v-model="form.user_id" /> -->
                                    <VueMultiselect v-model="userSelect" label="name" :placeholder="'Escolha uma opção'"
                                        @select="selectedUSer" :selectedLabel="'Selecionado'" :deselectLabel="'remover'"
                                        :selectLabel="'Selecionar'" :options="users">
                                    </VueMultiselect>

                                    <small class="form-text text-danger">
                                        {{ form.errors.user_id }}
                                    </small>
                                </div>

                                <div class="col-md-12 mt-4" v-if="form.type == 3">
                                    <jet-label for="user_id" value="Grupo Atribuido" />
                                    <!-- <jet-input id="user_id" type="text" v-model="form.user_id" /> -->
                                    <VueMultiselect v-model="roleSelect" label="name" :placeholder="'Escolha uma opção'"
                                        @select="selectedGruop" :selectedLabel="'Selecionado'" :deselectLabel="'remover'"
                                        :selectLabel="'Selecionar'" :options="roles">
                                    </VueMultiselect>

                                    <small class="form-text text-danger">
                                        {{ form.errors.user_id }}
                                    </small>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" @click="store()">SALVAR</button>
                            <button type="button" class="btn btn-secondary" @click="fechaModal()">FECHAR</button>
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
import Modal from '@/Components/Modal.vue';
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import VueMultiselect from 'vue-multiselect';
import axios from "axios";

export default defineComponent({
    components: {
        AppLayout,
        Modal,
        JetButton,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetLabel,
        JetValidationErrors,
        VueMultiselect,
    },
    mounted() {
        this.usersAll();
    },
    methods: {
        abreModal(id) {
            this.modal = new bootstrap.Modal(document.getElementById(id), {})
            this.modal.show()
        },
        fechaModal() {
            this.modal.hide()
        },
        formEditeOpem(item) {
            console.log(item)
            this.formEdit.id = item.id;
            this.formEdit.name = item.name;
            this.formEdit.ordem = item.ordem;
            this.formEdit.type = item.type;
            this.formEdit.group = item.group;
        },
        async usersAll() {
            await axios.get(route('API.utei.usuarios'))
                .then(response => {
                    this.users = response.data;
                });
            await axios.get(route('API.utei.roles'))
                .then(response => {
                    this.roles = response.data;
                });
        },
        selectedTipo(item) {
            this.form.type = item.id
        },
        selectedUSer(item) {
            this.form.user_id = item.id
            this.form.group = null
            this.roleSelect = null
        },
        selectedGruop(item) {
            this.form.group = item.id
            this.form.user_id = null
            this.userSelect = null
        },
        store() {
            this.form.post(route('form.config.create'), {
                //erroBag: "UserRequest",
                preserveScroll: true,
                onSuccess: (result) => {
                    this.fechaModal('cadastra')
                },
            });
        },
        edit() {
            this.formEdit.post(route('form.config.edit'), {
                //erroBag: "UserRequest",
                preserveScroll: true,
                onSuccess: (result) => {
                    this.fechaModal()
                },
            });
        },
    },
    props: ['formInfo'],
    data() {
        return {
            criarNovo: false,
            modal: '',
            tipos: [
                {
                    id: 1,
                    nome: 'inicio'
                },
                {
                    id: 2,
                    nome: 'Atividade a usuário unico'
                },
                {
                    id: 3,
                    nome: 'Atividade atribuido a Grupo'
                },
                {
                    id: 4,
                    nome: 'Encerramento'
                },
                {
                    id: 5,
                    nome: 'Looping'
                },
            ],
            tipoSelect: null,
            userSelect: null,
            roleSelect: null,
            users: [],
            roles: [],
            form: this.$inertia.form({
                name: "",
                ordem: "",
                type: "",
                user_id: null,
                group: null,
                status: "",
                form_id: this.formInfo.id,
            }),
            formEdit: this.$inertia.form({
                id: "",
                name: "",
                ordem: "",
                type: "",
                user_id: null,
                group: null,
                status: "",
                form_id: this.formInfo.id,
            }),
        };
    },
});
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>