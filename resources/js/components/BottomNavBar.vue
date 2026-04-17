<template>
  <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-xl border-t border-outline-variant/20 flex justify-around items-center px-4 z-50">
    <router-link to="/dashboard" class="flex flex-col items-center gap-1" :class="isDashboard ? 'text-blue-600' : 'text-slate-400'">
      <span class="material-symbols-outlined" data-icon="dashboard" :style="isDashboard ? 'font-variation-settings:\'FILL\' 1;' : ''">dashboard</span>
      <span class="text-[10px] font-label uppercase" :class="{ 'font-bold': isDashboard }">Início</span>
    </router-link>
    
    <router-link v-if="authStore.isAdmin" to="/admin" class="flex flex-col items-center gap-1" :class="isAdmin ? 'text-blue-600' : 'text-slate-400'">
      <span class="material-symbols-outlined" data-icon="cloud_upload" :style="isAdmin ? 'font-variation-settings:\'FILL\' 1;' : ''">cloud_upload</span>
      <span class="text-[10px] font-label uppercase" :class="{ 'font-bold': isAdmin }">Importar</span>
    </router-link>
    
    <router-link to="/history" class="flex flex-col items-center gap-1" :class="isHistory ? 'text-blue-600' : 'text-slate-400'">
      <span class="material-symbols-outlined" data-icon="history" :style="isHistory ? 'font-variation-settings:\'FILL\' 1;' : ''">history</span>
      <span class="text-[10px] font-label uppercase" :class="{ 'font-bold': isHistory }">Histórico</span>
    </router-link>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const authStore = useAuthStore();

const isDashboard = computed(() => route.name === 'dashboard');
const isAdmin = computed(() => route.name === 'admin');
const isHistory = computed(() => route.name === 'history');
</script>
