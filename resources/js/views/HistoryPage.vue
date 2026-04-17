<template>
  <div class="bg-background text-on-surface min-h-screen">
    <TopAppBar />
    
    <main class="max-w-[1600px] mx-auto px-8 pt-32 pb-12">
      <div class="mb-12">
        <h1 class="text-4xl font-headline font-extrabold tracking-tight text-on-surface mb-2">
          Histórico de Simulações
        </h1>
        <p class="text-on-surface-variant text-lg max-w-2xl font-light">
          Acompanhe suas projeções passadas e compare os cenários esperados.
        </p>
      </div>

      <div v-if="loading" class="flex justify-center py-20">
        <span class="material-symbols-outlined animate-spin text-4xl text-primary">autorenew</span>
      </div>
      
      <div v-else-if="simulations.length === 0" class="bg-surface-container-low rounded-xl p-12 text-center border border-outline-variant/20">
        <span class="material-symbols-outlined text-6xl text-on-surface-variant/40 mb-4">history_toggle_off</span>
        <h3 class="text-xl font-bold font-headline mb-2">Nenhuma simulação salva</h3>
        <p class="text-on-surface-variant">Vá para o Dashboard para criar sua primeira projeção de anúncios.</p>
        <router-link to="/dashboard" class="inline-block mt-6 px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary/90 transition-colors">
          Ir para Dashboard
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="sim in simulations" :key="sim.id" class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow">
          <div class="flex justify-between items-start mb-4">
            <div>
              <span class="inline-block px-2 py-1 bg-primary-container/10 text-primary text-[10px] font-bold uppercase tracking-widest rounded mb-2">
                {{ sim.goal }}
              </span>
              <h3 class="text-xl font-bold tracking-tight">{{ formatCurrency(sim.budget) }}</h3>
            </div>
            <div class="flex gap-2">
                <span class="text-xs text-on-surface-variant flex items-center">{{ formatDate(sim.created_at) }}</span>
                <button @click="deleteSimulation(sim.id)" class="text-error/70 hover:text-error hover:bg-error-container p-1 rounded-md transition-all material-symbols-outlined text-sm">delete</button>
            </div>
          </div>
          
          <div class="space-y-2 mb-6">
            <div class="flex justify-between text-sm">
              <span class="text-on-surface-variant">Período</span>
              <span class="font-medium">{{ sim.campaign_days }} dias</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-on-surface-variant">Maturidade</span>
              <span class="font-medium capitalize">{{ sim.maturity_level }}</span>
            </div>
          </div>
          
          <button @click="loadSimulation(sim)" class="w-full py-2 bg-surface-container hover:bg-surface-container-high transition-colors text-on-surface rounded-lg font-semibold text-sm">
            Ver Detalhes
          </button>
        </div>
      </div>
    </main>

    <BottomNavBar class="md:hidden" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import TopAppBar from '../components/TopAppBar.vue';
import BottomNavBar from '../components/BottomNavBar.vue';
import { useSimulationStore } from '../stores/simulation';

const router = useRouter();
const simulationStore = useSimulationStore();
const simulations = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const data = await simulationStore.loadHistory();
        simulations.value = data;
    } catch (e) {
        console.error("Error loading history", e);
    } finally {
        loading.value = false;
    }
});

const loadSimulation = (sim) => {
    simulationStore.grossBudget = Number(sim.budget);
    simulationStore.paymentType = sim.payment_type || 'Pré-paga PIX';
    simulationStore.taxEnabled = sim.results_json ? sim.results_json.net_budget < sim.budget : true; // fallback
    simulationStore.campaignDays = sim.campaign_days;
    simulationStore.regionId = sim.region_id;
    simulationStore.segmentId = sim.segment_id;
    simulationStore.goal = sim.goal;
    simulationStore.maturityLevel = sim.maturity_level;
    simulationStore.campaignMonth = sim.campaign_month;
    router.push({ name: 'dashboard' });
};

const deleteSimulation = async (id) => {
    if(!confirm('Tem certeza que deseja excluir esta simulação?')) return;
    try {
        await fetch(`/api/simulations/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Accept': 'application/json'
            }
        });
        simulations.value = simulations.value.filter(s => s.id !== id);
    } catch (e) {
        console.error("Error deleting simulation", e);
        alert('Erro ao deletar simulação!');
    }
};

const formatCurrency = (val) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);

const formatDate = (dateString) => {
    const d = new Date(dateString);
    return new Intl.DateTimeFormat('pt-BR', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    }).format(d);
};
</script>
