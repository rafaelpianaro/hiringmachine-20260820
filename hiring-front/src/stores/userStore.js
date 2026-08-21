import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/authService'

const STORAGE_KEY_USER = 'recepies_user'
const STORAGE_KEY_TOKEN = 'recepies_token'

export const useUserStore = defineStore('user', () => {
  const currentUser = ref(null)
  const token = ref(null)
  const showLoginModal = ref(false)
  const loading = ref(false)
  const error = ref(null)
  const fieldErrors = ref(null)

  // Restaurar sessão do localStorage
  const savedUser = localStorage.getItem(STORAGE_KEY_USER)
  const savedToken = localStorage.getItem(STORAGE_KEY_TOKEN)
  if (savedUser && savedToken) {
    try {
      currentUser.value = JSON.parse(savedUser)
      token.value = savedToken
    } catch (e) {}
  }

  const isLoggedIn = computed(() => !!currentUser.value && !!token.value)

  function setSession(user, tokenValue) {
    currentUser.value = user
    token.value = tokenValue
    localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(user))
    localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue)
  }

  function clearSession() {
    currentUser.value = null
    token.value = null
    localStorage.removeItem(STORAGE_KEY_USER)
    localStorage.removeItem(STORAGE_KEY_TOKEN)
  }

  function clearErrors() {
    error.value = null
    fieldErrors.value = null
  }

  async function login(email, password) {
    loading.value = true
    clearErrors()
    try {
      const response = await authService.login(email, password)
      const data = response.data || response
      const user = {
        id: String(data.user?.id || data.id),
        name: data.user?.name || data.name,
        email: data.user?.email || data.email,
        avatarUrl: `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user?.name || data.name)}&background=8DB33F&color=fff&size=128`
      }
      const authToken = data.token || data.access_token
      setSession(user, authToken)
      showLoginModal.value = false
      return user
    } catch (e) {
      error.value = e.message
      fieldErrors.value = e.fieldErrors || null
      throw e
    } finally {
      loading.value = false
    }
  }

  async function register(formData) {
    loading.value = true
    clearErrors()
    try {
      const response = await authService.register(formData)
      const data = response.data || response
      const user = {
        id: String(data.user?.id || data.id),
        name: data.user?.name || data.name,
        email: data.user?.email || data.email,
        avatarUrl: `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user?.name || data.name)}&background=8DB33F&color=fff&size=128`
      }
      const authToken = data.token || data.access_token
      setSession(user, authToken)
      showLoginModal.value = false
      return user
    } catch (e) {
      error.value = e.message
      fieldErrors.value = e.fieldErrors || null
      throw e
    } finally {
      loading.value = false
    }
  }

  function logout() {
    clearSession()
  }

  function openLogin() {
    clearErrors()
    showLoginModal.value = true
  }

  function closeLogin() {
    clearErrors()
    showLoginModal.value = false
  }

  return {
    currentUser, token, isLoggedIn, showLoginModal, loading, error, fieldErrors,
    login, register, logout, openLogin, closeLogin
  }
})
