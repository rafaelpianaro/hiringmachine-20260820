<template>
  <RecipeForm
    :initial-data="existingRecipe"
    :field-errors="recipeStore.fieldErrors"
    :global-error="recipeStore.error"
    @submit="handleSubmit"
    @clear-error="clearFieldError"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRecipeStore } from '@/stores/recipeStore'
import { useUserStore } from '@/stores/userStore'
import { useToastStore } from '@/stores/toastStore'
import RecipeForm from '@/components/recipes/RecipeForm.vue'

const route = useRoute()
const router = useRouter()
const recipeStore = useRecipeStore()
const userStore = useUserStore()
const toast = useToastStore()

const existingRecipe = ref(null)
const isEditing = ref(false)

onMounted(async () => {
  // Limpar erros anteriores ao entrar na página
  recipeStore.fieldErrors = null
  recipeStore.error = null

  if (route.params.id) {
    isEditing.value = true
    try {
      const recipe = await recipeStore.getRecipe(route.params.id)

      // Check if the logged-in user is the owner of the recipe
      if (!userStore.currentUser || String(recipe.authorId) !== String(userStore.currentUser.id)) {
        toast.show('Você não tem permissão para editar esta receita.', 'error')
        router.push('/minhas-receitas')
        return
      }

      existingRecipe.value = recipe
    } catch (e) {
      toast.show(e.message, 'error')
      router.push('/')
    }
  }
})

function clearFieldError(field) {
  const fe = recipeStore.fieldErrors
  if (fe && fe[field]) {
    const { [field]: _, ...rest } = fe
    recipeStore.fieldErrors = { ...rest }
  }
  if (recipeStore.error) recipeStore.error = null
}

async function handleSubmit(formData) {
  try {
    if (isEditing.value) {
      await recipeStore.updateRecipe(route.params.id, formData)
      toast.show('Sua receita foi atualizada com carinho 🍲')
    } else {
      await recipeStore.createRecipe(formData)
      toast.show('Sua receita foi salva com carinho 🍲')
    }
    router.push('/minhas-receitas')
  } catch (e) {
    // erros já são exibidos pelo store
  }
}
</script>
