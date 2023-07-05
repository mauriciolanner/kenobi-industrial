<template>
  <div class="card-body card-tabelas bg-white shadow-sm border-bottom rounded-top table-responsive">
    <div class="row mb-3">
      <div class="col-md-1">
        <button :class="'btn btn-criar'" v-on:click="criarNovo = true" @click="$emit('voltaParaLista')">
          <i class="bi bi-person-plus"></i>
        </button>
      </div>
      <div class="col-md-11">
        <jet-input-search id="buscar" :class="'mt-3'" type="text" v-model="buscar" @keyup="search"
          placeholder="Buscar..." />
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped table-padrao">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col">Nome</th>
              <th scope="col">Grupo</th>
              <th scope="col">Usuário</th>
              <th scope="col">E-mail</th>
              <th scope="col">Ut. Acesso</th>
              <th scope="col">Status</th>
              <th scope="col" style="width: 86px;" class="text-end"
                v-if="$page.props.user.role_id == 1 || $page.props.user.role_id == 2">
                Opções
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in allUsers" :key="allUsers.id">
              <th>
                <img v-if="$page.props.jetstream.managesProfilePhotos" class="rounded-circle"
                  style="margin-right: 10px;" width="32" height="32" :src="user.profile_photo_url" :alt="user.name" />
              </th>
              <td>{{ user.name }}</td>
              <td>
                <span v-if="user.role_id == 1" class="badge bg-success p-2">{{ user.role.name }}</span>
                <span v-if="user.role_id == 2" class="badge bg-primary p-2">{{ user.role.name }}</span>
                <span v-if="user.role_id == 3" class="badge bg-warning p-2">{{ user.role.name }}</span>
                <span v-if="user.role_id == 4" class="badge bg-dark p-2">{{ user.role.name }}</span>
              </td>
              <td>{{ user.user_name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <div v-if="user.updated_at != null">
                  {{ user.updated_at }}
                </div>
              </td>
              <td>
                <span v-if="user.status == 1" class="badge bg-success p-2">Ativo</span>
                <span v-if="user.status == 0" class="badge bg-danger p-2">Inativo</span>
              </td>
              <td class="text-end">
                <Link v-if="($page.props.user.role_id == 1 || $page.props.user.role_id == 2) && user.PR_CLIENTE != null"
                  :href="'/perfil/' + user.PR_CLIENTE">
                <button class="btn-icon btn-info">
                  <i class="bi bi-eye"></i>
                </button>
                </Link>
                <button
                  v-if="($page.props.user.role_id == 1 || $page.props.user.role_id == 2) && user.id != $page.props.user.id"
                  class="btn-icon" v-bind:class="{ 'btn-success': user.status == 0, 'btn-danger': user.status == 1 }"
                  @click="destroy(user.id)">
                  <i class="bi " v-bind:class="{ 'bi-lock': user.status == 0, 'bi-unlock': user.status == 1 }"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInputSearch from "@/Jetstream/Input.vue";
import ShowPerfil from "./Show.vue";
import moment from "moment";
import { Link } from '@inertiajs/inertia-vue3'

export default defineComponent({
  components: {
    JetButton,
    JetInputSearch,
    Link,
    ShowPerfil,
  },
  emits: ["voltaParaLista"],
  props: ["allUsers"],
  methods: {
    moment: function () {
      return moment();
    },
    search() {
      this.$inertia.get(
        "/usuarios/master",
        { buscar: this.buscar },
        { preserveState: true }
      );
    },
    destroy(id) {
      this.$inertia.get("/usuario/bloquear/" + id),
      {
        erroBag: "EmpEmpresaRequest",
        preserveScroll: true,
      };
    },
    updateSpecial(id) {
      this.$inertia.put("/empresa/edita/especial/" + id);
    },
  },
  data() {
    return {
      buscar: "",
    };
  },
});
</script>
