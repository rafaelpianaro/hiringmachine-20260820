<template>
  <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
    @click="$emit('click')">
    <div class="relative aspect-[4/3] overflow-hidden">
      <img :src="recipe.coverImage" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" :alt="recipe.title">
      <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-xs font-medium text-graphite">
        {{ recipe.category }}
      </span>
      <div v-if="isOwner" class="absolute top-4 right-4 flex gap-2" @click.stop>
        <button @click="$router.push(`/recipes/${recipe.id}/edit`)"
          class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center hover:bg-olive hover:text-white transition shadow-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        </button>
        <button @click="$emit('delete', recipe.id)"
          class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center hover:bg-error hover:text-white transition shadow-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
      </div>
    </div>
    <div class="p-6">
      <h3 class="font-serif text-xl font-semibold mb-3 text-graphite group-hover:text-olive transition">
        {{ recipe.title }}
      </h3>
      <div class="flex items-center gap-3 text-xs text-sage uppercase tracking-wide mb-4">
        <span class="flex items-center gap-1">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          {{ recipe.prepTimeMinutes }} min
        </span>
        <span class="flex items-center gap-1">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M10 21v-6h4v6"/></svg>
          {{ recipe.servings }} servings
        </span>
        <span class="flex items-center gap-1">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          {{ recipe.difficulty }}
        </span>
      </div>
      <div class="flex items-center justify-between">
        <StarRating :average="averageRating" :readonly="!canRate" :show-count="true" :count="ratingCount"
          @rate="(stars) => $emit('rate', recipe.id, stars)" />
        <div class="flex items-center gap-2">
          <img v-if="author.avatarUrl" :src="author.avatarUrl" class="w-6 h-6 rounded-full object-cover" alt="">
          <div v-else class="w-6 h-6 rounded-full bg-olive/20 flex items-center justify-center text-xs font-medium text-olive">
            {{ author.name.charAt(0) }}
          </div>
          <span class="text-xs text-sage">{{ author.name.split(' ')[0] }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import StarRating from './StarRating.vue'

const props = defineProps({ recipe: Object })
defineEmits(['click', 'delete', 'rate'])

const router = useRouter()
const userStore = useUserStore()

const author = computed(() => ({
  name: props.recipe.authorName || 'Unknown',
  avatarUrl: props.recipe.authorAvatar || ''
}))

const averageRating = computed(() => {
  const ratings = Array.isArray(props.recipe?.ratings) ? props.recipe.ratings : []
  if (!ratings.length) return 0

  const total = ratings.reduce((sum, item) => sum + Number(item?.stars || 0), 0)
  return total / ratings.length
})

const ratingCount = computed(() => props.recipe.ratings.length)

const isOwner = computed(() => {
  return userStore.currentUser && props.recipe.authorId === userStore.currentUser.id
})

const canRate = computed(() => {
  if (!userStore.currentUser) return false
  return !isOwner.value
})
</script>
