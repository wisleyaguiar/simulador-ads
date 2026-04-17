<template>
  <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl flex justify-between items-center fixed w-full px-8 h-20 top-0 z-50 shadow-xl shadow-slate-200/50 dark:shadow-none">
    <div class="flex items-center gap-6">
      <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center text-white">
        <img src="../../images/logo-aplicativo.png" alt="Logo Simulador Ads">
      </div>
      <router-link to="/dashboard" class="text-2xl font-black tracking-tight text-blue-700 dark:text-blue-500 font-headline">Simulador Ads</router-link>
      <div class="hidden md:flex items-center gap-8">
        <router-link to="/dashboard" class="font-bold border-b-2 pb-1 transition-all duration-300" :class="$route.name === 'dashboard' ? 'text-blue-600 dark:text-blue-400 border-blue-600' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-blue-500'">Dashboard</router-link>
        <router-link to="/history" class="font-bold border-b-2 pb-1 transition-all duration-300" :class="$route.name === 'history' ? 'text-blue-600 dark:text-blue-400 border-blue-600' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-blue-500'">Histórico</router-link>
        <router-link v-if="authStore.isAdmin" to="/admin" class="font-bold border-b-2 pb-1 transition-all duration-300" :class="$route.name === 'admin' ? 'text-blue-600 dark:text-blue-400 border-blue-600' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-blue-500'">Admin</router-link>
      </div>
    </div>
    <div class="flex items-center gap-6">
      <div class="flex items-center gap-4">
        <button class="material-symbols-outlined text-slate-500 hover:bg-slate-100 p-2 rounded-full transition-all">notifications</button>
        <router-link to="/settings" class="material-symbols-outlined text-slate-500 hover:bg-slate-100 p-2 rounded-full transition-all flex items-center justify-center">settings</router-link>
      </div>
      <button @click="handleNewSimulation" class="bg-gradient-to-br from-primary to-primary-container text-on-primary px-8 py-3 rounded-xl font-bold hover:scale-95 transition-all duration-150 ease-in-out">
        Nova Simulação
      </button>
      <div class="relative group cursor-pointer inline-block py-2">
        <img alt="User profile avatar" class="w-10 h-10 rounded-full object-cover ring-2 ring-primary/10 relative z-10" :src="`https://ui-avatars.com/api/?name=${authStore.user?.name || 'User'}&background=0D8ABC&color=fff`" />
        <div class="absolute right-0 top-full mt-0 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-outline-variant/10 hidden group-hover:block z-50 pt-2 pb-2">
          <button @click="handleLogout" class="w-full text-left px-4 py-3 text-sm text-error hover:bg-error-container/50 transition-colors font-medium">
            Sair
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useSimulationStore } from '../stores/simulation';

const router = useRouter();
const route = useRoute(); // used implicitly in template via $route
const authStore = useAuthStore();
const simulationStore = useSimulationStore();

const handleNewSimulation = () => {
    simulationStore.$reset();
    if (router.currentRoute.value.name !== 'dashboard') {
        router.push({ name: 'dashboard' });
    }
};

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};
</script>
