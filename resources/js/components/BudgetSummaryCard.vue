<template>
  <div class="relative overflow-hidden bg-gradient-to-br from-primary to-primary-container rounded-xl p-10 text-on-primary shadow-2xl">
    <div class="relative z-10 grid md:grid-cols-3 gap-8 items-center">
      <div>
        <p class="text-primary-fixed/80 font-medium text-sm mb-1 uppercase tracking-widest">Orçamento Bruto</p>
        <h3 class="text-3xl font-extrabold tracking-tighter">{{ formatCurrency(simulationStore.grossBudget) }}</h3>
      </div>
      
      <div v-if="simulationStore.taxEnabled" class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10">
        <div class="flex items-center gap-2 mb-1">
          <span class="material-symbols-outlined text-sm">info</span>
          <p class="text-white/70 font-medium text-xs uppercase tracking-widest">Dedução Tributária (12.15%)</p>
        </div>
        <h3 class="text-2xl font-bold tracking-tight">- {{ formatCurrency(taxAmount) }}</h3>
      </div>
      <div v-else class="bg-white/5 rounded-2xl p-6 border border-white/5 opacity-50">
        <p class="text-white/50 font-medium text-xs uppercase tracking-widest">Dedução Desativada</p>
      </div>
      
      <div class="text-right">
        <p class="text-primary-fixed/80 font-medium text-sm mb-1 uppercase tracking-widest">Orçamento Real (Líquido)</p>
        <h3 class="text-5xl font-black tracking-tighter text-white">{{ formatCurrency(simulationStore.netBudget) }}</h3>
      </div>
    </div>
    
    <!-- Decorative background element -->
    <div class="absolute top-[-50%] right-[-10%] w-[400px] h-[400px] bg-white/5 rounded-full blur-3xl"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useSimulationStore } from '../stores/simulation';

const simulationStore = useSimulationStore();

const taxAmount = computed(() => {
    return simulationStore.grossBudget - simulationStore.netBudget;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};
</script>
