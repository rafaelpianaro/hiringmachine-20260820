<template>
  <RecipeForm :initial-data="existingRecipe" @submit="handleSubmit" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRecipeStore } from '@/stores/recipeStore'
import { useToastStore } from '@/stores/toastStore'
import RecipeForm from '@/components/recipes/RecipeForm.vue'

const route = useRoute()
const router = useRouter()
const recipeStore = useRecipeStore()
const toast = useToastStore()

const existingRecipe = ref(null)
const isEditing = ref(false)

onMounted(async () => {
  if (route.params.id) {
    isEditing.value = true
    try {
      const recipe = await recipeStore.getRecipe(route.params.id)
      existingRecipe.value = recipe
    } catch (e) {
      toast.show(e.message, 'error')
      router.push('/')
    }
  }
})

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
    toast.show(e.message, 'error')
  }
}
</script>
