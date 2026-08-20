<template>
  <section class="py-20 md:py-28 bg-olive text-white text-center">
    <div class="max-w-3xl mx-auto px-6">
      <h2 class="font-serif text-3xl md:text-5xl font-medium mb-4">Sua receita merece ser descoberta.</h2>
      <p class="text-white/90 text-lg mb-8 leading-relaxed">Crie sua conta gratuita e compartilhe seus pratos com milhares de pessoas.</p>
      <button @click="handleCta" class="px-8 py-4 rounded-full bg-white text-graphite font-medium hover:scale-[1.03] active:scale-[0.98] transition-all shadow-lg">
        {{ userStore.isLoggedIn ? 'Criar nova receita' : 'Criar conta grátis' }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const router = useRouter()

function handleCta() {
  if (userStore.isLoggedIn) {
    router.push('/receitas/nova')
  } else {
    userStore.openLogin()
  }
}
</script>
