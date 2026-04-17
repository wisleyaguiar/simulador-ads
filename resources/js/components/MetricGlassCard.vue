<template>
  <div class="relative h-64 rounded-xl overflow-hidden shadow-lg mt-8">
    <img alt="Analytics visualization" class="w-full h-full object-cover" data-alt="abstract digital visualization of rising data bars and glowing network nodes in a deep blue and cyan space" src="https://lh3.googleusercontent.com/aida-public/AB6AXuChXR8yxnUzzy5yqLl1D8JpKPBrrSoikqaCpUbzWIAcJN6p8o7abdsBu7cYlXnt-kYVxqydsl_88gBfu08zFEfVChfop1tcuwc4K_eDIpoStmPqzl-uOmeRMVM5tItD7_zYydc1vR-apgqu05FFRE1YEiFSsTvuLap-y_LGYFefnLJkGlBoKM9ZcWlabrCXh7DVOB2vG5eKxuaRKyuemrR7Z-ygY9n8Z5fWDCvW1WPyiQJKe4zyV4Zf26wYECHZZsa8IojYY-dPypxk"/>
    
    <div class="absolute inset-0 bg-gradient-to-r from-on-surface/80 to-transparent flex items-center px-12">
      <div class="glass-metric p-8 rounded-2xl max-w-xs border border-white/20">
        <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-2">Alcance Estimado Total</p>
        <p class="text-4xl font-black text-on-surface tracking-tighter">{{ formatNumber(totalReach) }}+</p>
        <p class="text-xs text-on-surface-variant mt-2">Pessoas únicas impactadas dentro do seu segmento.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useSimulationStore } from '../stores/simulation';

const simulationStore = useSimulationStore();
const scenarios = computed(() => simulationStore.scenarios);

const totalReach = computed(() => {
    // Estimating reach as ~70% of optimistic impressions for visual appeal
    if (!scenarios.value.optimistic) return 0;
    return Math.floor(scenarios.value.optimistic.impressions * 0.7);
});

const formatNumber = (value) => {
    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value >= 1000) return (value / 1000).toFixed(1) + 'k';
    return Math.floor(value || 0).toString();
};
</script>
