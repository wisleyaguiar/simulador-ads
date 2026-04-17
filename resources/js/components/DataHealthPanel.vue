<template>
  <div class="lg:col-span-2 mt-8">
    <div class="bg-surface-container-low rounded-xl p-8 border border-outline-variant/20 flex flex-col md:flex-row items-center gap-8">
      <div class="md:w-1/3">
        <h3 class="text-2xl font-headline font-bold text-on-surface mb-2">Integridade do Banco</h3>
        <p class="text-on-surface-variant text-sm">
          Última atualização geral realizada há {{ daysSinceUpdate }} dias. Mantenha os dados sincronizados para simulações precisas.
        </p>
      </div>
      <div class="md:w-2/3 grid grid-cols-3 gap-4 w-full">
        <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/10 text-center dark:bg-white/5">
          <p class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant mb-1">Média CPM</p>
          <p class="text-2xl font-headline font-extrabold text-primary">{{ formatCurrency(avgCpm) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/10 text-center dark:bg-white/5">
          <p class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant mb-1">Média CTR</p>
          <p class="text-2xl font-headline font-extrabold text-primary">{{ (avgCtr * 100).toFixed(2) }}%</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/10 text-center dark:bg-white/5">
          <p class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant mb-1">Confidence Score</p>
          <p class="text-2xl font-headline font-extrabold" :class="confidenceColor">{{ confidenceScore }}%</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useParameterStore } from '../stores/parameter';

const paramStore = useParameterStore();

// Some simple mock logic for the health panel metrics based on the generic store.
const avgCpm = computed(() => {
    if (paramStore.regions.length === 0) return 0;
    const sum = paramStore.regions.reduce((acc, r) => acc + Number(r.avg_cpm || 0), 0);
    return sum / paramStore.regions.length;
});

const avgCtr = computed(() => {
    if (paramStore.segments.length === 0) return 0;
    const sum = paramStore.segments.reduce((acc, s) => acc + Number(s.avg_ctr || 0), 0);
    return sum / paramStore.segments.length;
});

const confidenceScore = computed(() => {
    // Arbitrary metric logic representing data health, higher if we have many parameters
    const base = 70;
    const bonus = Math.min(30, (paramStore.segments.length + paramStore.regions.length));
    return base + bonus;
});

const confidenceColor = computed(() => {
    if (confidenceScore.value >= 90) return 'text-emerald-500';
    if (confidenceScore.value >= 70) return 'text-yellow-500';
    return 'text-error';
});

const daysSinceUpdate = 2; // Fixed for MVP

const formatCurrency = (val) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);

onMounted(() => {
    if (!paramStore.loaded) {
        paramStore.fetchParameters();
    }
});
</script>
