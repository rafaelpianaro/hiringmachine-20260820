import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { recipeService } from '@/services/recipeService'
import { useUserStore } from './userStore'

export const useRecipeStore = defineStore('recipe', () => {
  const recipes = ref([])
  const myRecipes = ref([])
  const loading = ref(false)
  const error = ref(null)
  const fieldErrors = ref(null)
  const searchQuery = ref('')
  const activeCategory = ref('Todas')

  const categories = [
    { name: 'Sobremesas', count: 124, image: 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&h=200&fit=crop' },
    { name: 'Sopas', count: 89, image: 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=200&h=200&fit=crop' },
    { name: 'Vegetariano', count: 156, image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=200&h=200&fit=crop' },
    { name: 'Carnes', count: 203, image: 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=200&h=200&fit=crop' }
  ]

  const filteredRecipes = computed(() => {
    let result = recipes.value
    if (activeCategory.value !== 'Todas') {
      result = result.filter(r => r.category === activeCategory.value)
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase()
      result = result.filter(r =>
        r.title.toLowerCase().includes(q) ||
        r.ingredients.some(i => i.toLowerCase().includes(q))
      )
    }
    return result
  })

  async function fetchRecipes() {
    loading.value = true
    error.value = null
    try {
      recipes.value = await recipeService.getAllRecipes()
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function fetchMyRecipes() {
    loading.value = true
    error.value = null
    try {
      myRecipes.value = await recipeService.getMyRecipes()
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function getRecipe(id) {
    loading.value = true
    try {
      return await recipeService.getRecipeById(id)
    } finally {
      loading.value = false
    }
  }

  async function createRecipe(formData) {
    fieldErrors.value = null
    try {
      const newRecipe = await recipeService.createRecipe(formData)
      recipes.value.unshift(newRecipe)
      return newRecipe
    } catch (e) {
      error.value = e.message
      fieldErrors.value = e.fieldErrors || null
      throw e
    }
  }

  async function updateRecipe(id, formData) {
    fieldErrors.value = null
    try {
      const updated = await recipeService.updateRecipe(id, formData)
      const idx = recipes.value.findIndex(r => r.id === id)
      if (idx !== -1) recipes.value[idx] = updated
      return updated
    } catch (e) {
      error.value = e.message
      fieldErrors.value = e.fieldErrors || null
      throw e
    }
  }

  async function deleteRecipe(id) {
    await recipeService.deleteRecipe(id)
    recipes.value = recipes.value.filter(r => r.id !== id)
  }

  async function rateRecipe(recipeId, stars) {
    const updated = await recipeService.rateRecipe(recipeId, stars)
    // Atualiza a receita no array local com os ratings retornados
    const id = String(recipeId)
    const idx = recipes.value.findIndex(r => r.id === id)
    if (idx !== -1) {
      // Substitui o objeto inteiro para garantir reatividade
      recipes.value.splice(idx, 1, updated)
    }
    // Atualiza também em myRecipes se existir
    const idxMy = myRecipes.value.findIndex(r => r.id === id)
    if (idxMy !== -1) {
      myRecipes.value.splice(idxMy, 1, updated)
    }
    return updated
  }

  function setCategory(cat) { activeCategory.value = cat }
  function setSearch(q) { searchQuery.value = q }

  return {
    recipes, loading, error, fieldErrors, searchQuery, activeCategory, categories,
    filteredRecipes, myRecipes,
    fetchRecipes, fetchMyRecipes, getRecipe, createRecipe, updateRecipe, deleteRecipe, rateRecipe,
    setCategory, setSearch
  }
})
