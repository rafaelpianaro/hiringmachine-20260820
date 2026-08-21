<template>
  <section id="receitas" class="py-16 md:py-24 bg-mint">
    <div class="px-6 mx-auto max-w-7xl">
      <!-- Header com busca -->
      <div class="flex flex-col justify-between gap-4 mb-8 md:flex-row md:items-end">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-px bg-olive"></div>
            <span class="text-sm font-medium tracking-widest uppercase text-sage">Descubra</span>
          </div>
          <h2 class="font-serif text-3xl font-medium md:text-4xl text-graphite">Receitas em destaque</h2>
        </div>
        <div class="relative w-full md:w-80">
          <input v-model="recipeStore.searchQuery" type="text" placeholder="Buscar receita ou ingrediente..."
            class="w-full py-3 pl-10 pr-4 text-sm transition bg-white border rounded-full shadow-sm outline-none border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20">
          <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sage" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
      </div>

      <!-- Category filters -->
      <div class="flex flex-wrap gap-2 mb-8">
        <button @click="recipeStore.setCategory('Todas')"
          :class="['px-4 py-2 rounded-full text-sm font-medium transition', recipeStore.activeCategory === 'Todas' ? 'bg-graphite text-white' : 'bg-white text-sage hover:bg-graphite hover:text-white']">
          Todas
        </button>
        <button v-for="cat in apiCategories" :key="cat"
          @click="recipeStore.setCategory(cat)"
          :class="['px-4 py-2 rounded-full text-sm font-medium transition', recipeStore.activeCategory === cat ? 'bg-graphite text-white' : 'bg-white text-sage hover:bg-graphite hover:text-white']">
          {{ cat }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="recipeStore.loading" class="flex justify-center py-20">
        <div class="w-10 h-10 border-4 rounded-full border-olive/30 border-t-olive animate-spin"></div>
      </div>

      <!-- Grid -->
      <div v-else-if="recipeStore.filteredRecipes.length" class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <RecipeCard v-for="recipe in recipeStore.filteredRecipes" :key="recipe.id"
          :recipe="recipe"
          @click="openModal(recipe)"
          @delete="confirmDelete"
          @rate="handleRate" />
      </div>

      <!-- Empty -->
      <div v-else class="py-20 text-center">
        <div class="mb-4 text-6xl">🍲</div>
        <h3 class="mb-2 font-serif text-xl font-semibold text-graphite">Nenhuma receita encontrada</h3>
        <p class="text-sage">Tente ajustar sua busca ou categoria.</p>
      </div>
    </div>
  </section>

  <!-- Detail Modal -->
  <RecipeDetailModal v-if="selectedRecipe" v-model="showModal" :recipe="selectedRecipe" @deleted="onRecipeDeleted" />

  <!-- Delete Confirmation -->
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4" @click.self="showDeleteModal = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-sm p-8 text-center bg-white shadow-2xl rounded-3xl">
          <div class="flex items-center justify-center mx-auto mb-4 rounded-full w-14 h-14 bg-error/10 text-error">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </div>
          <h3 class="mb-2 font-serif text-xl font-semibold text-graphite">Excluir receita?</h3>
          <p class="mb-6 text-sm text-sage">Esta ação não pode ser desfeita. Tem certeza?</p>
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
import RecipeCard from './RecipeCard.vue'
import RecipeDetailModal from './RecipeDetailModal.vue'

const recipeStore = useRecipeStore()
const toast = useToastStore()

const apiCategories = computed(() => {
  const cats = new Set(recipeStore.recipes.map(r => r.category).filter(Boolean))
  return [...cats].sort()
})

const showModal = ref(false)
const selectedRecipeId = ref(null)
const selectedRecipe = computed(() => {
  if (!selectedRecipeId.value) return null
  return recipeStore.recipes.find(r => r.id === selectedRecipeId.value) || null
})
const showDeleteModal = ref(false)
const deleteTargetId = ref(null)

onMounted(() => {
  recipeStore.fetchRecipes()
})

function openModal(recipe) {
  selectedRecipeId.value = recipe.id
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

async function handleRate(recipeId, stars) {
  try {
    await recipeStore.rateRecipe(recipeId, stars)
    toast.show('Avaliação registrada! ⭐')
  } catch (e) {
    toast.show(e.message, 'error')
  }
}

function onRecipeDeleted() {
  // Deletion is already applied locally in the store; no refetch needed.
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
