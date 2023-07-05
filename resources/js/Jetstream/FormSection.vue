<template>
  <div class="row">
    <div class="col-md-4">

    </div>

    <div class="col-md-12">

      <form @submit.prevent="$emit('submitted')">
        <div class="card bg-white shadow-sm">
          <div class="card-body">

            <jet-section-title>
              <template #title>
                <slot name="title"></slot>
              </template>
              <template #description>
                <span class="small">
                  <slot name="description"></slot>
                </span>
              </template>
            </jet-section-title>

            <slot name="form"></slot>
          </div>

          <div class="card-footer bg-white d-flex justify-content-end" v-if="hasActions">
            <slot name="actions"></slot>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import JetSectionTitle from './SectionTitle.vue'

export default defineComponent({
  components: {
    JetSectionTitle,
  },

  emits: ['submitted'],

  computed: {
    hasActions() {
      return !!this.$slots.actions
    }
  }
})
</script>
