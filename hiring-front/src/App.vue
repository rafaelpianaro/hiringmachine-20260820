<template>
  <div class="min-h-screen flex flex-col">
    <AppHeader />
    <main class="flex-1">
      <RouterView />
    </main>
    <AppFooter />
    <LoginModal />
    <ToastNotification />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import AppHeader from '@/components/layout/Header.vue'
import AppFooter from '@/components/layout/Footer.vue'
import LoginModal from '@/components/auth/LoginModal.vue'
import ToastNotification from '@/components/ToastNotification.vue'
</script>
