import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/recipes/:id', name: 'recipe-detail', component: () => import('@/views/RecipeDetailView.vue') },
    { path: '/my-recipes', name: 'my-recipes', component: () => import('@/views/MyRecipesView.vue'), meta: { requiresAuth: true } },
    { path: '/recipes/new', name: 'new-recipe', component: () => import('@/views/RecipeEditView.vue'), meta: { requiresAuth: true } },
    { path: '/recipes/:id/edit', name: 'edit-recipe', component: () => import('@/views/RecipeEditView.vue'), meta: { requiresAuth: true } }
  ],
  scrollBehavior() { return { top: 0, behavior: 'smooth' } }
})

router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('recepies_user') || 'null')
  if (to.meta.requiresAuth && !user) {
    next('/')
  } else {
    next()
  }
})

export default router
