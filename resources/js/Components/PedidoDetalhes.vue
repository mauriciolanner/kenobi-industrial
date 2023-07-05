<template>
    <div class="modal-mask" :id="id" @click="fechaModal(id)">
        <div class="modal-wrapper ">
            <div class="modal-container">
                <div class="modal-body text-start">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Descrição do item</th>
                                <th scope="col">UM</th>
                                <th scope="col">Qtd</th>
                                <th scope="col">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in itens">
                                <th scope="row">{{ item.C6_DESCRI }}</th>
                                <td>{{ item.C6_UM }}</td>
                                <td>{{ parseFloat(item.C6_QTDVEN) }}</td>
                                <td>{{ parseFloat(item.C6_PRCVEN).toLocaleString('pt-br', {
                                        style: 'currency', currency:
                                            'BRL'
                                    })
                                }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer text-center">
                    <slot name="footer">
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: ['id', 'itens'],
    data() {
        return {

        };
    },
    methods: {
        forData(data) {
            //return data.substr(6, 2) + "." + data.substr(4, 2) + "." + data.substr(0, 4);
            return data
        },
        fechaModal(id) {
            eval(id).style.display = 'none'
        }
    },
});
</script>
