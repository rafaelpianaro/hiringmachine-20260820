<template>
  <div class="max-w-3xl mx-auto px-6 pt-24 pb-16">
    <button @click="$router.back()" class="mb-6 flex items-center gap-2 text-sm text-sage hover:text-olive transition">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Voltar
    </button>

    <h1 class="font-serif text-3xl md:text-4xl font-medium mb-8 text-graphite">
      {{ isEditing ? 'Editar receita' : 'Nova receita' }}
    </h1>

    <form @submit.prevent="handleSubmit" class="space-y-8">
      <!-- Basic Info -->
      <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-border-light">
        <h3 class="font-serif text-lg font-semibold mb-5 text-graphite">Informações básicas</h3>
        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1.5 text-graphite">Título da receita *</label>
            <input v-model="form.title" type="text" placeholder="Ex: Bolo de Cenoura"
              class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition" required>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Categoria</label>
              <select v-model="form.category"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition bg-white">
                <option>Sobremesas</option>
                <option>Sopas</option>
                <option>Vegetariano</option>
                <option>Carnes</option>
                <option>Massas</option>
                <option>Bebidas</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Dificuldade</label>
              <select v-model="form.difficulty"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition bg-white">
                <option>Fácil</option>
                <option>Médio</option>
                <option>Difícil</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Tempo (minutos)</label>
              <input v-model.number="form.prepTimeMinutes" type="number" min="1"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Porções</label>
              <input v-model.number="form.servings" type="number" min="1"
                class="w-full px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5 text-graphite">Imagem da receita</label>
            <div class="flex items-center gap-4">
              <div v-if="imagePreview" class="w-24 h-24 rounded-2xl overflow-hidden flex-shrink-0">
                <img :src="imagePreview" class="w-full h-full object-cover" alt="preview">
              </div>
              <label class="flex-1 cursor-pointer">
                <div class="w-full px-4 py-8 rounded-xl border-2 border-dashed border-border-light hover:border-olive transition text-center">
                  <span class="text-sm text-sage">Clique para fazer upload de uma imagem</span>
                </div>
                <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Ingredients -->
      <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-border-light">
        <h3 class="font-serif text-lg font-semibold mb-5 text-graphite">Ingredientes *</h3>
        <div class="space-y-3">
          <div v-for="(ing, i) in form.ingredients" :key="i" class="flex gap-2">
            <input v-model="form.ingredients[i]" type="text" placeholder="Ex: 2 xícaras de farinha"
              class="flex-1 px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition">
            <button type="button" @click="removeIngredient(i)" v-if="form.ingredients.length > 1"
              class="px-3 py-2 rounded-xl border border-border-light text-sage hover:border-error hover:text-error transition">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </div>
        <button type="button" @click="addIngredient"
          class="mt-4 inline-flex items-center gap-2 text-sm text-olive font-medium hover:text-olive-dark transition">
          <span class="w-6 h-6 rounded-full bg-olive/10 flex items-center justify-center text-olive text-xs">+</span>
          Adicionar ingrediente
        </button>
      </div>

      <!-- Steps -->
      <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-border-light">
        <h3 class="font-serif text-lg font-semibold mb-5 text-graphite">Modo de preparo *</h3>
        <div class="space-y-3">
          <div v-for="(step, i) in form.steps" :key="i" class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-mint text-olive flex items-center justify-center text-sm font-bold flex-shrink-0 mt-1">{{ i + 1 }}</div>
            <textarea v-model="form.steps[i]" rows="2" placeholder="Descreva o passo..."
              class="flex-1 px-4 py-3 rounded-xl border border-border-light focus:border-olive focus:ring-2 focus:ring-olive/20 outline-none transition resize-none"></textarea>
            <button type="button" @click="removeStep(i)" v-if="form.steps.length > 1"
              class="px-3 py-2 rounded-xl border border-border-light text-sage hover:border-error hover:text-error transition self-start">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </div>
        <button type="button" @click="addStep"
          class="mt-4 inline-flex items-center gap-2 text-sm text-olive font-medium hover:text-olive-dark transition">
          <span class="w-6 h-6 rounded-full bg-olive/10 flex items-center justify-center text-olive text-xs">+</span>
          Adicionar passo
        </button>
      </div>

      <!-- Actions -->
      <div class="flex gap-4">
        <button type="submit" :disabled="saving"
          class="flex-1 py-3.5 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-60 flex items-center justify-center gap-2">
          <div v-if="saving" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          <span>{{ saving ? 'Salvando...' : (isEditing ? 'Salvar alterações' : 'Salvar receita') }}</span>
        </button>
        <button type="button" @click="$router.back()"
          class="px-8 py-3.5 rounded-full border border-border-light font-medium hover:bg-mint transition">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  initialData: { type: Object, default: null }
})
const emit = defineEmits(['submit'])

const saving = ref(false)
const imagePreview = ref('')

const form = reactive({
  title: '',
  category: 'Sobremesas',
  prepTimeMinutes: 30,
  servings: 4,
  difficulty: 'Fácil',
  coverImage: '',
  ingredients: [''],
  steps: ['']
})

const isEditing = ref(false)

watch(() => props.initialData, (val) => {
  if (val) {
    isEditing.value = true
    form.title = val.title
    form.category = val.category
    form.prepTimeMinutes = val.prepTimeMinutes
    form.servings = val.servings
    form.difficulty = val.difficulty
    form.coverImage = val.coverImage
    form.ingredients = [...val.ingredients]
    form.steps = [...val.steps]
    imagePreview.value = val.coverImage
  }
}, { immediate: true })

function handleImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  const url = URL.createObjectURL(file)
  imagePreview.value = url
  form.coverImage = url
}

function addIngredient() { form.ingredients.push('') }
function removeIngredient(i) { if (form.ingredients.length > 1) form.ingredients.splice(i, 1) }
function addStep() { form.steps.push('') }
function removeStep(i) { if (form.steps.length > 1) form.steps.splice(i, 1) }

async function handleSubmit() {
  if (!form.title.trim() || form.ingredients.every(i => !i.trim()) || form.steps.every(s => !s.trim())) {
    alert('Preencha o título, pelo menos um ingrediente e um passo.')
    return
  }
  saving.value = true
  try {
    await emit('submit', { ...form })
  } finally {
    saving.value = false
  }
}
</script>
