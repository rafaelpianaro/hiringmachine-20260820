<template>
  <header :class="['fixed top-0 left-0 right-0 z-50 transition-all duration-300',
    scrolled ? 'backdrop-blur-md bg-white/85 shadow-sm' : 'bg-transparent']">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
      <RouterLink to="/" class="flex items-center gap-2">
        <span class="font-serif text-2xl font-semibold text-graphite">Recepies</span>
      </RouterLink>

      <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-sage">
        <a href="/#categorias" class="hover:text-olive transition">Categorias</a>
        <a href="/#receitas" class="hover:text-olive transition">Receitas</a>
        <a href="#" class="hover:text-olive transition">Sobre</a>
        <a href="#" class="hover:text-olive transition">Contato</a>
      </nav>

      <div class="flex items-center gap-3">
        <button class="p-2 rounded-full hover:bg-mint transition text-sage" @click="scrollToRecipes">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>

        <template v-if="!userStore.isLoggedIn">
          <button @click="userStore.openLogin()"
            class="hidden sm:inline-flex px-5 py-2 rounded-full border border-border-light text-sm font-medium hover:border-olive hover:text-olive transition">
            Entrar
          </button>
          <button @click="userStore.openLogin()"
            class="px-5 py-2 rounded-full bg-olive text-white text-sm font-medium hover:bg-olive-dark hover:scale-[1.03] active:scale-[0.98] transition-all">
            Criar Receita
          </button>
        </template>

        <template v-else>
          <div class="relative group">
            <button class="flex items-center gap-2 p-1 pr-3 rounded-full hover:bg-mint transition">
              <img :src="userStore.currentUser.avatarUrl" class="w-8 h-8 rounded-full object-cover" alt="avatar">
              <span class="text-sm font-medium hidden sm:inline text-graphite">{{ userStore.currentUser.name.split(' ')[0] }}</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-lg border border-border-light opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 overflow-hidden">
              <RouterLink to="/minhas-receitas" class="w-full text-left px-4 py-3 text-sm hover:bg-mint transition flex items-center gap-2 text-graphite">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
                Minhas Receitas
              </RouterLink>
              <RouterLink to="/receitas/nova" class="w-full text-left px-4 py-3 text-sm hover:bg-mint transition flex items-center gap-2 text-graphite">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Nova Receita
              </RouterLink>
              <div class="h-px bg-border-light mx-2"></div>
              <button @click="handleLogout" class="w-full text-left px-4 py-3 text-sm hover:bg-mint transition text-error flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Sair
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useToastStore } from '@/stores/toastStore'

const userStore = useUserStore()
const toast = useToastStore()
const router = useRouter()
const scrolled = ref(false)

onMounted(() => {
  window.addEventListener('scroll', () => { scrolled.value = window.scrollY > 20 })
})

function handleLogout() {
  userStore.logout()
  toast.show('Até a próxima receita! 👋')
  router.push('/')
}

function scrollToRecipes() {
  const el = document.getElementById('receitas')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
  else router.push('/#receitas')
}
</script>
