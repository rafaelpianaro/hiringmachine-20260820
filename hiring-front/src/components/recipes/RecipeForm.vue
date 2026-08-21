<template>
  <div class="max-w-3xl mx-auto px-6 pt-24 pb-16">
    <button @click="$router.back()" class="mb-6 flex items-center gap-2 text-sm text-sage hover:text-olive transition">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Voltar
    </button>

    <h1 class="font-serif text-3xl md:text-4xl font-medium mb-8 text-graphite">
      {{ isEditing ? 'Editar receita' : 'Nova receita' }}
    </h1>

    <!-- Global Error -->
    <div v-if="globalError" class="mb-6 p-4 rounded-xl bg-error/10 text-error text-sm">
      {{ globalError }}
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-8">
      <!-- Basic Info -->
      <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-border-light">
        <h3 class="font-serif text-lg font-semibold mb-5 text-graphite">Informações básicas</h3>
        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1.5 text-graphite">Título da receita *</label>
            <input v-model="form.title" type="text" placeholder="Ex: Bolo de Cenoura"
              :class="inputClass('title')"
              @input="clearFieldError('title')">
            <p v-if="fieldError('title')" class="mt-1 text-xs text-error">{{ fieldError('title') }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5 text-graphite">Descrição *</label>
            <textarea v-model="form.description" rows="2" placeholder="Descreva sua receita..."
              :class="inputClass('description') + ' resize-none'"
              @input="clearFieldError('description')"></textarea>
            <p v-if="fieldError('description')" class="mt-1 text-xs text-error">{{ fieldError('description') }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Categoria *</label>
              <select v-model="form.category"
                :class="inputClass('category')"
                @change="clearFieldError('category')">
                <option value="">Selecione...</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
              <p v-if="fieldError('category')" class="mt-1 text-xs text-error">{{ fieldError('category') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Dificuldade *</label>
              <select v-model="form.difficulty"
                :class="inputClass('difficulty')"
                @change="clearFieldError('difficulty')">
                <option value="">Selecione...</option>
                <option value="easy">Fácil</option>
                <option value="medium">Médio</option>
                <option value="hard">Difícil</option>
              </select>
              <p v-if="fieldError('difficulty')" class="mt-1 text-xs text-error">{{ fieldError('difficulty') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Tempo de preparo (min)</label>
              <input v-model.number="form.prep_time" type="number" min="1"
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
              :class="['flex-1 px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('ingredients') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
              @input="clearFieldError('ingredients')">
            <button type="button" @click="removeIngredient(i)" v-if="form.ingredients.length > 1"
              class="px-3 py-2 rounded-xl border border-border-light text-sage hover:border-error hover:text-error transition">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </div>
        <p v-if="fieldError('ingredients')" class="mt-2 text-xs text-error">{{ fieldError('ingredients') }}</p>
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
          <div v-for="(step, i) in form.instructions" :key="i" class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-mint text-olive flex items-center justify-center text-sm font-bold flex-shrink-0 mt-1">{{ i + 1 }}</div>
            <textarea v-model="form.instructions[i]" rows="2" placeholder="Descreva o passo..."
              :class="['flex-1 px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition resize-none', fieldError('instructions') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
              @input="clearFieldError('instructions')"></textarea>
            <button type="button" @click="removeStep(i)" v-if="form.instructions.length > 1"
              class="px-3 py-2 rounded-xl border border-border-light text-sage hover:border-error hover:text-error transition self-start">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </div>
        <p v-if="fieldError('instructions')" class="mt-2 text-xs text-error">{{ fieldError('instructions') }}</p>
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
  initialData: { type: Object, default: null },
  fieldErrors: { type: Object, default: null },
  globalError: { type: String, default: '' }
})
const emit = defineEmits(['submit', 'clearError'])

const saving = ref(false)
const imagePreview = ref('')

const categories = ['Doces', 'Bolos', 'Saladas', 'Pratos Principais', 'Pães', 'Sopas', 'Massas', 'Carnes', 'Bebidas', 'Sobremesas', 'Vegetariano']

const form = reactive({
  title: '',
  description: '',
  category: '',
  prep_time: 30,
  servings: 4,
  difficulty: '',
  image: '',
  ingredients: [''],
  instructions: ['']
})

const isEditing = ref(false)

watch(() => props.initialData, (val) => {
  if (val) {
    isEditing.value = true
    form.title = val.title || ''
    form.description = val.description || ''
    form.category = val.category || ''
    form.prep_time = val.prepTimeMinutes || val.prep_time || 30
    form.servings = val.servings || 4
    form.difficulty = val.difficulty || ''
    form.image = val.image || val.coverImage || ''
    form.ingredients = val.ingredients?.length ? [...val.ingredients] : ['']
    form.instructions = (val.steps || val.instructions || []).length ? [...(val.steps || val.instructions)] : ['']
    imagePreview.value = val.image || val.coverImage || ''
  }
}, { immediate: true })

function fieldError(field) {
  const fe = props.fieldErrors
  if (!fe || !fe[field]) return ''
  const errors = fe[field]
  return Array.isArray(errors) ? errors[0] : errors
}

function inputClass(field) {
  const base = 'w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition bg-white'
  return fieldError(field) ? `${base} border-error focus:border-error` : `${base} border-border-light focus:border-olive`
}

function clearFieldError(field) {
  if (props.fieldErrors?.[field]) {
    emit('clearError', field)
  }
}

function handleImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  const url = URL.createObjectURL(file)
  imagePreview.value = url
  form.image = url
}

function addIngredient() { form.ingredients.push('') }
function removeIngredient(i) { if (form.ingredients.length > 1) form.ingredients.splice(i, 1) }
function addStep() { form.instructions.push('') }
function removeStep(i) { if (form.instructions.length > 1) form.instructions.splice(i, 1) }

async function handleSubmit() {
  saving.value = true
  const payload = {
    title: form.title,
    description: form.description,
    category: form.category,
    difficulty: form.difficulty,
    prep_time: form.prep_time,
    cook_time: form.cook_time || 0,
    servings: form.servings,
    image: form.image || null,
    ingredients: form.ingredients.filter(i => i.trim()),
    instructions: form.instructions.filter(s => s.trim())
  }
  emit('submit', payload)
}

// Resetar saving quando API retornar erro
let wasSubmitting = false
watch(() => saving.value, (val) => { wasSubmitting = val })
watch(() => [props.fieldErrors, props.globalError], ([fe, ge]) => {
  if (wasSubmitting && (fe || ge)) {
    saving.value = false
    wasSubmitting = false
  }
})
</script>
