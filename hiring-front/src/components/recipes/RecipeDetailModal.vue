<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-[100] flex items-start justify-center p-4 md:p-8 overflow-y-auto"
        @click.self="close">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close"></div>

        <div class="relative bg-off-white rounded-[2rem] w-full max-w-4xl my-4 md:my-8 shadow-2xl overflow-hidden">
          <!-- Close button -->
          <button @click="close" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center hover:bg-white transition shadow-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>

          <!-- Image Header -->
          <div class="relative aspect-[21/9] overflow-hidden">
            <img :src="recipe.coverImage" class="w-full h-full object-cover" :alt="recipe.title">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            <div class="absolute bottom-6 left-6 md:bottom-8 md:left-8 text-white">
              <span class="inline-block bg-white/20 backdrop-blur-sm rounded-full px-4 py-1 text-xs font-medium mb-3">{{ recipe.category }}</span>
              <h2 class="font-serif text-2xl md:text-4xl font-medium leading-tight">{{ recipe.title }}</h2>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6 md:p-10">
            <!-- Meta -->
            <div class="flex flex-wrap items-center gap-4 mb-8 pb-6 border-b border-border-light">
              <div class="flex items-center gap-3">
                <img :src="author.avatarUrl" class="w-10 h-10 rounded-full object-cover" alt="">
                <div>
                  <div class="text-sm font-medium text-graphite">{{ author.name }}</div>
                  <div class="text-xs text-sage">{{ recipe.createdAt }}</div>
                </div>
              </div>
              <div class="h-8 w-px bg-border-light hidden sm:block"></div>
              <div class="flex items-center gap-4 text-sm text-sage">
                <span class="flex items-center gap-1.5">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  {{ recipe.prepTimeMinutes }} min
                </span>
                <span class="flex items-center gap-1.5">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M10 21v-6h4v6"/></svg>
                  {{ recipe.servings }} porções
                </span>
                <span class="flex items-center gap-1.5">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                  {{ recipe.difficulty }}
                </span>
              </div>
              <div class="ml-auto flex gap-2" v-if="isOwner">
                <button @click="goToEdit"
                  class="px-4 py-2 rounded-full border border-border-light text-sm font-medium hover:border-olive hover:text-olive transition flex items-center gap-2 bg-white">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                  Editar
                </button>
                <button @click="confirmDelete"
                  class="px-4 py-2 rounded-full border border-border-light text-sm font-medium text-error hover:bg-error hover:text-white hover:border-error transition flex items-center gap-2 bg-white">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  Excluir
                </button>
              </div>
            </div>

            <div class="grid md:grid-cols-5 gap-8">
              <!-- Ingredients -->
              <div class="md:col-span-2">
                <h3 class="font-serif text-lg font-semibold mb-4 text-graphite">Ingredientes</h3>
                <ul class="space-y-3">
                  <li v-for="(ing, i) in recipe.ingredients" :key="i" class="flex items-start gap-3 group">
                    <div @click="toggleCheck(i)"
                      :class="['w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 mt-0.5 cursor-pointer transition',
                        checkedIngredients.has(i) ? 'bg-olive border-olive' : 'border-border-light group-hover:border-olive']">
                      <svg v-if="checkedIngredients.has(i)" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span :class="['text-sm leading-relaxed transition', checkedIngredients.has(i) ? 'text-sage line-through' : 'text-graphite']">{{ ing }}</span>
                  </li>
                </ul>
              </div>

              <!-- Steps + Rating -->
              <div class="md:col-span-3">
                <h3 class="font-serif text-lg font-semibold mb-4 text-graphite">Modo de preparo</h3>
                <div class="space-y-5">
                  <div v-for="(step, i) in recipe.steps" :key="i" class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-mint text-olive flex items-center justify-center text-sm font-bold flex-shrink-0">{{ i + 1 }}</div>
                    <p class="text-sage leading-relaxed pt-1 text-sm">{{ step }}</p>
                  </div>
                </div>

                <!-- Rating -->
                <div class="mt-8 pt-6 border-t border-border-light">
                  <h3 class="font-serif text-lg font-semibold mb-3 text-graphite">Avaliações</h3>
                  <div class="bg-white rounded-2xl p-5 border border-border-light">
                    <div class="flex items-center gap-4 mb-4">
                      <div class="text-3xl font-serif font-bold text-graphite">{{ averageRating }}</div>
                      <div>
                        <StarRating :average="Number(averageRating)" :readonly="true" />
                        <div class="text-xs text-sage mt-1">{{ recipe.ratings.length }} avaliações</div>
                      </div>
                    </div>

                    <div v-if="userStore.isLoggedIn && canRate" class="border-t border-border-light pt-4">
                      <p class="text-sm text-sage mb-2">Avalie esta receita:</p>
                      <StarRating v-model="userRating" :readonly="false" @rate="handleRate" />
                    </div>
                    <div v-else-if="!userStore.isLoggedIn" class="border-t border-border-light pt-4">
                      <p class="text-sm text-sage">
                        <button @click="userStore.openLogin(); close()" class="text-olive font-medium hover:underline">Entre</button> para avaliar.
                      </p>
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
      </div>
    </Transition>
  </Teleport>

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
          <p class="text-sage text-sm mb-6">Esta ação não pode ser desfeita.</p>
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
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useRecipeStore } from '@/stores/recipeStore'
import { useToastStore } from '@/stores/toastStore'
import StarRating from './StarRating.vue'

