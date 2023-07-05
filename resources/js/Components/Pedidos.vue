<template>
    <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
        <div class="row">
            <div class="col-md-6">
                <label for="pedido" class="form-label">Busca pedido</label>
                <div class="input-group">
                    <input type="text" v-model="pedido" class="form-control text-uppercase" id="pedido">
                    <span class="input-group-text border icone_imput_menor"><i class="bi bi-search"></i></span>
                </div>
            </div>
            <div class="col-md-2">
                <label for="dataInicio" class="form-label">Data inicio</label>
                <div class="input-group">
                    <input type="date" v-model="dataInicio" class="form-control campo_form_data text-uppercase"
                        id="dataInicio">
                    <span class="input-group-text border icone_imput_menor"><i class="bi bi-calendar3"></i></span>
                </div>
            </div>
            <div class="col-md-2">
                <label for="dataFim" class="form-label">Data fim</label>
                <div class="input-group">
                    <input type="date" v-model="dataFim" class="form-control campo_form_data text-uppercase"
                        id="dataFim">
                    <span class="input-group-text border icone_imput_menor"><i class="bi bi-calendar3"></i></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <button type="button" @click="search(1)" class="btn btn-success mt-30"><i
                            class="bi bi-search"></i></button>
                    <button type="button" @click="limpar()" style="margin-left: 10px;" class="btn btn-info mt-30"><i
                            class="bi bi-x-lg"></i></button>
                    <button type="button" @click="back" style="margin-left: 10px;" class="btn btn-danger mt-30">
                        <i class="bi bi-arrow-90deg-left"></i>
                    </button>
                </div>
            </div>
        </div>
        <div v-if="user_profile == false" class="row">
            <div class="col-md-2">
                <div>
                    <button class="btn btn-info dropdown-toggle mt-30" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        POR STATUS
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'LIBERADO', search(1)" href="#">LIBERADO</a>
                        </li>
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'ENCERRADO', search(1)" href="#">ENCERRADO</a>
                        </li>
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'PARCIAL', search(1)" href="#">PARCIAL</a>
                        </li>
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'ORÇAMENTO', search(1)" href="#">
                                ORÇAMENTO
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'BLOQUEADO PR', search(1)" href="#">
                                BLOQUEADO PR
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" @click="porStatus = 'BLOQUEADO OL', search(1)" href="#">
                                BLOQUEADO OL
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <div>
                    <button class="btn btn-info dropdown-toggle mt-30" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        RESULTADO POR PÁGINA {{  porPagina  }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" @click="porPagina = 10, search(1)" href="#">10</a></li>
                        <li><a class="dropdown-item" @click="porPagina = 50, search(1)" href="#">50</a></li>
                        <li><a class="dropdown-item" @click="porPagina = 100, search(1)" href="#">100</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-padrao">
                    <thead>
                        <tr>
                            <th scope="col">Pedido</th>
                            <th scope="col">Ordem</th>
                            <th scope="col" v-if="user_profile == false">Cliente</th>
                            <th scope="col">Cond Pag</th>
                            <th scope="col">Data Emi.</th>
                            <th scope="col">Data Saída</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Opções</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pedido in pedidos.data" :key="pedido.R_E_C_N_O_">
                            <th scope="row">{{  pedido.C5_NUM  }}</th>
                            <th>{{  pedido.C5_FSOCOMP  }}</th>
                            <th v-if="user_profile == false">
                                <div v-if="pedido.cliente != null">
                                    <Link :href="'/perfil/' + pedido.cliente.A1_COD">
                                    {{  pedido.cliente.A1_NOME  }}
                                    </Link>
                                </div>
                            </th>
                            <th>{{  pedido.CONDICAO  }}</th>
                            <td>{{  forData(pedido.C5_EMISSAO)  }}</td>
                            <td>
                                <div v-if="pedido.itens[0].C2_DATAPCP > 0">
                                    {{  forData(pedido.itens[0].C2_DATAPCP)  }}</div>
                            </td>
                            <td>
                                <span class="badge" v-bind:class="{
                                    'bg-blue': pedido.C5_FSSTBI.trim() == 'LIBERADO',
                                    'bg-success': pedido.C5_FSSTBI.trim() == 'ENCERRADO',
                                    'bg-gray': pedido.C5_FSSTBI.trim() == 'BLOQUEADO LO',
                                    'bg-gray-2': pedido.C5_FSSTBI.trim() == 'BLOQUEADO PR',
                                    'bg-warning text-dark': pedido.C5_FSSTBI.trim() == 'PARCIAL',
                                    'bg-danger': pedido.C5_FSSTBI.trim() == 'ABERTO',
                                    'bg-primary': pedido.C5_FSSTBI.trim() == 'ORCAMENTO'
                                }">
                                    {{  pedido.C5_FSSTBI  }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button @click="abrirModal(pedido.C5_NUM)" class="btn-icon btn-info">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <ModalDetalhes v-show="false" :id="'modal' + pedido.C5_NUM" :itens="pedido.itens" />
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">

                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li v-for="(link, index) in pedidos.links" class="page-item"
                            v-bind:class="{ active: link.active }">
                            <Link href="#" @click="search(link.label)" v-if="Number.isInteger(parseInt(link.label))"
                                class="page-link">
                            {{  link.label  }}
                            </Link>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>

</template>
<script>

import { defineComponent } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import ModalDetalhes from "./PedidoDetalhes";

export default defineComponent({
    render: function (createElement) {
        return createElement(
            this.dataInicio = '2022-08-08'
        )
    },
    components: {
        Link,
        ModalDetalhes
    },
    props: ['pedidos', 'user_profile', 'dataInicioCarbon', 'dataFimCarbon'],
    methods: {
        forData(data) {
            return data.substr(6, 2) + "." + data.substr(4, 2) + "." + data.substr(0, 4);
        },
        search(page) {
            this.$inertia.get(
                this.$inertia.page.url,
                {
                    pedido: this.pedido,
                    dataInicio: this.dataInicio,
                    dataFim: this.dataFim,
                    page: page,
                    porPagina: this.porPagina,
                    porStatus: this.porStatus,
                },
                { preserveState: true }
            );
        },
        limpar() {
            this.pedido = '';
            this.dataInicio = '';
            this.dataFim = '';
            this.porPagina = '';
            this.porStatus = '';
            this.search(1);
        },
        back() {
            window.history.back()
        },
        abrirModal(id) {
            eval('modal' + id).style.display = 'inline-table'
        }
    },
    data() {
        return {
            pedido: '',
            dataInicio: this.dataInicioCarbon,
            dataFim: this.dataFimCarbon,
            page: 1,
            porPagina: 10,
            porStatus: ''
        };
    }
})
</script>