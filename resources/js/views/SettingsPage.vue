<template>
  <div class="bg-background text-on-surface min-h-screen">
    <TopAppBar />
    
    <main class="max-w-4xl mx-auto px-8 pt-32 pb-12">
      <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Configurações da Conta</h1>
        <p class="text-on-surface-variant">Gerencie seus dados pessoais, plano atual e segurança.</p>
      </div>

      <div class="grid grid-cols-1 gap-8">
        <!-- Dados do Usuário -->
        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/10">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-2xl">person</span>
            <h2 class="text-xl font-bold">Dados do Perfil</h2>
          </div>
          
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nome Completo</label>
                <input v-model="form.name" type="text" class="w-full bg-surface-container-low border-none rounded-lg p-3 focus:ring-2 focus:ring-primary" required />
              </div>
              <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">E-mail</label>
                <input v-model="form.email" type="email" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-on-surface-variant cursor-not-allowed" disabled />
                <p class="text-xs text-on-surface-variant mt-1">O e-mail não pode ser alterado no momento.</p>
              </div>
            </div>
            
            <div class="flex justify-end mt-4">
              <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-bold hover:bg-primary/90 transition-all">Salvar Alterações</button>
            </div>
          </form>
        </div>

        <!-- Segurança -->
        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/10">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-2xl">lock</span>
            <h2 class="text-xl font-bold">Segurança e Senha</h2>
          </div>
          
          <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Senha Atual</label>
              <input v-model="pass.current" type="password" class="w-full bg-surface-container-low border-none rounded-lg p-3 focus:ring-2 focus:ring-primary" required />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nova Senha</label>
                <input v-model="pass.new" type="password" class="w-full bg-surface-container-low border-none rounded-lg p-3 focus:ring-2 focus:ring-primary" required />
              </div>
              <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Confirmar Nova Senha</label>
                <input v-model="pass.confirm" type="password" class="w-full bg-surface-container-low border-none rounded-lg p-3 focus:ring-2 focus:ring-primary" required />
              </div>
            </div>
            
            <div class="flex justify-end mt-4">
              <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-bold hover:bg-primary/90 transition-all">Atualizar Senha</button>
            </div>
          </form>
        </div>

        <!-- Plano -->
        <div class="bg-gradient-to-br from-primary-container to-surface flex justify-between items-center rounded-xl p-8 shadow-sm border border-primary/20">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <span class="material-symbols-outlined text-primary text-2xl">star</span>
              <h2 class="text-xl font-bold">Meu Plano</h2>
            </div>
            <p class="text-on-surface-variant font-medium">Você está no plano <strong class="text-primary">Pro Ilimitado</strong></p>
          </div>
          <button class="bg-primary-fixed text-on-primary-fixed px-6 py-3 rounded-lg font-bold hover:scale-[1.02] transition-all editorial-shadow disabled:opacity-50" disabled>
            Gerenciar Assinatura
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import TopAppBar from '../components/TopAppBar.vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

const form = reactive({
    name: '',
    email: ''
});

const pass = reactive({
    current: '',
    new: '',
    confirm: ''
});

onMounted(() => {
    if (authStore.user) {
        form.name = authStore.user.name;
        form.email = authStore.user.email;
    }
});

const updateProfile = () => {
    alert('Dados de perfil atualizados com sucesso! (Modo Simulação)');
};

const updatePassword = () => {
    if (pass.new !== pass.confirm) {
        alert('As senhas não coincidem!');
        return;
    }
    alert('Senha atualizada com sucesso! (Modo Simulação)');
    pass.current = ''; pass.new = ''; pass.confirm = '';
};
</script>
