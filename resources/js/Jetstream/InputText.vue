<template>
    <textarea
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :value="value" @input="$emit('input', $event.target.value)" ref="input"></textarea>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    props: ['modelValue', 'rows', 'value'],
    emits: ['update:modelValue'],
    methods: {
        focus() {
            this.$refs.input.focus()
        }
    }
})
</script>

