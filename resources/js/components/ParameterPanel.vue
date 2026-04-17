<template>
  <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/10">
    <div class="flex items-center gap-3 mb-8">
      <span class="material-symbols-outlined text-primary text-3xl">tune</span>
      <h2 class="text-2xl font-bold tracking-tight">Parâmetros da Campanha</h2>
    </div>
    
    <form class="space-y-6" @submit.prevent="handleSimulate">
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Orçamento Total (R$)</label>
        <input v-model.number="simulationStore.grossBudget" required min="100" class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary transition-all text-on-surface font-medium" placeholder="Ex: 5000,00" type="number" step="0.01" />
        
        <label class="flex items-center gap-2 mt-3 cursor-pointer">
          <input type="checkbox" v-model="simulationStore.taxEnabled" class="text-primary rounded focus:ring-primary border-outline-variant" />
          <span class="text-sm font-medium text-on-surface-variant">Dedução Tributária PIS/Cofins/ISS (12,15%)</span>
        </label>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Tipo de Conta 2026</label>
        <select v-model="simulationStore.paymentType" class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary transition-all text-on-surface font-medium appearance-none">
          <option>Pré-paga PIX</option>
          <option>Pós-paga Cartão</option>
        </select>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Período (Dias)</label>
          <input v-model.number="simulationStore.campaignDays" required min="1" class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary font-medium" type="number" />
        </div>
        <div>
          <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Região</label>
          <select v-model="simulationStore.regionId" required class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary font-medium">
            <option :value="null" disabled>Selecione...</option>
            <option v-for="region in paramStore.regions" :key="region.id" :value="region.id">
              {{ region.name }}
            </option>
          </select>
        </div>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Segmento de Mercado</label>
        <select v-model="simulationStore.segmentId" required class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary font-medium">
          <option :value="null" disabled>Selecione...</option>
          <option v-for="segment in paramStore.segments" :key="segment.id" :value="segment.id">
            {{ segment.name }}
          </option>
        </select>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-4">Objetivo da Campanha</label>
        <div class="grid grid-cols-1 gap-3">
          <label v-for="opt in ['Alcance', 'Tráfego', 'Leads', 'Conversões']" :key="opt" class="flex items-center gap-3 p-4 rounded-lg bg-surface-container-low cursor-pointer hover:bg-surface-container transition-all border" :class="simulationStore.goal === opt ? 'border-primary/50 bg-primary-fixed/30' : 'border-transparent'">
            <input type="radio" :value="opt" v-model="simulationStore.goal" name="objective" class="text-primary focus:ring-primary" />
            <span class="font-medium">{{ opt }}</span>
          </label>
        </div>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-4">Nível de Maturidade da campanha</label>
        <div class="grid grid-cols-1 gap-3">
          <label v-for="lvl in ['Iniciante', 'Intermediário', 'Avançado']" :key="lvl" class="flex items-center gap-3 p-4 rounded-lg bg-surface-container-low cursor-pointer hover:bg-surface-container transition-all border" :class="simulationStore.maturityLevel === lvl ? 'border-primary/50 bg-primary-fixed/30' : 'border-transparent'">
            <input type="radio" :value="lvl" v-model="simulationStore.maturityLevel" name="maturity" class="text-primary focus:ring-primary" />
            <span class="font-medium">{{ lvl }}</span>
          </label>
        </div>
      </div>
      
      <button type="submit" class="w-full bg-primary text-on-primary py-5 rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:shadow-xl hover:translate-y-[-2px] transition-all">
        Simular Resultados
      </button>
    </form>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useSimulationStore } from '../stores/simulation';
import { useParameterStore } from '../stores/parameter';

const simulationStore = useSimulationStore();
const paramStore = useParameterStore();

const handleSimulate = () => {
    // Can emit an event or just do a smooth scroll to results in parent
    const resultSection = document.getElementById('results-section');
    if (resultSection) {
        resultSection.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Optionally trigger save here or on a separate button
    // simulationStore.saveSimulation();
};

onMounted(() => {
    if (!paramStore.loaded) {
        paramStore.fetchParameters().then(() => {
            // Auto-select first if none selected
            if (!simulationStore.regionId && paramStore.regions.length > 0) {
                simulationStore.regionId = paramStore.regions[0].id;
            }
            if (!simulationStore.segmentId && paramStore.segments.length > 0) {
                simulationStore.segmentId = paramStore.segments[0].id;
            }
        });
    }
});
</script>
