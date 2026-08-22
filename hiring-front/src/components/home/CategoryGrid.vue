<template>
  <section id="categorias" class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-6">
      <div class="flex items-end justify-between mb-10">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-px bg-olive"></div>
            <span class="text-sm text-sage uppercase tracking-widest font-medium">Browse</span>
          </div>
          <h2 class="font-serif text-3xl md:text-4xl font-medium text-graphite">Popular Categories</h2>
        </div>
        <div class="flex gap-2">
          <button class="w-10 h-10 rounded-full border border-border-light flex items-center justify-center hover:border-olive hover:text-olive transition text-sage">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button class="w-10 h-10 rounded-full border border-border-light flex items-center justify-center hover:border-olive hover:text-olive transition text-sage">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div v-for="cat in recipeStore.categories" :key="cat.name"
          @click="selectCategory(cat.name)"
          class="bg-white rounded-3xl p-5 shadow-sm hover:shadow-md transition-all cursor-pointer group border border-transparent hover:border-border-light">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-semibold text-graphite group-hover:text-olive transition">{{ cat.name }}</h3>
              <p class="text-xs text-sage mt-0.5">{{ cat.count }} recipes</p>
            </div>
            <div class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0">
              <img :src="cat.image" class="w-full h-full object-cover" :alt="cat.name">
            </div>
          </div>
          <button class="px-4 py-1.5 rounded-full border border-border-light text-xs font-medium text-sage hover:border-olive hover:text-olive transition">
            View all
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRecipeStore } from '@/stores/recipeStore'

const recipeStore = useRecipeStore()

function selectCategory(name) {
  recipeStore.setCategory(name)
  const el = document.getElementById('recipes')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}
</script>
