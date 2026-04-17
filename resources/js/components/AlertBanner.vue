<template>
  <div class="bg-surface-container-low rounded-xl p-8" v-if="alerts.length > 0">
    <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">auto_awesome</span>
      Alertas Inteligentes e Sugestões
    </h3>
    <div class="grid md:grid-cols-2 gap-4">
      
      <div v-for="(alert, index) in alerts" :key="index" class="flex gap-4 p-5 bg-white rounded-xl shadow-sm">
        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" :class="{
          'bg-yellow-100': alert.type === 'warning',
          'bg-emerald-100': alert.type === 'tip' || alert.type === 'season',
          'bg-blue-100': alert.type === 'insight'
        }">
          <span class="material-symbols-outlined" :class="{
            'text-yellow-600': alert.type === 'warning',
            'text-emerald-600': alert.type === 'tip' || alert.type === 'season',
            'text-blue-600': alert.type === 'insight'
          }" style="font-variation-settings: 'FILL' 1;">
            {{ alert.type === 'warning' ? 'warning' : (alert.type === 'insight' ? 'tips_and_updates' : 'lightbulb') }}
          </span>
        </div>
        <div>
          <h4 class="font-bold text-on-surface mb-1">{{ alert.title }}</h4>
          <p class="text-sm text-on-surface-variant leading-relaxed">{{ alert.message }}</p>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAlerts } from '../composables/useAlerts';
import { useSimulationStore } from '../stores/simulation';

const simulationStore = useSimulationStore();
const { checkAlerts } = useAlerts();

const alerts = computed(() => {
    return checkAlerts(simulationStore);
});
</script>
