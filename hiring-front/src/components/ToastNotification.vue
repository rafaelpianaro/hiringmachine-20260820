<template>
  <Teleport to="body">
    <Transition name="slide-up">
      <div v-if="toast.visible"
        :class="['fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] px-6 py-3 rounded-full shadow-lg text-sm font-medium flex items-center gap-2 transition-all whitespace-nowrap',
          toast.type === 'error' ? 'bg-error text-white' : 'bg-graphite text-white']">
        <span v-if="toast.type === 'success'">✓</span>
        <span v-else>!</span>
        {{ toast.message }}
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useToastStore } from '@/stores/toastStore'
const toast = useToastStore()
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}
.slide-up-enter-from, .slide-up-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>
