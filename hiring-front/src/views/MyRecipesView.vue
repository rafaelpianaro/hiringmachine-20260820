<template>
  <div class="pt-24 pb-16 min-h-screen bg-off-white">
    <div class="max-w-7xl mx-auto px-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-px bg-olive"></div>
            <span class="text-sm text-sage uppercase tracking-widest font-medium">Sua cozinha</span>
          </div>
          <h2 class="font-serif text-3xl md:text-4xl font-medium text-graphite">Minhas Receitas</h2>
          <p class="text-sage mt-2">Gerencie suas criações e acompanhe as avaliações.</p>
        </div>
        <div class="flex gap-3">
          <div class="relative w-56">
            <input v-model="searchQuery" type="text" placeholder="Buscar nas minhas receitas..."
              class="w-full pl-9 pr-4 py-2.5 rounded-full border border-border-light bg-white shadow-sm focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition text-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-sage" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </div>
          <RouterLink to="/receitas/nova"
            class="px-5 py-2.5 rounded-full bg-olive text-white text-sm font-medium hover:bg-olive-dark hover:scale-[1.03] active:scale-[0.98] transition-all flex items-center gap-2 shadow-sm whitespace-nowrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Nova Receita
          </RouterLink>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-3 gap-4 mb-8" v-if="filteredMyRecipes.length > 0">
        <div class="bg-white rounded-2xl p-4 border border-border-light">
          <div class="text-2xl font-serif font-bold text-graphite">{{ filteredMyRecipes.length }}</div>
          <div class="text-xs text-sage uppercase tracking-wide">Receitas</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-border-light">
          <div class="text-2xl font-serif font-bold text-graphite">{{ totalRatings }}</div>
          <div class="text-xs text-sage uppercase tracking-wide">Avaliações</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-border-light">
          <div class="text-2xl font-serif font-bold text-graphite">{{ avgRating }}</div>
          <div class="text-xs text-sage uppercase tracking-wide">Média</div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="recipeStore.loading" class="flex justify-center py-20">
        <div class="w-10 h-10 border-4 border-olive/30 border-t-olive rounded-full animate-spin"></div>
      </div>

      <!-- Grid -->
      <div v-else-if="filteredMyRecipes.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="recipe in filteredMyRecipes" :key="recipe.id"
          class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
          @click="openModal(recipe)">
          <div class="relative aspect-[4/3] overflow-hidden">
            <img :src="recipe.coverImage" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" :alt="recipe.title">
            <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-xs font-medium text-graphite">{{ recipe.category }}</span>
            <div class="absolute top-4 right-4 flex gap-2" @click.stop>
              <button @click="$router.push(`/receitas/${recipe.id}/editar`)"
                class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center hover:bg-olive hover:text-white transition shadow-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
              </button>
              <button @click="confirmDelete(recipe.id)"
                class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center hover:bg-error hover:text-white transition shadow-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </div>
          </div>
          <div class="p-6">
            <h3 class="font-serif text-xl font-semibold mb-2 text-graphite group-hover:text-olive transition">{{ recipe.title }}</h3>
            <div class="flex items-center gap-3 text-xs text-sage uppercase tracking-wide mb-3">
              <span class="flex items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ recipe.prepTimeMinutes }} min
              </span>
              <span class="flex items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M10 21v-6h4v6"/></svg>
                {{ recipe.servings }} porções
              </span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1">
                <svg v-for="s in 3" :key="s" width="16" height="16" viewBox="0 0 24 24"
                  :fill="s <= Math.round(getAverage(recipe.ratings)) ? '#F2B705' : 'none'"
                  :stroke="s <= Math.round(getAverage(recipe.ratings)) ? '#F2B705' : '#D8DED2'"
                  stroke-width="1.5">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <span class="text-xs text-sage ml-1">({{ recipe.ratings.length }})</span>
              </div>
              <span class="text-xs text-sage">{{ recipe.createdAt }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-24">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-mint flex items-center justify-center text-4xl">🥘</div>
        <h3 class="font-serif text-2xl font-semibold mb-3 text-graphite">Sua cozinha está vazia</h3>
        <p class="text-sage mb-8 max-w-md mx-auto">Você ainda não publicou nenhuma receita. Que tal compartilhar sua primeira criação?</p>
        <RouterLink to="/receitas/nova"
          class="px-8 py-3 rounded-full bg-olive text-white font-medium hover:bg-olive-dark transition inline-flex items-center gap-2">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
          Criar primeira receita
        </RouterLink>
      </div>
    </div>
  </div>

  <!-- Detail Modal -->
  <RecipeDetailModal v-model="showModal" :recipe="selectedRecipe" @deleted="onRecipeDeleted" />

  <!-- Delete Confirmation -->
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4" @click.self="showDeleteModal = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl text-center">
          <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-error/10 flex items-center justify-center text-error">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </div>
          <h3 class="font-serif text-xl font-semibold mb-2 text-graphite">Excluir receita?</h3>
          <p class="text-sage text-sm mb-6">Esta ação não pode ser desfeita. Tem certeza?</p>
          <div class="flex gap-3">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 rounded-full border border-border-light text-sm font-medium hover:bg-mint transition">Cancelar</button>
            <button @click="executeDelete" class="flex-1 py-2.5 rounded-full bg-error text-white text-sm font-medium hover:bg-[#c9302c] transition">Excluir</button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRecipeStore } from '@/stores/recipeStore'
import { useToastStore } from '@/stores/toastStore'
import RecipeDetailModal from '@/components/recipes/RecipeDetailModal.vue'

const recipeStore = useRecipeStore()
const toast = useToastStore()

const searchQuery = ref('')
const showModal = ref(false)
const selectedRecipe = ref(null)
const showDeleteModal = ref(false)
const deleteTargetId = ref(null)

onMounted(() => {
  recipeStore.fetchRecipes()
})

const filteredMyRecipes = computed(() => {
  let result = recipeStore.myRecipes
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(r => r.title.toLowerCase().includes(q))
  }
  return result
})

const totalRatings = computed(() => {
  return recipeStore.myRecipes.reduce((sum, r) => sum + r.ratings.length, 0)
})

const avgRating = computed(() => {
  const all = recipeStore.myRecipes.flatMap(r => r.ratings)
  if (!all.length) return '0.0'
  return (all.reduce((a, b) => a + b.stars, 0) / all.length).toFixed(1)
})

function getAverage(ratings) {
  if (!ratings.length) return 0
  return ratings.reduce((a, b) => a + b.stars, 0) / ratings.length
}

function openModal(recipe) {
  selectedRecipe.value = recipe
  showModal.value = true
}

function confirmDelete(id) {
  deleteTargetId.value = id
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTargetId.value) return
  try {
    await recipeStore.deleteRecipe(deleteTargetId.value)
    toast.show('Receita removida.')
  } catch (e) {
    toast.show(e.message, 'error')
  }
  showDeleteModal.value = false
  deleteTargetId.value = null
}

function onRecipeDeleted() {
  recipeStore.fetchRecipes()
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
