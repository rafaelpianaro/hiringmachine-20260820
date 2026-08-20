import recipesData from '@/data/recipes.json'
import { useUserStore } from '@/stores/userStore'

const delay = (ms = 400) => new Promise(r => setTimeout(r, ms))
let recipes = [...recipesData]

const generateId = () => 'r' + Date.now() + Math.random().toString(36).substr(2, 5)
const generateSlug = (title) => title.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')

export const recipeService = {
  async getAllRecipes() {
    await delay(300)
    return [...recipes]
  },

  async getRecipeById(id) {
    await delay(200)
    const recipe = recipes.find(r => r.id === id)
    if (!recipe) throw new Error('Receita não encontrada')
    return { ...recipe }
  },

  async getRecipesByCategory(category) {
    await delay(300)
    return recipes.filter(r => r.category === category)
  },

  async searchRecipes(query) {
    await delay(400)
    const q = query.toLowerCase()
    return recipes.filter(r =>
      r.title.toLowerCase().includes(q) ||
      r.ingredients.some(i => i.toLowerCase().includes(q))
    )
  },

  async getMyRecipes(userId) {
    await delay(300)
    return recipes.filter(r => r.authorId === userId)
  },

  async createRecipe(formData) {
    await delay(600)
    const userStore = useUserStore()
    if (!userStore.currentUser) throw new Error('Usuário não autenticado')

    const newRecipe = {
      id: generateId(),
      title: formData.title,
      slug: generateSlug(formData.title),
      coverImage: formData.coverImage || 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&h=600&fit=crop',
      category: formData.category,
      prepTimeMinutes: Number(formData.prepTimeMinutes),
      servings: Number(formData.servings),
      difficulty: formData.difficulty,
      ingredients: formData.ingredients.filter(i => i.trim()),
      steps: formData.steps.filter(s => s.trim()),
      authorId: userStore.currentUser.id,
      ratings: [],
      createdAt: new Date().toISOString().split('T')[0],
      updatedAt: new Date().toISOString().split('T')[0]
    }
    recipes.unshift(newRecipe)
    return { ...newRecipe }
  },

  async updateRecipe(id, formData) {
    await delay(600)
    const userStore = useUserStore()
    const idx = recipes.findIndex(r => r.id === id)
    if (idx === -1) throw new Error('Receita não encontrada')
    if (recipes[idx].authorId !== userStore.currentUser?.id) throw new Error('Sem permissão')

    recipes[idx] = {
      ...recipes[idx],
      title: formData.title,
      slug: generateSlug(formData.title),
      category: formData.category,
      prepTimeMinutes: Number(formData.prepTimeMinutes),
      servings: Number(formData.servings),
      difficulty: formData.difficulty,
      coverImage: formData.coverImage || recipes[idx].coverImage,
      ingredients: formData.ingredients.filter(i => i.trim()),
      steps: formData.steps.filter(s => s.trim()),
      updatedAt: new Date().toISOString().split('T')[0]
    }
    return { ...recipes[idx] }
  },

  async deleteRecipe(id) {
    await delay(400)
    const userStore = useUserStore()
    const recipe = recipes.find(r => r.id === id)
    if (!recipe) throw new Error('Receita não encontrada')
    if (recipe.authorId !== userStore.currentUser?.id) throw new Error('Sem permissão')
    recipes = recipes.filter(r => r.id !== id)
    return { success: true }
  },

  async rateRecipe(recipeId, stars) {
    await delay(300)
    const userStore = useUserStore()
    if (!userStore.currentUser) throw new Error('Faça login para avaliar')

    const recipe = recipes.find(r => r.id === recipeId)
    if (!recipe) throw new Error('Receita não encontrada')
    if (recipe.authorId === userStore.currentUser.id) throw new Error('Não pode avaliar própria receita')

    const existing = recipe.ratings.find(r => r.userId === userStore.currentUser.id)
    if (existing) {
      existing.stars = stars
    } else {
      recipe.ratings.push({ userId: userStore.currentUser.id, stars })
    }
    return { ...recipe }
  }
}
