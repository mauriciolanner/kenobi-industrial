<template>
    <div class="card-body mt-3 shadow-sm bg-white border-bottom rounded-top">
        <div class="row">
            <div class="col-12">
                <h3><i class="bi bi-box-seam"></i> Produtos comprado:</h3>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-padrao">
                    <thead>
                        <tr>
                            <th scope="col">Produto</th>
                            <th scope="col">Grupo</th>
                            <th scope="col">Preço</th>
                            <th scope="col">Ultima compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="produto in produtosCompra">
                            <th scope="row">{{  produto.B1_DESC  }}</th>
                            <th>{{  produto.D2_GRUPO  }}</th>
                            <th>{{  (parseFloat(produto.D2_PRCVEN)).toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            })
 }}
                            </th>
                            <th>{{  produto.DATA_EMISSAO  }}</th>
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
    props: ['produtosCompra'],
    methods: {
    },
    data() {
        return {

        };
    }
})
</script>