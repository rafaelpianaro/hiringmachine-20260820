import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const STORAGE_KEY = 'recepies_user'

export const useUserStore = defineStore('user', () => {
  const currentUser = ref(null)
  const showLoginModal = ref(false)

  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved) {
    try { currentUser.value = JSON.parse(saved) } catch (e) {}
  }

  const isLoggedIn = computed(() => !!currentUser.value)

  function login(name, email) {
    const user = {
      id: 'u' + Date.now(),
      name: name.trim(),
      avatarUrl: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=8DB33F&color=fff&size=128`
    }
    currentUser.value = user
    localStorage.setItem(STORAGE_KEY, JSON.stringify(user))
    showLoginModal.value = false
    return user
  }

  function logout() {
    currentUser.value = null
    localStorage.removeItem(STORAGE_KEY)
  }

  function openLogin() { showLoginModal.value = true }
  function closeLogin() { showLoginModal.value = false }

  return { currentUser, isLoggedIn, showLoginModal, login, logout, openLogin, closeLogin }
})
