<template>
    <div class="modal-mask" :id="id">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-header">
                    <slot name="header"></slot>
                </div>

                <div class="modal-body">
                    <slot name="body"></slot>
                </div>

                <div class="modal-footer">
                    <slot name="footer">
                        <button class="btn btn-info" @click="fechaModal(id)">
                            Fechar
                        </button>
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
        fechaModal(id) {
            eval(id).style.display = 'none'
        }
    },
});
</script>