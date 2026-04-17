<template>
  <main class="flex flex-col md:flex-row w-full min-h-screen overflow-hidden">
    <!-- Left Side: Branded Graphic -->
    <section class="hidden md:flex md:w-1/2 lg:w-[55%] relative overflow-hidden bg-primary-container items-center justify-center p-12">
      <div class="absolute inset-0 opacity-40">
        <img alt="Marketing ROI Growth Visualization" class="w-full h-full object-cover mix-blend-overlay" data-alt="Modern data visualization dashboard with 3D floating bar charts and growth curves in shades of corporate blue and white, editorial style" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU_Z5ysIcxj5KwazzjGtLx0Z13Oxzm4lDF60RraFaTZDaiBHgXx0ktEFfejoDhXxMWx0YwXGtKB11wqHCW1kp5HpmAIfLCLKxterSNmAhd4NigC-mku_AiQloUwWVp4kZKd3svH0ftSXQKqPX0L_CBvLUcweQTxt0qV3KZGHPw0UzOLG7z7F0zlL7Slk2oQ1VUY-wSCX_I6uPVEsnp9dyz-SasoptTeOzwYNzSuHjijNxPpJm_jaC3uhTtqNZvoE85-gsNnPbr5nFH"/>
      </div>
      <!-- Asymmetrical Decorative Elements -->
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary rounded-full blur-[100px] opacity-50"></div>
      <div class="absolute -bottom-48 -right-48 w-[500px] h-[500px] bg-secondary-container rounded-full blur-[120px] opacity-30"></div>
      <div class="relative z-10 max-w-xl">
        <div class="mb-12">
          <span class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md text-white border border-white/20 text-sm font-medium tracking-wide">
            <span class="material-symbols-outlined text-[18px]">verified</span>
            Inteligência Preditiva para Gestores
          </span>
        </div>
        <h2 class="text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-8 tracking-tight font-headline">
          Domine o futuro das suas campanhas.
        </h2>
        <p class="text-xl text-blue-50/80 leading-relaxed font-body mb-12">
          Acesse o Simulador Ads e visualize a escala do seu ROI antes mesmo de configurar o Gerenciador de Anúncios.
        </p>
        <!-- Metric Glass Card -->
        <div class="glass-panel p-8 rounded-xl border border-white/20 editorial-shadow max-w-md">
          <div class="flex items-center justify-between mb-6">
            <div class="flex flex-col">
              <span class="text-on-surface-variant font-label text-xs uppercase tracking-widest mb-1">Crescimento Estimado</span>
              <span class="text-4xl font-bold text-primary tracking-tighter">+245%</span>
            </div>
            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">trending_up</span>
            </div>
          </div>
          <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
            <div class="bg-primary h-full w-[75%] rounded-full"></div>
          </div>
          <div class="mt-4 flex justify-between text-xs text-on-surface-variant font-medium">
            <span>Investimento: R$ 5k</span>
            <span>Retorno: R$ 17.5k</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Right Side: Login/Signup Form -->
    <section class="flex-1 flex flex-col items-center justify-center p-8 md:p-16 lg:p-24 bg-surface h-full">
      <div class="w-full max-w-md">
        <!-- Brand Anchor -->
        <header class="mb-12">
          <div class="flex items-center gap-2 mb-8">
            <div class="w-10 h-10 gradient-button !px-0 rounded-xl flex items-center justify-center">
              <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">analytics</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-primary font-headline">Simulador Ads</h1>
          </div>
          <h2 class="text-3xl font-extrabold text-on-surface leading-tight mb-3 font-headline">
            Preveja o resultado das suas campanhas antes de investir
          </h2>
          <p class="text-on-surface-variant font-medium">
            Acesse as melhores estimativas do mercado
          </p>
        </header>

        <LoginForm />
        
      </div>
    </section>
  </main>
</template>

<script setup>
import LoginForm from '../components/LoginForm.vue';
</script>
