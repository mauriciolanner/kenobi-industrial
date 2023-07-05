<template>
    <app-layout title="Formulários">
        <template #header>
            <h2 class="h4 font-weight-bold">{{ form.name }}</h2>
        </template>
        <div>
            <div class="row tab-form">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <button class="nav-link" :class="{ active: tabs.form }" @click="tabsInfo(1)">Formulário</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" :class="{ active: tabs.workflow }" @click="tabsInfo(2)">Fluxo</button>
                    </li>
                </ul>
            </div>
            <!--Workflow-->
            <div class="row" v-show="tabs.workflow">
                <div class="col-md-12">
                    <div class="card-body bg-white shadow-sm border-bottom rounded-top table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tracking-pre"></div>
                                <div id="tracking">
                                    <div class="text-center tracking-status-intransit">
                                        <p class="tracking-status text-tight">Fluxo do formulário</p>
                                    </div>

                                    <div class="tracking-list" v-if="aprovacao.length == 0">
                                        <div class="tracking-item" v-for="task in formFluxo">
                                            <div v-if="task.ordem == 1" class="tracking-icon status-inforeceived">
                                                <i class="bi bi-arrow-right-short"></i>
                                            </div>
                                            <div v-if="task.ordem == formFluxo.length"
                                                class="tracking-icon status-intransit">
                                                <i class="bi bi-check2-circle"></i>
                                            </div>
                                            <div v-if="task.type == 2" class="tracking-icon status-intransit">
                                                <i class="bi bi-check-all"></i>
                                            </div>
                                            <!--div class="tracking-date">09 Julho, 20022<span>11:04</span></div-->
                                            <div class="tracking-content">{{ task.name }}
                                                <!-- <span>Atribuido a: Usuário iniciador</span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tracking-list" v-else>
                                        <div class="tracking-item" v-for="task in aprovacao">
                                            <div v-if="task.ordem == 1" class="tracking-icon status-inforeceived">
                                                <i class="bi bi-arrow-right-short"></i>
                                            </div>
                                            <div v-if="task.type == 4 && task.status == 1"
                                                class="tracking-icon status-intransit">
                                                <i class="bi bi-check2-circle"></i>
                                            </div>
                                            <div v-if="task.type == 4 && task.status == 2"
                                                class="tracking-icon status-inforeceived">
                                                <i class="bi bi-check2-circle"></i>
                                            </div>
                                            <div v-if="task.type == 4 && task.status == 0"
                                                class="tracking-icon status-sponsored">
                                                <i class="bi bi-dash-circle"></i>
                                            </div>
                                            <div v-if="task.type == 2" class="tracking-icon"
                                                v-bind:class="{ 'status-intransit': task.status == 1, 'status-delivered': task.status == 2 }">
                                                <i class="bi bi-check-all"></i>
                                            </div>
                                            <div class="tracking-date" v-if="task.status == 2 || task.status == 0"
                                                v-html="dateTask(task.updated_at)"></div>
                                            <div class="tracking-content">{{ task.name }}
                                                <span v-if="task.type == 1 && task.user_create != null">
                                                    Iniciado por: {{ task.user_create.name }}
                                                </span>
                                                <span v-else-if="task.type == 4 && task.status != 0 && task.user != null">
                                                </span>
                                                <span v-else-if="task.status == 0 && task.user != null">
                                                    Cancelado por: {{ task.user.name }}
                                                </span>
                                                <span v-else-if="task.user_approver != null">Atribuido a:
                                                    {{ task.user_approver.name }}
                                                </span>
                                                <span v-else-if="task.group_detail != null">Atribuido ao grupo:
                                                    {{ task.group_detail.A3_NOME }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12" v-if="historico.length > 0">
                                <div id="tracking-pre"></div>
                                <div id="tracking">
                                    <div class="text-center tracking-status-intransit">
                                        <p class="tracking-status text-tight">Histórico do processo</p>
                                    </div>
                                    <div class="tracking-list">
                                        <div class="tracking-item" v-for="log in historico">
                                            <div class="tracking-icon status-delivered">
                                                <i class="bi bi-check"></i>
                                            </div>
                                            <div class="tracking-date" v-html="dateTask(log.updated_at)"></div>
                                            <div class="tracking-content">
                                                {{ log.description }}
                                                <span v-if="log.occurrence != null">
                                                    MOTIVO: {{ log.occurrence }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!--Workflow-->

            <!--Form-->
            <div class="row" v-show="tabs.form">
                <div class="col-md-12" v-if="historico.length > 0">
                    <div class="card-body bg-warning shadow-sm border-bottom rounded-top table-responsive"
                        v-if="historico[historico.length - 1].occurrence != null">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Ocorrência</h3>
                                {{ historico[historico.length - 1].occurrence }}
                            </div>
                        </div>
                    </div>
                </div>
                <slot name="formBody"></slot>
            </div>
            <!--Form-->
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";

export default defineComponent({
    components: {
        AppLayout,
    },
    props: ['formFluxo', 'aprovacao', 'historico', 'form'],
    methods: {
        dateTask(dateIni) {
            var date = new Date(dateIni);
            var mes = '';
            (date.getMonth() == 0) ? mes = 'Janeiro' : '';
            (date.getMonth() == 1) ? mes = 'Fevereiro' : '';
            (date.getMonth() == 2) ? mes = 'Março' : '';
            (date.getMonth() == 3) ? mes = 'Abril' : '';
            (date.getMonth() == 4) ? mes = 'Maio' : '';
            (date.getMonth() == 5) ? mes = 'Junho' : '';
            (date.getMonth() == 6) ? mes = 'Julho' : '';
            (date.getMonth() == 7) ? mes = 'Julho' : '';
            (date.getMonth() == 8) ? mes = 'Julho' : '';
            (date.getMonth() == 9) ? mes = 'Julho' : '';
            (date.getMonth() == 10) ? mes = 'Julho' : '';
            (date.getMonth() == 11) ? mes = 'Julho' : '';

            return date.getDate() + ' ' + mes + ', ' + date.getFullYear() + '<span>' + date.getHours() + ':' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + '</span>';
        },
        tabsInfo(id) {
            if (id == 1) {
                this.tabs.form = true;
                this.tabs.workflow = false;
            } else if (id == 2) {
                this.tabs.form = false;
                this.tabs.workflow = true;
            }
        }
    },
    data() {
        return {
            criarNovo: false,
            tabs: {
                form: true,
                workflow: false
            }
        };
    },
});
</script>