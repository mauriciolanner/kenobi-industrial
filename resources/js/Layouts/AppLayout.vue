<template>
  <div>

    <Head :title="title" />

    <jet-banner />

    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top full-scream">
      <div class="container">
        <!-- Logo -->
        <Link class="navbar-brand me-4" :href="route('dashboard')">

        <jet-application-mark width="180" />
        </Link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav me-auto">
          </ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav align-items-baseline text-center"
            :class="{ 'navbar-dark': $page.props.user.dark_mode == 1 }">
            <button v-if="$page.props.notifications.length != 0" type="button" data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"
              class="btn btn-link position-relative notificate">
              <i class="bi bi-bell"></i>
              <span class="position-absolute top-0 translate-middle badge rounded-pill bg-danger">
                {{ $page.props.notifications.length }}
              </span>
            </button>

            <!-- Authentication Links -->
            <jet-dropdown id="settingsDropdown">
              <template #trigger>
                <img v-if="$page.props.user.profile_photo_path" class="rounded-circle" style="margin-right: 10px;"
                  width="32" height="32"
                  :src="'https://192.168.254.74/bomixsmp/storage/' + $page.props.user.profile_photo_path" :alt="'SF'" />
                <img v-else class="rounded-circle" style="margin-right: 10px;" width="32" height="32"
                  :src="$page.props.user.profile_photo_url" :alt="'SF'" />

                {{ $page.props.user.name }}
              </template>

              <template #content>
                <div class="text-menususp px-4" v-if="$page.props.user.coligada == 1">
                  Coligada: BOMIX - Matriz
                </div>
                <div class="text-menususp px-4" v-if="$page.props.user.coligada == 2">
                  Coligada: BOMIX - Divisão Sopro
                </div>
                <div class="text-menususp px-4" v-if="$page.props.user.coligada == 3">
                  Coligada: BOMIXLOG
                </div>
                <div class="text-menususp px-4" v-if="$page.props.user.coligada == 4">
                  Coligada: RAMI
                </div>
                <div class="text-menususp px-4" v-if="$page.props.user.alteraColigada == 1">
                  <button style="border-radius: 10px;" class="btn btn-success w-100" @click="abreModalColigada()">
                    Alterar coligada
                  </button>
                </div>
                <hr class="dropdown-divider">
                <!-- Account Management -->
                <h6 class="dropdown-header small text-muted">
                  Meus dados
                </h6>

                <jet-dropdown-link :href="route('profile.show')">
                  <i class="bi bi-person-fill"></i> Perfil
                </jet-dropdown-link>

                <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                  API Tokens
                </jet-dropdown-link>

                <hr class="dropdown-divider">

                <h6 class="dropdown-header small text-muted">
                  Configurações
                </h6>

                <jet-dropdown-link :href="route('usuarios.master')" v-if="$page.props.user.role_id != 4">
                  <i class="bi bi-people-fill"></i> Usuários do sistema
                </jet-dropdown-link>

                <jet-dropdown-link :href="route('form.config')" v-if="$page.props.user.role_id == 1">
                  <i class="bi bi-people-fill"></i> Formulário
                </jet-dropdown-link>

                <hr class="dropdown-divider">

                <!-- Authentication -->
                <form @submit.prevent="logout">
                  <jet-dropdown-link as="button">
                    <i class="bi bi-box-arrow-right"></i> Sair
                  </jet-dropdown-link>
                </form>
              </template>
            </jet-dropdown>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="d-flex bg-white shadow-sm border-bottom full-scream-menu">

      <div class="offcanvas offcanvas-end collapse-notification" tabindex="-1" id="offcanvasRight"
        aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasRightLabel">
            <div class="text-center">
              <!-- <Link :href="route('limparTudonotificacao')">Limpar todas notificações</Link> -->
            </div>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div v-if="!loadingNotifications" v-for="notification in $page.props.notifications" class="card-notification"
            :class="{ 'card-notification-active': notification.status == 2 }">
            <div class="row">
              <div class="col-2">
                <button class="btn" @click="goForm(notification.form_number)">
                  <div class="ico-notification">
                    <i class="bi bi-envelope-check"></i>
                  </div>
                </button>
              </div>
              <div class="col-10">

                <button class="btn" @click="goForm(notification.form_number)">
                  <h5>{{ notification.title }}</h5>
                </button>
                <p>
                  {{ notification.message }}
                  <br>
                  em: {{ moment(notification.created_at).format("DD.MM.YYYY - H:mm:ss") }}
                </p>
              </div>
            </div>
          </div>

          <div class="row" v-else>
            <div class="d-flex justify-content-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">

        <nav class="navbar navbar-principal navbar-expand-lg align-items-center">
          <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <jet-nav-link :href="route('forms.dashboard')" :class="'pading-x'">
                  Ver requisições
                </jet-nav-link>

                <jet-nav-link v-if="$page.props.controllersAccess.borderoView == 1" :href="route('form.Modelo.index')"
                  :class="'pading-x'">
                  Modelo Formulário
                </jet-nav-link>

              </ul>
            </div>
          </div>
        </nav>

      </div>
    </header>

    <div class="container">
      <div class="header-text p-3">
        <slot name="header"></slot>
      </div>
    </div>

    <!--modal coligada-->
    <div class="modal fade" id="modalColigada" tabindex="-1" aria-labelledby="modalColigadaLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header text-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">


            <VueMultiselect v-model="coligadaSelect" label="nome" :placeholder="'Escolha uma opção'"
              :selectedLabel="'Selecionado'" :deselectLabel="'remover'" :selectLabel="'Selecionar'"
              @select="alteraColigada" :options="coligadas">
            </VueMultiselect>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="fechaModalColigada()">FECHAR</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Page Content -->
    <main class="container placeholder-glow">

      <!-- Alert -->
      <div class="col-md-12" id="a">
        <div class="alert alert-dismissible fade show" v-if="$page.props.flash.type && !$page.props.flash.form"
          :class="$page.props.flash.type" role="alert">
          <strong>{{ $page.props.flash.title }}</strong> <br> {{ $page.props.flash.message }}
          <button type="button" class="btn-close" @click="$page.props.flash.type = null"></button>
        </div>
      </div>

      <slot></slot>

      <footer class="py-3 my-4">
        <p class="text-center text-muted">© 2023 Bomix Base, Versão 2.0</p>
      </footer>
    </main>
  </div>
