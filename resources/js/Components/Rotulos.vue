<template>
    <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
        <div class="row">
            <div class="col-12">
                <h3><i class="bi bi-box-seam"></i> Rotulos:</h3>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-padrao">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Rotulo</th>
                            <th scope="col">Lote</th>
                            <th scope="col">Validade</th>
                            <th scope="col">Saldo.Lote</th>
                            <th scope="col">Saldo.Real</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rolulo in rolulos">
                            <th scope="row">{{  rolulo.ID  }}</th>
                            <th>{{  rolulo.Rotulo  }}</th>
                            <th>{{  rolulo.Lote  }}</th>
                            <th>{{  dateTask(rolulo.DataValidade)  }}</th>
                            <td>{{  rolulo.SaldoPorLote  }}</td>
                            <td>{{  rolulo.SaldoReal  }}</td>
                            <td>
                                <span class="badge" v-bind:class="{
                                    'bg-success': rolulo.Status.trim() == 'NO PRAZO',
                                    'bg-danger': rolulo.Status.trim() == 'VENCIDO',
                                }">
                                    {{  rolulo.Status  }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>
<script>

import { defineComponent } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    render: function (createElement) {
        return createElement(

        )
    },
    components: {
        Link,
    },
    props: ['rolulos'],
    methods: {
        dateTask(dateIni) {
            var date = new Date(dateIni);
            var mes = '';
            (date.getMonth() == 0) ? mes = 'Jan' : '';
            (date.getMonth() == 1) ? mes = 'Fev' : '';
            (date.getMonth() == 2) ? mes = 'Mar' : '';
            (date.getMonth() == 3) ? mes = 'Abr' : '';
            (date.getMonth() == 4) ? mes = 'Mai' : '';
            (date.getMonth() == 5) ? mes = 'Jun' : '';
            (date.getMonth() == 6) ? mes = 'Jul' : '';
            (date.getMonth() == 7) ? mes = 'Ago' : '';
            (date.getMonth() == 8) ? mes = 'Set' : '';
            (date.getMonth() == 9) ? mes = 'Out' : '';
            (date.getMonth() == 10) ? mes = 'Nov' : '';
            (date.getMonth() == 11) ? mes = 'Dez' : '';

            var retorno = date.getDate() + ' ' + mes +
                ', ' + date.getFullYear();

            return retorno;
        },
    },
    data() {
        return {

        };
    }
})
</script>