const props = defineProps({
  modelValue: Boolean,
  recipe: { type: Object, required: true }
})
const emit = defineEmits(['update:modelValue', 'deleted'])

const router = useRouter()
const userStore = useUserStore()
const recipeStore = useRecipeStore()
const toast = useToastStore()

const checkedIngredients = ref(new Set())
const showDeleteModal = ref(false)
const userRating = ref(0)

const users = {
  u1: { name: 'Ana Cozinheira', avatarUrl: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face' },
  u2: { name: 'Marco Sabor', avatarUrl: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face' },
  u3: { name: 'Julia Tempero', avatarUrl: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face' }
}

const author = computed(() => users[props.recipe?.authorId] || { name: 'Desconhecido', avatarUrl: '' })

const averageRating = computed(() => {
  if (!props.recipe?.ratings.length) return '0.0'
  return (props.recipe.ratings.reduce((a, b) => a + b.stars, 0) / props.recipe.ratings.length).toFixed(1)
})

const isOwner = computed(() => {
  return userStore.currentUser && props.recipe?.authorId === userStore.currentUser.id
})

const canRate = computed(() => {
  if (!userStore.currentUser) return false
  return !isOwner.value
})

watch(() => props.modelValue, (val) => {
  if (val) {
    checkedIngredients.value.clear()
    if (userStore.currentUser && props.recipe) {
      const r = props.recipe.ratings.find(rat => rat.userId === userStore.currentUser.id)
      userRating.value = r ? r.stars : 0
    }
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

function close() {
  emit('update:modelValue', false)
}

function toggleCheck(i) {
  if (checkedIngredients.value.has(i)) checkedIngredients.value.delete(i)
  else checkedIngredients.value.add(i)
}

async function handleRate(stars) {
  try {
    await recipeStore.rateRecipe(props.recipe.id, stars)
    toast.show('Avaliação registrada! ⭐')
  } catch (e) {
    toast.show(e.message, 'error')
  }
}

function goToEdit() {
  close()
  router.push(`/receitas/${props.recipe.id}/editar`)
}

function confirmDelete() {
  showDeleteModal.value = true
}

async function executeDelete() {
  try {
    await recipeStore.deleteRecipe(props.recipe.id)
    toast.show('Receita removida.')
    showDeleteModal.value = false
    close()
    emit('deleted')
  } catch (e) {
    toast.show(e.message, 'error')
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
.modal-enter-from .relative, .modal-leave-to .relative {
  transform: scale(0.95);
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
