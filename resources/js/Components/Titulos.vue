<template>
    <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
        <div class="row">
            <div class="col-12">
                <h3><i class="bi bi-cash-coin"></i> Títulos:</h3>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-padrao">
                    <thead>
                        <tr>
                            <th scope="col">Num Titulo</th>
                            <th>Parc</th>
                            <th>Emissão</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="titulos in titulos.data">
                            <th scope="row">{{  titulos.E1_NUM  }}</th>
                            <th>{{  titulos.E1_PARCELA  }}</th>
                            <th>{{  forData(titulos.E1_EMISSAO)  }}</th>
                            <th>
                                {{  forData(titulos.E1_VENCTO)  }}
                            </th>
                            <th>{{  parseFloat(titulos.E1_VALOR).toLocaleString('pt-br', {
                                style: 'currency', currency:
                                    'BRL'
                            })
 }}</th>
                            <td class="text-end">
                                <span v-if="titulos.E1_BAIXA.trim() == '' && verificaBoleto(titulos.E1_VENCTO) == false"
                                    class="badge bg-success">
                                    ABERTO
                                </span>
                                <span v-if="verificaBoleto(titulos.E1_VENCTO)" class="badge bg-danger">
                                    VENCIDO
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <nav aria-label="Page Titles">
                    <ul class="pagination justify-content-end">
                        <li v-for="(link, index) in titulos.links" class="page-item"
                            v-bind:class="{ active: link.active }">
                            <Link :href="link.url" v-if="Number.isInteger(parseInt(link.label))" class="page-link">
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
    props: ['titulos'],
    methods: {
        forData(data) {
            return data.substr(6, 2) + "." + data.substr(4, 2) + "." + data.substr(0, 4);
        },
        verificaBoleto(data) {
            var retorno = false;
            var compara = new Date(data.substr(0, 4) + "-" + data.substr(4, 2) + "-" + data.substr(6, 2))
            var hoje = new Date();

            if (compara < hoje)
                retorno = true

            return retorno;
        },
        search(page) {
            // this.$inertia.get(
            //     this.$inertia.page.url,
            //     {
            //         pedido: this.pedido,
            //         dataInicio: this.dataInicio,
            //         dataFim: this.dataFim,
            //         page: page,
            //         porPagina: this.porPagina,
            //         porStatus: this.porStatus,
            //     },
            //     { preserveState: true }
            // );
        },
    },
    data() {
        return {

        };
    }
})
</script>