</template>

<script>
import JetApplicationLogo from '@/Jetstream/ApplicationLogo.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import JetApplicationMark from '@/Jetstream/ApplicationMark.vue'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
import JetNavLink from '@/Jetstream/NavLink.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import moment from "moment";
import VueMultiselect from 'vue-multiselect';

export default {
  props: {
    title: String,
  },

  components: {
    Head,
    JetApplicationLogo,
    JetBanner,
    JetApplicationMark,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    Link,
    VueMultiselect
  },

  created: () => {

  },

  mounted() {
    this.allNotification();
    this.canvasNotification = new bootstrap.Offcanvas(document.getElementById('offcanvasRight'), {})

    if (typeof this.darkMode !== 'undefined') {
      if (this.darkMode == '1')
        bodyPrincipal.setAttribute('data-bs-theme', 'dark')
      else
        bodyPrincipal.setAttribute('data-bs-theme', 'light')
    }
  },

  data() {
    return {
      showingNavigationDropdown: false,
      notifications: [],
      loadingNotifications: false,
      modalColigada: '',
      canvasNotification: '',
      coligadaSelect: '',
      coligadas: [
        { nome: "Bomix - Matriz", codColigada: 1 },
        { nome: "BOMIX - Divisão Sopro", codColigada: 2 },
        { nome: "BOMIXLOG", codColigada: 3 },
        { nome: "RAMI", codColigada: 4 }
      ]
    }
  },

  methods: {
    async darkModel() {
      await axios.get(route('darkMode')).then(response => {
        console.log(response.data.dark_mode)
        if (response.data.dark_mode == '1')
          bodyPrincipal.setAttribute('data-bs-theme', 'dark')
        else
          bodyPrincipal.setAttribute('data-bs-theme', 'light')
      });
    },
    abreModalColigada() {
      this.modalColigada = new bootstrap.Modal(document.getElementById('modalColigada'), {})
      this.modalColigada.show()
    },
    fechaModalColigada() {
      this.modalColigada.hide()
    },
    async allNotification() {
      this.loadingNotifications = true

      await axios.get(route('notifications.opemTask'))
        .then(response => {
          this.notifications = response.data;
          console.log(this.notifications);
          this.loadingNotifications = false;
        }).catch(function (error) {
          console.error(error);
          this.loadingNotifications = false
        })
    },

    alteraColigada(item) {
      this.$inertia.post(route('usuarios.alteraColigada'), {
        'codColigada': item.codColigada
      }, {
        preserveState: false,
        onSuccess: () => this.fechaModalColigada(),
      })
    },

    abreOffcanvas(id) {
      this.canvasNotification.show()
    },

    fechaOffcanvas() {
      this.canvasNotification.hide()
    },

    switchToTeam(team) {
      this.$inertia.put(route('current-team.update'), {
        'team_id': team.id
      }, {
        preserveState: false
      })
    },

    goForm(formNumber) {
      console.log(formNumber);
      this.fechaOffcanvas()
      this.$inertia.get(route('form.view', formNumber), {
        preserveState: false
      })
    },

    moment: function () {
      return moment();
    },

    logout() {
      this.$inertia.post(route('logout'));
    },

    async deletAllNotification() {
      this.loadingNotifications = true
      await axios.get(route('notifications.clear'))
        .then(response => {
          this.allNotification();
          this.loadingNotifications = false;
        }).catch(function (error) {
          console.error(error);
          this.loadingNotifications = false
        })
    },

    async deletNotification(id) {
      this.loadingNotifications = true
      await axios.get(route('notifications.delet', id))
        .then(response => {
          this.allNotification();
          this.loadingNotifications = false;
        }).catch(function (error) {
          console.error(error);
          this.loadingNotifications = false
        })
    }
  },

  computed: {
    path() {
      return window.location.pathname
    }
  }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
