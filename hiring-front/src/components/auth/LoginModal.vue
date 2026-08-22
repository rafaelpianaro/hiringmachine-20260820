<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="userStore.showLoginModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="userStore.closeLogin()"></div>
        <div class="relative bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
          <button @click="userStore.closeLogin()" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition shadow-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>

          <!-- Tabs -->
          <div class="flex mb-6 bg-mint rounded-full p-1">
            <button @click="switchMode('login')"
              :class="['flex-1 py-2.5 rounded-full text-sm font-medium transition', mode === 'login' ? 'bg-white text-graphite shadow-sm' : 'text-sage hover:text-graphite']">
              Sign In
            </button>
            <button @click="switchMode('register')"
              :class="['flex-1 py-2.5 rounded-full text-sm font-medium transition', mode === 'register' ? 'bg-white text-graphite shadow-sm' : 'text-sage hover:text-graphite']">
              Sign Up
            </button>
          </div>

          <!-- Global Error -->
          <div v-if="globalError && !hasFieldErrors" class="mb-4 p-3 rounded-xl bg-error/10 text-error text-sm">
            {{ globalError }}
          </div>

          <!-- Login Form -->
          <form v-if="mode === 'login'" @submit.prevent="handleLogin" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Email</label>
              <input v-model="loginForm.email" type="email" placeholder="you@email.com"
                :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('email') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                @input="clearFieldError('email')">
              <p v-if="fieldError('email')" class="mt-1 text-xs text-error">{{ fieldError('email') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Password</label>
              <input v-model="loginForm.password" type="password" placeholder="Your password"
                :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('password') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                @input="clearFieldError('password')">
              <p v-if="fieldError('password')" class="mt-1 text-xs text-error">{{ fieldError('password') }}</p>
            </div>
            <button type="submit" :disabled="userStore.loading"
              class="w-full py-3 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="userStore.loading" class="flex items-center justify-center gap-2">
                <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Signing in...
              </span>
              <span v-else>Sign In</span>
            </button>
          </form>

          <!-- Register Form -->
          <form v-else @submit.prevent="handleRegister" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Full name</label>
              <input v-model="registerForm.name" type="text" placeholder="Your name"
                :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('name') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                @input="clearFieldError('name')">
              <p v-if="fieldError('name')" class="mt-1 text-xs text-error">{{ fieldError('name') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5 text-graphite">Email</label>
              <input v-model="registerForm.email" type="email" placeholder="you@email.com"
                :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('email') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                @input="clearFieldError('email')">
              <p v-if="fieldError('email')" class="mt-1 text-xs text-error">{{ fieldError('email') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium mb-1.5 text-graphite">Password</label>
                <input v-model="registerForm.password" type="password" placeholder="Min. 8 characters"
                  :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('password') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                  @input="clearFieldError('password')">
                <p v-if="fieldError('password')" class="mt-1 text-xs text-error">{{ fieldError('password') }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1.5 text-graphite">Confirm password</label>
                <input v-model="registerForm.password_confirmation" type="password" placeholder="Repeat password"
                  :class="['w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-olive/20 outline-none transition', fieldError('password_confirmation') ? 'border-error focus:border-error' : 'border-border-light focus:border-olive']"
                  @input="clearFieldError('password_confirmation')">
                <p v-if="fieldError('password_confirmation')" class="mt-1 text-xs text-error">{{ fieldError('password_confirmation') }}</p>
              </div>
            </div>
            <button type="submit" :disabled="userStore.loading"
              class="w-full py-3 rounded-full bg-olive text-white font-medium hover:bg-olive-dark hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="userStore.loading" class="flex items-center justify-center gap-2">
                <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Creating account...
              </span>
              <span v-else>Sign Up</span>
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useToastStore } from '@/stores/toastStore'

const userStore = useUserStore()
const toast = useToastStore()

const mode = ref('login')

// Refs locais para erros — garantem reatividade no template
const fieldErrors = ref(null)
const globalError = ref('')

const loginForm = reactive({ email: '', password: '' })
const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const hasFieldErrors = ref(false)

// Sincronizar erros do store → refs locais
watch(() => userStore.fieldErrors, (val) => {
  fieldErrors.value = val
  hasFieldErrors.value = val !== null && typeof val === 'object' && Object.keys(val).length > 0
}, { immediate: true })

watch(() => userStore.error, (val) => {
  globalError.value = val || ''
}, { immediate: true })

function fieldError(field) {
  if (!fieldErrors.value || !fieldErrors.value[field]) return ''
  const errors = fieldErrors.value[field]
  return Array.isArray(errors) ? errors[0] : errors
}

function clearFieldError(field) {
  if (fieldErrors.value && fieldErrors.value[field]) {
    const { [field]: _, ...rest } = fieldErrors.value
    fieldErrors.value = rest
    userStore.fieldErrors = { ...rest }
    hasFieldErrors.value = Object.keys(rest).length > 0
  }
  if (globalError.value) {
    globalError.value = ''
    userStore.error = null
  }
}

function switchMode(newMode) {
  mode.value = newMode
  fieldErrors.value = null
  globalError.value = ''
  userStore.error = null
  userStore.fieldErrors = null
}

async function handleLogin() {
  if (!loginForm.email.trim() || !loginForm.password.trim()) return
  try {
    await userStore.login(loginForm.email, loginForm.password)
    toast.show('Welcome to the kitchen! 👨‍🍳')
    loginForm.email = ''
    loginForm.password = ''
  } catch (e) {
    // erros já sincronizados via watch
  }
}

async function handleRegister() {
  if (registerForm.password !== registerForm.password_confirmation) {
    const errs = { password_confirmation: ['Passwords do not match'] }
    fieldErrors.value = errs
    userStore.fieldErrors = errs
    hasFieldErrors.value = true
    return
  }
  try {
    await userStore.register(registerForm)
    toast.show('Account created successfully! Welcome 👨‍🍳')
    Object.assign(registerForm, { name: '', email: '', password: '', password_confirmation: '' })
  } catch (e) {
    // erros já sincronizados via watch
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
