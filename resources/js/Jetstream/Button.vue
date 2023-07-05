<template>
  <button :type="type" class="btn text-uppercase btn-info" style="margin-left: 5px">
    <slot></slot>
  </button>
</template>

<script>
  import { defineComponent } from 'vue'

  export default defineComponent({
    props: {
      type: {
        type: String,
        default: 'submit',
      },
    }
  })
</script>
