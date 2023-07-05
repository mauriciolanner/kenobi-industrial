<template>
  <jet-action-message :on="form.recentlySuccessful">
    usuário criado
  </jet-action-message>
  <jet-form-section @submit="store">
    <template #title> Criar novo usuário</template>
    <template #form>
      <div class="col-md-12 mt-4" v-if="$page.props.user.role_id == 1 || $page.props.user.role_id == 2">
        <jet-label for="roles" value="Escolha o tipo de usuário que deseja criar" />
        <VueMultiselect v-model="selected" label="name" :placeholder="'Escolha uma opção'"
          :selectedLabel="'Selecionado'" :deselectLabel="'remover'" :selectLabel="'Selecionar'" @select="dispatchAction"
          :options="options" @search-change="asyncFind">
        </VueMultiselect>
      </div>

      <div class="col-md-12 mt-4" v-if="form.role != ''">
        <jet-label for="name" value="Nome do usuário" />
        <jet-input id="name" type="text" v-model="form.name" />

        <small class="form-text text-danger">
          {{ form.errors.name }}
        </small>
      </div>

      <div class="col-md-12 mt-4" v-if="form.role != ''">
        <jet-label for="user_name" value="Id do usuário" />
        <jet-input id="user_name" type="text" v-model="form.user_name" />

        <small class="form-text text-danger">
          {{ form.errors.user_name }}
        </small>
      </div>

      <div class="col-md-12 mt-4" v-if="form.role_id == 3">
        <jet-label for="cnpj" value="CNPJ" />
        <jet-input id="cnpj" disabled="disabled" type="text" v-model="form.cnpj" />

        <small class="form-text text-danger">
          {{ form.errors.cnpj }}
        </small>
      </div>

      <div class="col-md-12 mt-4" v-if="form.role != ''">
        <jet-label for="email" value="E-mail" />
        <jet-input id="email" type="text" v-model="form.email" />

        <small class="form-text text-danger">
          {{ form.errors.email }}
        </small>
      </div>

      <div class="col-md-12 mt-4">
        <jet-label for="passwordOne" value="Senha" />
        <jet-input id="passwordOne" v-bind:class="{ 'is-invalid': classError, 'is-valid': classConfirm }" type="text"
          v-model="form.passwordOne" />

        <small class="form-text text-danger">
          {{ form.errors.passwordOne }}
        </small>
      </div>

      <div class="col-md-12 mt-4">
        <jet-label for="password" value="Confirmar senha" />
        <jet-input id="password" v-bind:class="{ 'is-invalid': classError, 'is-valid': classConfirm }" type="text"
          v-on:keyup="confirmPassword" v-model="form.password" />

        <small class="form-text text-danger">
          {{ form.errors.password }}
        </small>
      </div>

    </template>


    <template #actions>
      <jet-button :class="form.processing" :disabled="form.processing || classError">
        Salvar
        <div v-show="form.processing" class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden"></span>
        </div>
      </jet-button>

      <jet-button @click="$emit('voltaParaLista')"> voltar </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import VueMultiselect from 'vue-multiselect';

export default defineComponent({
  components: {
    JetButton,
    JetInput,
    JetInputError,
    JetLabel,
    JetActionMessage,
    JetValidationErrors,
    JetFormSection,
    VueMultiselect,
  },
  props: [
    'roles'
  ],
  data() {
    return {
      selected: null,
      options: this.roles,
      optionsCust: this.customers,
      optionsClientes: this.verCustomer,
      selectedRegiao: null,
      selectedCLiente: null,
      selectedComercial: null,
      classError: false,
      classConfirm: null,
      form: this.$inertia.form({
        name: "",
        user_name: "",
        cnpj: "",
        role_id: "",
        email: "",
        password: "",
        passwordOne: "",
        user_id: "",
        A1_COD: "",
        regiao: "",
      }),
    };
  },
  methods: {
    nameWithLang({ A1_NOME, A1_CGC }) {
      return `${A1_NOME + ' - CNPJ: ' + A1_CGC}`
    },
    asyncFind(query) {
      this.$inertia.get(
        "/usuarios/master",
        { buscarZoom: query },
        { preserveState: true }
      );
    },
    asyncFindRegiao(query) {
      this.$inertia.get(
        "/usuarios/master",
        { buscarZoomRegiao: query },
        { preserveState: true }
      );
    },
    asyncFindCust(query) {
      this.$inertia.get(
        "/usuarios/master",
        { buscarZoomCust: query },
        { preserveState: true }
      );
    },
    asyncFindClientes(query) {
      this.$inertia.get(
        "/usuarios/master",
        { buscaProt: query.toUpperCase() },
        {
          preserveState: true,
          preserveScroll: true,
          replace: true
        }
      );
    },
    asyncFindFunc(query) {
      this.$inertia.get(
        "/usuarios/master",
        { buscaFunc: query.toUpperCase() },
        {
          preserveState: true,
          preserveScroll: true,
          replace: true
        }
      );
    },
    dispatchAction(selectItem) {
      this.limparCampos();
      this.form.role_id = selectItem.id;
    },
    dispatchActionCust(selectItem) {
      this.form.cnpj = selectItem.cnpj
    },
    dispatchActionCliente(selectItem) {
      this.form.name = selectItem.A1_NOME
      this.form.email = selectItem.A1_EMAIL
      this.form.regiao = selectItem.A1_VEND
      this.form.cnpj = selectItem.A1_CGC
      this.form.A1_COD = selectItem.A1_COD
    },
    dispatchActionComercial(selectItem) {
      this.form.name = selectItem.Funcionario
      this.form.email = selectItem.Email
      this.form.user_name = selectItem.Login

    },
    dispatchActionRegiao(selectItem) {
      //this.form.regiao_protheus = selectItem.A3_COD
    },
    store() {
      this.form.post(route("usuarios.store"), {
        erroBag: "UserRequest",
        preserveScroll: true,
        onSuccess: (result) => {
          this.$emit("voltaParaLista");
          this.reset();
        },
      });
    },
    confirmPassword() {
      if (this.form.password != this.form.passwordOne) {
        this.classError = true
        this.classConfirm = false
      } else {
        this.classError = false
        this.classConfirm = true
      }
    },
    confereCpnj() {
      $('#cnpj').mask('00.000.000/0000-00');
      this.$inertia.get(
        "/usuarios/master",
        { buscaProt: this.form.cnpj },
        { preserveState: true }
      );
      if (this.verCustomer != '') {
        console.log('this.verCustomer');
        console.log(this.verCustomer);
        this.form.name = this.verCustomer.A1_NOME
        this.form.email = this.verCustomer.A1_EMAIL
        this.form.A1_COD = this.verCustomer.A1_COD
        this.form.regiao = this.verCustomer.A1_VEND
      }
    },
    reset() {
      this.form.emp_nom_empresa = "";
      this.emp_dti_atividade = "";
    },
    limparCampos() {
      this.formname = "";
      this.formuser_name = "";
      this.formcnpj = "";
      this.formrole_id = "";
      this.formemail = "";
      this.formpassword = "";
      this.formpasswordOne = "";
      this.formuser_id = "";
      this.formA1_COD = "";
      this.formregiao = "";
    },
  },
});
</script>
<style src="vue-multiselect/dist/vue-multiselect.css">

</style>