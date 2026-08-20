<template>
  <div v-if="loading" class="pt-32 flex justify-center min-h-screen">
    <div class="w-10 h-10 border-4 border-olive/30 border-t-olive rounded-full animate-spin"></div>
  </div>
  <div v-else-if="recipe" class="pt-20 pb-16">
    <div class="max-w-5xl mx-auto px-6">
      <button @click="$router.push('/')" class="mb-6 flex items-center gap-2 text-sm text-sage hover:text-olive transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        Voltar para receitas
      </button>
      <div class="relative rounded-[2rem] overflow-hidden mb-8 aspect-[21/9]">
        <img :src="recipe.coverImage" class="w-full h-full object-cover" :alt="recipe.title">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-6 left-6 md:bottom-10 md:left-10 text-white">
          <span class="inline-block bg-white/20 backdrop-blur-sm rounded-full px-4 py-1 text-xs font-medium mb-3">{{ recipe.category }}</span>
          <h1 class="font-serif text-3xl md:text-5xl font-medium leading-tight">{{ recipe.title }}</h1>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-6 mb-10 pb-8 border-b border-border-light">
        <div class="flex items-center gap-3">
          <img :src="author.avatarUrl" class="w-10 h-10 rounded-full object-cover" alt="">
          <div>
            <div class="text-sm font-medium text-graphite">{{ author.name }}</div>
            <div class="text-xs text-sage">{{ recipe.createdAt }}</div>
          </div>
        </div>
        <div class="h-8 w-px bg-border-light hidden sm:block"></div>
        <div class="flex items-center gap-4 text-sm text-sage">
          <span class="flex items-center gap-1.5"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>{{ recipe.prepTimeMinutes }} min</span>
          <span class="flex items-center gap-1.5"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M10 21v-6h4v6"/></svg>{{ recipe.servings }} porções</span>
          <span class="flex items-center gap-1.5"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>{{ recipe.difficulty }}</span>
        </div>
        <div class="ml-auto flex gap-2" v-if="isOwner">
          <button @click="$router.push(`/receitas/${recipe.id}/editar`)" class="px-4 py-2 rounded-full border border-border-light text-sm font-medium hover:border-olive hover:text-olive transition flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>Editar
          </button>
          <button @click="confirmDelete" class="px-4 py-2 rounded-full border border-border-light text-sm font-medium text-error hover:bg-error hover:text-white hover:border-error transition flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Excluir
          </button>
        </div>
      </div>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="md:col-span-1">
          <h3 class="font-serif text-xl font-semibold mb-4 text-graphite">Ingredientes</h3>
          <ul class="space-y-3">
            <li v-for="(ing, i) in recipe.ingredients" :key="i" class="flex items-start gap-3 group">
              <div class="w-5 h-5 rounded border-2 border-border-light flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:border-olive transition cursor-pointer" @click="toggleCheck(i)" :class="checkedIngredients.has(i) ? 'bg-olive border-olive' : ''">
                <svg v-if="checkedIngredients.has(i)" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <span :class="['text-sm leading-relaxed transition', checkedIngredients.has(i) ? 'text-sage line-through' : 'text-graphite']">{{ ing }}</span>
            </li>
          </ul>
        </div>
        <div class="md:col-span-2">
          <h3 class="font-serif text-xl font-semibold mb-4 text-graphite">Modo de preparo</h3>
          <div class="space-y-6">
            <div v-for="(step, i) in recipe.steps" :key="i" class="flex gap-4">
              <div class="w-8 h-8 rounded-full bg-mint text-olive flex items-center justify-center text-sm font-bold flex-shrink-0">{{ i + 1 }}</div>
              <p class="text-sage leading-relaxed pt-1">{{ step }}</p>
            </div>
          </div>
          <div class="mt-12 pt-8 border-t border-border-light">
            <h3 class="font-serif text-xl font-semibold mb-4 text-graphite">Avaliações</h3>
            <div class="bg-off-white rounded-2xl p-6 border border-border-light">
              <div class="flex items-center gap-4 mb-4">
                <div class="text-4xl font-serif font-bold text-graphite">{{ averageRating }}</div>
                <div>
                  <StarRating :average="Number(averageRating)" :readonly="true" />
                  <div class="text-xs text-sage mt-1">{{ recipe.ratings.length }} avaliações</div>
                </div>
              </div>
              <div v-if="userStore.isLoggedIn && canRate" class="border-t border-border-light pt-4">
                <p class="text-sm text-sage mb-3">Avalie esta receita (1 a 3 estrelas):</p>
                <StarRating v-model="userRating" :readonly="false" @rate="handleRate" />
              </div>
              <div v-else-if="!userStore.isLoggedIn" class="border-t border-border-light pt-4">
                <p class="text-sm text-sage"><button @click="userStore.openLogin()" class="text-olive font-medium hover:underline">Entre</button> para avaliar esta receita.</p>
              </div>
              <div v-else class="border-t border-border-light pt-4">
                <p class="text-sm text-sage">Você não pode avaliar sua própria receita.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" @click.self="showDeleteModal = false">
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
import { useRoute, useRouter } from 'vue-router'
import { useRecipeStore } from '@/stores/recipeStore'
import { useUserStore } from '@/stores/userStore'
import { useToastStore } from '@/stores/toastStore'
import StarRating from '@/components/recipes/StarRating.vue'

const route = useRoute()
const router = useRouter()
const recipeStore = useRecipeStore()
const userStore = useUserStore()
const toast = useToastStore()

const recipe = ref(null)
const loading = ref(true)
const showDeleteModal = ref(false)
const checkedIngredients = ref(new Set())
const userRating = ref(0)

const users = {
  u1: { name: 'Ana Cozinheira', avatarUrl: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face' },
  u2: { name: 'Marco Sabor', avatarUrl: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face' },
  u3: { name: 'Julia Tempero', avatarUrl: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face' }
}

const author = computed(() => users[recipe.value?.authorId] || { name: 'Desconhecido', avatarUrl: '' })
const averageRating = computed(() => {
  if (!recipe.value?.ratings.length) return '0.0'
  return (recipe.value.ratings.reduce((a, b) => a + b.stars, 0) / recipe.value.ratings.length).toFixed(1)
})
const isOwner = computed(() => userStore.currentUser && recipe.value?.authorId === userStore.currentUser.id)
const canRate = computed(() => userStore.currentUser && !isOwner.value)

onMounted(async () => {
  try {
    recipe.value = await recipeStore.getRecipe(route.params.id)
    if (userStore.currentUser && recipe.value) {
      const r = recipe.value.ratings.find(rat => rat.userId === userStore.currentUser.id)
      userRating.value = r ? r.stars : 0
    }
  } catch (e) {
    toast.show(e.message, 'error')
    router.push('/')
  } finally {
    loading.value = false
  }
})

function toggleCheck(i) {
  if (checkedIngredients.value.has(i)) checkedIngredients.value.delete(i)
  else checkedIngredients.value.add(i)
}

async function handleRate(stars) {
  try {
    await recipeStore.rateRecipe(recipe.value.id, stars)
    recipe.value = await recipeStore.getRecipe(route.params.id)
    toast.show('Avaliação registrada! ⭐')
  } catch (e) {
    toast.show(e.message, 'error')
  }
}

function confirmDelete() { showDeleteModal.value = true }

async function executeDelete() {
  try {
    await recipeStore.deleteRecipe(recipe.value.id)
    toast.show('Receita removida.')
    router.push('/')
  } catch (e) {
    toast.show(e.message, 'error')
  }
  showDeleteModal.value = false
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
