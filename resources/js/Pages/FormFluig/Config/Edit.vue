<template>
    <div class="container">
        <div class="row text-start">
            <div class="col-md-12 mt-4" v-if="form.role != ''">
                <jet-label for="name" value="Nome da tarefa" />
                <jet-input id="name" type="text" v-model="form.name" />

                <small class="form-text text-danger">
                    {{ form.errors.name }}
                </small>
            </div>
            <div class="col-md-12 mt-4" v-if="form.role != ''">
                <jet-label for="ordem" value="Ordem" />
                <jet-input id="ordem" type="text" v-model="form.ordem" />

                <small class="form-text text-danger">
                    {{ form.errors.ordem }}
                </small>
            </div>
            <div class="col-md-12 mt-4" v-if="form.role != ''">
                <jet-label for="type" value="Tipo" />
                <jet-input id="type" type="text" v-model="form.type" />

                <small class="form-text text-danger">
                    {{ form.errors.type }}
                </small>
            </div>
            <div class="col-md-12 mt-4" v-if="form.role != ''">
                <jet-label for="user_id" value="Usuário aprovador" />
                <jet-input id="user_id" type="text" v-model="form.user_id" />

                <small class="form-text text-danger">
                    {{ form.errors.user_id }}
                </small>
            </div>

            <div class="col-md-12 mt-4">
                <button class="btn btn-success w-100" @click="edit">salvar</button>
            </div>

            <div class="col-md-12 mt-4">
                <button class="btn btn-danger w-100" @click="deletar">{{ deletarTask }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import TextInput from "@/Jetstream/InputText.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";

export default defineComponent({
    components: {
        AppLayout,
        JetButton,
        TextInput,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetLabel,
        JetValidationErrors,
    },
    methods: {
        edit() {
            this.form.post(route('form.config.edit'), {
                //erroBag: "UserRequest",
                preserveScroll: true,
                onSuccess: (result) => {
                    eval('edita' + this.task.id).style.display = 'table'
                },
            });
        },
        deletar() {
            this.deletarTask = 'Se deletar essa tarefa, será irreversivel, clique novamente para continuar'
            if (this.confirmaDeletar == 0)
                this.confirmaDeletar++
            else if (this.confirmaDeletar == 1)
                this.destroy()
        },
        destroy() {
            this.$inertia.get("/formulario/deletaTarefa/" + this.task.id),
            {
                erroBag: "EmpEmpresaRequest",
                preserveScroll: true,
            };
            this.confirmaDeletar = 0
        },
    },
    props: ['task'],
    data() {
        return {
            confirmaDeletar: 0,
            deletarTask: 'Deletar',
            criarNovo: false,
            form: this.$inertia.form({
                name: this.task.name,
                ordem: this.task.ordem,
                type: this.task.type,
                user_id: this.task.user_id,
                task_id: this.task.id
            }),
        };
    },
});
</script>