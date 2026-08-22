<template>
  <header :class="['fixed top-0 left-0 right-0 z-50 transition-all duration-300',
    scrolled ? 'backdrop-blur-md bg-white/85 shadow-sm' : 'bg-transparent']">
    <div class="flex items-center justify-between h-16 px-6 mx-auto max-w-7xl">
      <RouterLink to="/" class="flex items-center gap-2">
        <span class="font-serif text-2xl font-semibold text-graphite">Recepies</span>
      </RouterLink>

      <nav class="items-center hidden gap-8 text-sm font-medium md:flex text-sage">
        <!-- <a href="/#categorias" class="transition hover:text-olive">Categorias</a> -->
        <a href="/#recipes" class="transition hover:text-olive">Recipes</a>
        <a href="#" class="transition hover:text-olive">About</a>
        <!-- <a href="#" class="transition hover:text-olive">Contato</a> -->
      </nav>

      <div class="flex items-center gap-3">
        <button class="p-2 transition rounded-full hover:bg-mint text-sage" @click="scrollToRecipes">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>

        <template v-if="!userStore.isLoggedIn">
          <button @click="userStore.openLogin()"
            class="hidden px-5 py-2 text-sm font-medium transition border rounded-full sm:inline-flex border-border-light hover:border-olive hover:text-olive">
            Sign In
          </button>
          <button @click="userStore.openLogin()"
            class="px-5 py-2 rounded-full bg-olive text-white text-sm font-medium hover:bg-olive-dark hover:scale-[1.03] active:scale-[0.98] transition-all">
            Create Recipe
          </button>
        </template>

        <template v-else>
          <div class="relative group">
            <button class="flex items-center gap-2 p-1 pr-3 transition rounded-full hover:bg-mint">
              <img :src="userStore.currentUser.avatarUrl" class="object-cover w-8 h-8 rounded-full" alt="avatar">
              <span class="hidden text-sm font-medium sm:inline text-graphite">{{ userStore.currentUser.name.split(' ')[0] }}</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="absolute right-0 invisible w-48 mt-2 overflow-hidden transition-all duration-200 bg-white border shadow-lg opacity-0 top-full rounded-2xl border-border-light group-hover:opacity-100 group-hover:visible">
              <RouterLink to="/my-recipes" class="flex items-center w-full gap-2 px-4 py-3 text-sm text-left transition hover:bg-mint text-graphite">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
                My Recipes
              </RouterLink>
              <RouterLink to="/recipes/new" class="flex items-center w-full gap-2 px-4 py-3 text-sm text-left transition hover:bg-mint text-graphite">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                New Recipe
              </RouterLink>
              <div class="h-px mx-2 bg-border-light"></div>
              <button @click="handleLogout" class="flex items-center w-full gap-2 px-4 py-3 text-sm text-left transition hover:bg-mint text-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Sign Out
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
  toast.show('See you next time! 👋')
  router.push('/')
}

function scrollToRecipes() {
  const el = document.getElementById('recipes')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
  else router.push('/#recipes')
}
</script>
