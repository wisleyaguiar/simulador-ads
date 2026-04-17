<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Conservador -->
    <div class="bg-red-50/50 rounded-xl p-6 border border-red-100 flex flex-col justify-between">
      <div>
        <div class="flex justify-between items-start mb-6">
          <span class="bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded">Conservador</span>
          <span class="material-symbols-outlined text-red-300">trending_flat</span>
        </div>
        <div class="space-y-4">
          <div>
            <p class="text-xs text-red-900/50 font-bold uppercase tracking-wider">Impressões</p>
            <p class="text-2xl font-black text-red-900 tracking-tight">{{ formatNumber(scenarios.conservative.impressions) }}</p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <p class="text-[10px] text-red-900/50 font-bold uppercase">Cliques</p>
              <p class="text-lg font-bold text-red-900">{{ formatNumber(scenarios.conservative.clicks) }}</p>
            </div>
            <div>
              <p class="text-[10px] text-red-900/50 font-bold uppercase">Leads/Conv</p>
              <p class="text-lg font-bold text-red-900">{{ formatNumber(scenarios.conservative.leads) }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-red-200/50">
        <p class="text-[10px] text-red-900/50 font-bold uppercase mb-1">CPA Estimado</p>
        <p class="text-xl font-black text-red-700">{{ formatCurrency(scenarios.conservative.cpa) }}</p>
      </div>
    </div>
    
    <!-- Realista -->
    <div class="bg-yellow-50/50 rounded-xl p-6 border border-yellow-100 flex flex-col justify-between ring-2 ring-yellow-400/20">
      <div>
        <div class="flex justify-between items-start mb-6">
          <span class="bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded">Realista</span>
          <span class="material-symbols-outlined text-yellow-500" style="font-variation-settings: 'FILL' 1;">stars</span>
        </div>
        <div class="space-y-4">
          <div>
            <p class="text-xs text-yellow-900/50 font-bold uppercase tracking-wider">Impressões</p>
            <p class="text-2xl font-black text-yellow-900 tracking-tight">{{ formatNumber(scenarios.realistic.impressions) }}</p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <p class="text-[10px] text-yellow-900/50 font-bold uppercase">Cliques</p>
              <p class="text-lg font-bold text-yellow-900">{{ formatNumber(scenarios.realistic.clicks) }}</p>
            </div>
            <div>
              <p class="text-[10px] text-yellow-900/50 font-bold uppercase">Leads/Conv</p>
              <p class="text-lg font-bold text-yellow-900">{{ formatNumber(scenarios.realistic.leads) }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-yellow-200/50">
        <p class="text-[10px] text-yellow-900/50 font-bold uppercase mb-1">CPA Estimado</p>
        <p class="text-xl font-black text-yellow-700">{{ formatCurrency(scenarios.realistic.cpa) }}</p>
      </div>
    </div>
    
    <!-- Otimista -->
    <div class="bg-emerald-50/50 rounded-xl p-6 border border-emerald-100 flex flex-col justify-between">
      <div>
        <div class="flex justify-between items-start mb-6">
          <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded">Otimista</span>
          <span class="material-symbols-outlined text-emerald-400">trending_up</span>
        </div>
        <div class="space-y-4">
          <div>
            <p class="text-xs text-emerald-900/50 font-bold uppercase tracking-wider">Impressões</p>
            <p class="text-2xl font-black text-emerald-900 tracking-tight">{{ formatNumber(scenarios.optimistic.impressions) }}</p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <p class="text-[10px] text-emerald-900/50 font-bold uppercase">Cliques</p>
              <p class="text-lg font-bold text-emerald-900">{{ formatNumber(scenarios.optimistic.clicks) }}</p>
            </div>
            <div>
              <p class="text-[10px] text-emerald-900/50 font-bold uppercase">Leads/Conv</p>
              <p class="text-lg font-bold text-emerald-900">{{ formatNumber(scenarios.optimistic.leads) }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-emerald-200/50">
        <p class="text-[10px] text-emerald-900/50 font-bold uppercase mb-1">CPA Estimado</p>
        <p class="text-xl font-black text-emerald-700">{{ formatCurrency(scenarios.optimistic.cpa) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useSimulationStore } from '../stores/simulation';

const simulationStore = useSimulationStore();
const scenarios = computed(() => simulationStore.scenarios);

const formatNumber = (value) => {
    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value >= 1000) return (value / 1000).toFixed(1) + 'k';
    return Math.floor(value || 0).toString();
};

const formatCurrency = (value) => {
    if (!value || !isFinite(value)) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};
</script>
