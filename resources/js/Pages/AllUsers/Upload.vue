<template>
  <button
    class="btn-icon btn-info"
    data-bs-toggle="modal"
    :data-bs-target="'#modalArquivos' + documento.doc_id_doc"
  >
    <i class="bi bi-eye"></i>
  </button>

  <jet-modal-dialog :id="'modalArquivos' + documento.doc_id_doc">
    <template #title>
      {{ documento.tdo_tipo_documento.tdo_nom_tipo_documento }}
    </template>
    <template #content>
      <div
        v-for="imagem in documento.ind_imagem_documento"
        :key="imagem.imd_id_imd"
        class="row mb-3"
      >
        <div class="col-2">
          <h4><i class="bi bi-file-earmark-text"></i></h4>
        </div>
        <div class="col-6">
          {{ imagem.imd_nom_arquivo }}
        </div>
        <div class="col-md-2">
          <img
            :src="'/storage/imagensDocumentos/' + imagem.imd_arquivo"
            :alt="imagem.imd_nom_arquivo"
            class="img-thumbnail max-imagem"
          />
        </div>
        <div class="col-2 text-end">
          <button
            class="btn-icon btn-danger"
            @click="destroy(imagem.imd_id_imd)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
      <div class="dropdown-divider"></div>
      <div class="row">
        <div class="col-md-4">
          <label>Enviar um arquivo</label>
          <jet-input
            type="file"
            id="imd_arquivo"
            @input="novoEnviaImagem.imd_arquivo = $event.target.files[0]"
            class="form-control"
          />
          <small class="form-text text-danger">
            {{ novoEnviaImagem.errors.imd_arquivo }}
          </small>
        </div>
        <div class="col-md-6">
          <jet-label for="imd_nom_arquivo" value="Nome do arquivo" />
          <jet-input
            id="imd_nom_arquivo"
            type="text"
            v-model="novoEnviaImagem.imd_nom_arquivo"
            class="form-control"
          />
          <small class="form-text text-danger">
            {{ novoEnviaImagem.errors.imd_nom_arquivo }}
          </small>
        </div>
        <div class="col-md-2">
          <jet-button
            @click="sendImagem"
            :class="novoEnviaImagem.processing"
            :disabled="novoEnviaImagem.processing"
            class="btn btn-success"
            style="margin-top: 23px"
          >
            <i class="bi bi-cloud-arrow-up"></i> Enviar
          </jet-button>
        </div>
      </div>
    </template>
  </jet-modal-dialog>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";
import JetModalDialog from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";

export default defineComponent({
  components: {
    JetButton,
    JetModalDialog,
    JetInput,
    JetInputError,
    JetLabel,
    JetActionMessage,
    JetValidationErrors,
    JetFormSection,
    JetSecondaryButton,
  },
  props: ["documento"],
  methods: {},
  data() {
    return {
      novoEnviaImagem: this.$inertia.form({
        imd_nom_arquivo: "",
        imd_arquivo: "",
        imd_id_doc: this.documento.doc_id_doc,
      }),
    };
  },
  methods: {
    destroy(id) {
      this.$inertia.put("/documento/arquivo/deletar/" + id),
        {
          preserveScroll: true,
        };
    },
    sendImagem() {
      this.novoEnviaImagem.post(route("documento.enviar"), {
        forceFormData: true,
        erroBag: "ImdImagemDocumentoRequest",
        preserveScroll: true,
        onSuccess: () => {
          this.resetImagem();
        },
      });
    },
    resetImagem() {
      this.novoEnviaImagem.imd_nom_arquivo = "";
      this.novoEnviaImagem.imd_arquivo = "";
    },
  },
});
</script>