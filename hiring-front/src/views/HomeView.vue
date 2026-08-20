<template>
  <div>
    <Hero />
    <CategoryGrid />
    <RecipeGrid />
    <CtaSection />
  </div>
</template>

<script setup>
import Hero from '@/components/home/Hero.vue'
import CategoryGrid from '@/components/home/CategoryGrid.vue'
import RecipeGrid from '@/components/recipes/RecipeGrid.vue'
import CtaSection from '@/components/home/CtaSection.vue'
</script>
