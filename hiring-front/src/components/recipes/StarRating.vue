<template>
  <div class="flex items-center gap-1">
    <button v-for="star in max" :key="star"
      @click="handleClick(star)"
      :disabled="readonly"
      :class="['transition-colors duration-150', readonly ? 'cursor-default' : 'cursor-pointer hover:scale-110']">
      <svg width="20" height="20" viewBox="0 0 24 24"
        :fill="star <= filledStars ? '#F2B705' : 'none'"
        :stroke="star <= filledStars ? '#F2B705' : '#D8DED2'"
        stroke-width="1.5">
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
      </svg>
    </button>
    <span v-if="showCount" class="text-xs text-sage ml-1.5">({{ count }})</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Number, default: 0 },
  average: { type: Number, default: 0 },
  readonly: { type: Boolean, default: false },
  max: { type: Number, default: 3 },
  showCount: { type: Boolean, default: false },
  count: { type: Number, default: 0 }
})

const emit = defineEmits(['update:modelValue', 'rate'])

const filledStars = computed(() => {
  return props.readonly ? Math.round(props.average) : props.modelValue
})

function handleClick(star) {
  if (props.readonly) return
  emit('update:modelValue', star)
  emit('rate', star)
}
</script>
