<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="userStore.showLoginModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @click.self="userStore.closeLogin()">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl">
          <button @click="userStore.closeLogin()" class="absolute top-4 right-4 p-2 rounded-full hover:bg-mint transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
          <h2 class="font-serif text-2xl font-semibold mb-2 text-graphite">Entrar na cozinha</h2>
          <p class="text-sage text-sm mb-6">Faça login para avaliar, criar e compartilhar suas receitas favoritas.</p>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Nome</label>
              <input v-model="form.name" type="text" placeholder="Seu nome"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">E-mail</label>
              <input v-model="form.email" type="email" placeholder="seu@email.com"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition" required>
            </div>
            <button type="submit" class="w-full py-3 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.02] active:scale-[0.98] transition-all">
              Entrar
            </button>
          </form>
          <p class="text-center text-xs text-sage mt-4">Simulação de login — sem senha necessária.</p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { reactive } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useToastStore } from '@/stores/toastStore'

const userStore = useUserStore()
const toast = useToastStore()
const form = reactive({ name: '', email: '' })

function handleSubmit() {
  if (!form.name.trim() || !form.email.trim()) return
  userStore.login(form.name, form.email)
  toast.show('Bem-vindo(a) à cozinha! 👨‍🍳')
  form.name = ''
  form.email = ''
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
