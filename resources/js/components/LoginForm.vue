<template>
  <div>
    <form class="space-y-6" @submit.prevent="handleSubmit">
      <div class="space-y-2">
        <label class="text-sm font-semibold text-on-surface-variant ml-1" for="email">E-mail</label>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-[20px]">mail</span>
          <input v-model="email" required type="email" id="email" placeholder="seu@email.com" class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-md focus:ring-2 focus:ring-primary transition-all duration-200 text-on-surface placeholder:text-outline/60" />
        </div>
      </div>
      
      <div class="space-y-2">
        <div class="flex justify-between items-center px-1">
          <label class="text-sm font-semibold text-on-surface-variant" for="password">Senha</label>
          <a v-if="!isRegister" class="text-xs font-bold text-primary hover:underline" href="#">Esqueceu a senha?</a>
        </div>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-[20px]">lock</span>
          <input v-model="password" required :type="showPassword ? 'text' : 'password'" id="password" placeholder="••••••••" class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-md focus:ring-2 focus:ring-primary transition-all duration-200 text-on-surface placeholder:text-outline/60" />
          <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary">
            <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
          </button>
        </div>
      </div>

      <div v-if="isRegister" class="space-y-2">
        <label class="text-sm font-semibold text-on-surface-variant ml-1" for="name">Nome completo</label>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-[20px]">person</span>
          <input v-model="name" required type="text" id="name" placeholder="Seu nome" class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-md focus:ring-2 focus:ring-primary transition-all duration-200 text-on-surface placeholder:text-outline/60" />
        </div>
      </div>
      
      <p v-if="error" class="text-sm text-error mt-2 font-medium">{{ error }}</p>

      <button type="submit" :disabled="loading" class="w-full gradient-button text-white font-bold py-4 rounded-xl shadow-[0_24px_48px_-12px_rgba(25,27,35,0.08)] hover:scale-[1.02] active:scale-95 transition-all duration-200 text-lg opacity-100 disabled:opacity-70 disabled:hover:scale-100">
        <span v-if="loading">Aguarde...</span>
        <span v-else>{{ isRegister ? 'Criar conta grátis' : 'Entrar' }}</span>
      </button>

      <div class="relative py-4 flex items-center">
        <div class="flex-grow border-t border-outline-variant/30"></div>
        <span class="flex-shrink mx-4 text-xs font-bold text-outline uppercase tracking-widest">ou</span>
        <div class="flex-grow border-t border-outline-variant/30"></div>
      </div>

      <button type="button" class="w-full flex items-center justify-center gap-3 py-4 bg-surface-container-lowest border border-outline-variant/30 rounded-xl font-semibold text-on-surface hover:bg-surface-container-low transition-colors duration-200">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"></path>
        </svg>
        Continuar com o Google
      </button>
    </form>

    <footer class="mt-12 text-center">
      <p class="text-sm text-on-surface-variant font-medium">
        {{ isRegister ? 'Já possui uma conta?' : 'Ainda não possui conta?' }} 
        <button @click="toggleMode" class="text-primary font-bold hover:underline ml-1">
          {{ isRegister ? 'Fazer login' : 'Criar conta grátis' }}
        </button>
      </p>
      <p class="mt-8 text-[10px] text-outline leading-relaxed max-w-xs mx-auto">
        Ao continuar, você concorda com nossos <a href="#" class="underline hover:text-primary">Termos de Uso</a> e <a href="#" class="underline hover:text-primary">Política de Privacidade</a>.
      </p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const isRegister = ref(true);
const email = ref('');
const password = ref('');
const name = ref('');
const showPassword = ref(false);
const loading = ref(false);
const error = ref('');

const toggleMode = () => {
    isRegister.value = !isRegister.value;
    error.value = '';
    password.value = '';
};

const handleSubmit = async () => {
    loading.value = true;
    error.value = '';
    
    try {
        let success = false;
        
        if (isRegister.value) {
            success = await authStore.register({
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: password.value
            });
        } else {
            success = await authStore.login({
                email: email.value,
                password: password.value
            });
        }
        
        if (success) {
            if (authStore.isAdmin) {
                router.push({ name: 'admin' });
            } else {
                router.push({ name: 'dashboard' });
            }
        } else {
            error.value = isRegister.value 
                ? 'Não foi possível realizar o cadastro. Verifique os dados.' 
                : 'Credenciais inválidas. Tente novamente.';
        }
    } catch (e) {
        error.value = 'Ocorreu um erro de comunicação com o servidor.';
    } finally {
        loading.value = false;
    }
};
</script>
