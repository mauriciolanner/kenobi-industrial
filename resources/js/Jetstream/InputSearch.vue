<template>
  <div class="input-group rounded">
    <input
      :value="modelValue"
      class="form-control rounded search-input"
      @input="$emit('update:modelValue', $event.target.value)"
      ref="input"
    />
    <span class="input-group-text border-0 botao icone-busca" id="search-addon">
      <i class="bi bi-search"></i>
    </span>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  props: ["modelValue"],

  emits: ["update:modelValue"],

  methods: {
    focus() {
      this.$refs.input.focus();
    },
  },
});
</script>

