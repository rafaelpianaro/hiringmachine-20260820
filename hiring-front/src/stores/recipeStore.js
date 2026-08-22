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
  const activeCategory = ref('All')

  const categories = [
    { name: 'Desserts', count: 124, image: 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&h=200&fit=crop' },
    { name: 'Soups', count: 89, image: 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=200&h=200&fit=crop' },
    { name: 'Vegetarian', count: 156, image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=200&h=200&fit=crop' },
    { name: 'Meats', count: 203, image: 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=200&h=200&fit=crop' }
  ]

  const filteredRecipes = computed(() => {
    let result = recipes.value
    if (activeCategory.value !== 'All') {
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
    const recipeId = String(id)
    await recipeService.deleteRecipe(id)
    recipes.value = recipes.value.filter(r => String(r.id) !== recipeId)
    myRecipes.value = myRecipes.value.filter(r => String(r.id) !== recipeId)
  }

  async function rateRecipe(recipeId, stars) {
    const updated = await recipeService.rateRecipe(recipeId, stars)
    // Update the recipe in the local array with returned ratings
    const id = String(recipeId)
    const idx = recipes.value.findIndex(r => r.id === id)
    if (idx !== -1) {
      // Replace the entire object to ensure reactivity
      recipes.value.splice(idx, 1, updated)
    }
    // Also update in myRecipes if it exists
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
