<template>
  <section class="relative pt-28 pb-16 md:pt-36 md:pb-24 overflow-hidden"
    style="background: radial-gradient(circle at top right, #EAF1E4 0%, #FDFDF9 60%);">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="order-2 lg:order-1">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-px bg-olive"></div>
            <span class="text-sm text-sage uppercase tracking-widest font-medium">+10.000 receitas</span>
          </div>
          <h1 class="font-serif text-5xl md:text-6xl font-medium leading-[1.1] tracking-tight mb-6 text-graphite">
            As melhores receitas<br>para suas <span class="text-olive">refeições</span>
          </h1>
          <p class="text-sage text-lg leading-relaxed mb-8 max-w-lg">
            Descubra, cozinhe e compartilhe pratos incríveis — avaliados por quem realmente cozinha.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <RouterLink v-if="userStore.isLoggedIn" to="/receitas/nova"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.03] active:scale-[0.98] transition-all shadow-sm">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
              Criar Receita
            </RouterLink>
            <button v-else @click="userStore.openLogin()"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.03] active:scale-[0.98] transition-all shadow-sm">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
              Começar agora
            </button>
            <a href="#receitas"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full border border-border-light text-graphite font-medium hover:border-olive hover:text-olive transition">
              Explorar receitas
            </a>
          </div>
        </div>

        <div class="order-1 lg:order-2 relative flex justify-center">
          <div class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none">
            <div class="grid grid-cols-8 gap-3">
              <div v-for="n in 40" :key="n" class="w-1.5 h-1.5 rounded-full bg-olive/40"></div>
            </div>
          </div>
          <div class="relative w-72 h-72 md:w-96 md:h-96 rounded-full overflow-hidden shadow-2xl animate-float">
            <img src="https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=800&h=800&fit=crop"
              class="w-full h-full object-cover" alt="Prato destaque">
          </div>
          <div class="absolute -bottom-4 -right-4 md:bottom-4 md:right-0 bg-white rounded-2xl px-4 py-3 shadow-lg flex items-center gap-2">
            <span class="text-gold text-lg">⭐</span>
            <div>
              <div class="text-sm font-semibold text-graphite">4.8</div>
              <div class="text-xs text-sage">média de avaliação</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore'
const userStore = useUserStore()
</script>
