<template>
  <aside class="hidden md:flex flex-col h-screen w-64 bg-slate-50 dark:bg-slate-900 border-r border-outline-variant/10 fixed left-0 top-0 z-50">
    <div class="flex flex-col h-full py-6 px-4">
      <div class="mb-8 px-2 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center text-white">
          <img src="../../images/logo-aplicativo.png" alt="Logo Simulador Ads">
        </div>
        <div>
          <h1 class="text-lg font-bold text-slate-900 dark:text-white font-headline leading-tight">Simulador Ads</h1>
          <span class="text-[10px] font-label uppercase tracking-wider text-slate-500">Admin Console</span>
        </div>
      </div>
      
      <div class="space-y-1 flex-grow">
        <div class="mb-2 px-2">
          <span class="text-[11px] font-label font-bold uppercase tracking-widest text-on-surface-variant/60">Principal</span>
        </div>
        
        <router-link to="/dashboard" class="group flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-500 hover:translate-x-1 transition-transform duration-200">
          <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
          <span class="font-inter label-sm uppercase tracking-wider text-sm">Visão Geral</span>
        </router-link>
        
        <router-link to="/admin" exact-active-class="bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 rounded-xl shadow-sm hover:translate-x-1 transition-transform duration-200" class="group flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-500 hover:translate-x-1 transition-transform duration-200">
           <!-- Note: The exact-active-class handles the styling when the user is on this route, to make it look like the stitched ACTIVE TAB -->
          <span class="material-symbols-outlined" data-icon="cloud_upload" :style="$route.name === 'admin' ? 'font-variation-settings:\'FILL\' 1;' : ''">cloud_upload</span>
          <span class="font-inter label-sm uppercase tracking-wider text-sm" :class="{ 'font-bold': $route.name === 'admin' }">Importar Dados</span>
        </router-link>
      </div>
      
      <div class="mt-auto border-t border-outline-variant/10 pt-6 space-y-1">
        <button @click="handleLogout" class="w-full group flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-error hover:translate-x-1 transition-transform duration-200">
          <span class="material-symbols-outlined" data-icon="logout">logout</span>
          <span class="font-inter label-sm uppercase tracking-wider text-sm">Sair</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter, useRoute } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};
</script>
