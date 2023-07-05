<template v-on:fechar="fecharModal">
  <div class="modal-mask" :id="id">
    <div class="modal-wrapper">
      <div class="modal-container">
        <div class="modal-header" :class="headerClass">
          <slot class="text-center" name="header"></slot>
        </div>

        <div class="modal-body">
          <slot name="body"></slot>
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
  props: ['headerClass', 'id'],
  data() {
    return {
      modalShowData: this.modalShow,
    };
  },
  methods: {},
});
</script>
