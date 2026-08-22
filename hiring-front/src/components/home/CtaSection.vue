<template>
  <section class="py-20 md:py-28 bg-olive text-white text-center">
    <div class="max-w-3xl mx-auto px-6">
      <h2 class="font-serif text-3xl md:text-5xl font-medium mb-4">Your recipe deserves to be discovered.</h2>
      <p class="text-white/90 text-lg mb-8 leading-relaxed">Create your free account and share your dishes with thousands of people.</p>
      <button @click="handleCta" class="px-8 py-4 rounded-full bg-white text-graphite font-medium hover:scale-[1.03] active:scale-[0.98] transition-all shadow-lg">
        {{ userStore.isLoggedIn ? 'Create new recipe' : 'Create free account' }}
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
    router.push('/recipes/new')
  } else {
    userStore.openLogin()
  }
}
</script>
