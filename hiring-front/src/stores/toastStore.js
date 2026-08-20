import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const message = ref('')
  const type = ref('success')
  const visible = ref(false)
  let timeout = null

  function show(msg, t = 'success') {
    message.value = msg
    type.value = t
    visible.value = true
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => { visible.value = false }, 3000)
  }

  return { message, type, visible, show }
})
