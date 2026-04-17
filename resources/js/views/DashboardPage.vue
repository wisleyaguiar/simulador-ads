<template>
  <div class="bg-background text-on-surface min-h-screen">
    <TopAppBar />
    
    <main class="max-w-[1600px] mx-auto px-8 pt-32 pb-12">
      <div class="grid grid-cols-1 lg:grid-cols-[35%_65%] gap-8">
        
        <!-- Left Column: Input Panel -->
        <aside class="space-y-8">
          <ParameterPanel />
        </aside>
        
        <!-- Right Column: Results Panel -->
        <section id="results-section" class="space-y-8">
          
          <div class="flex justify-end items-center gap-3 mb-4 dropdown-container">
            <button @click="handleExportCSV" class="flex items-center gap-2 px-4 py-2 text-xs font-bold uppercase tracking-wider text-on-surface-variant hover:bg-surface-container rounded-lg transition-all border border-outline-variant/30">
              <span class="material-symbols-outlined text-sm">description</span>
              Excel
            </button>
            <button @click="handlePrint" class="flex items-center gap-2 px-4 py-2 text-xs font-bold uppercase tracking-wider text-on-surface-variant hover:bg-surface-container rounded-lg transition-all border border-outline-variant/30">
              <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
              PDF
            </button>
            <button @click="saveSim" :disabled="saving" class="flex items-center gap-2 px-4 py-2 text-xs font-bold uppercase tracking-wider text-primary hover:bg-primary-fixed/30 rounded-lg transition-all border border-primary/30">
              <span class="material-symbols-outlined text-sm">save</span>
              {{ saving ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
          
          <BudgetSummaryCard />
          <ScenarioCard />
          <AlertBanner />
          <MetricGlassCard />
          
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import TopAppBar from '../components/TopAppBar.vue';
import ParameterPanel from '../components/ParameterPanel.vue';
import BudgetSummaryCard from '../components/BudgetSummaryCard.vue';
import ScenarioCard from '../components/ScenarioCard.vue';
import AlertBanner from '../components/AlertBanner.vue';
import MetricGlassCard from '../components/MetricGlassCard.vue';
import { useSimulationStore } from '../stores/simulation';

const simulationStore = useSimulationStore();
const saving = ref(false);

const handlePrint = () => {
    window.print();
};

const handleExportCSV = () => {
    const sc = simulationStore.scenarios;
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Cenario,Impressoes,Cliques,Leads,CPA (R$)\n";
    csvContent += `Conservador,${sc.conservative.impressions},${sc.conservative.clicks},${sc.conservative.leads},${sc.conservative.cpa}\n`;
    csvContent += `Realista,${sc.realistic.impressions},${sc.realistic.clicks},${sc.realistic.leads},${sc.realistic.cpa}\n`;
    csvContent += `Otimista,${sc.optimistic.impressions},${sc.optimistic.clicks},${sc.optimistic.leads},${sc.optimistic.cpa}\n`;

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "simulacao-ads.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const saveSim = async () => {
    saving.value = true;
    try {
        const success = await simulationStore.saveSimulation();
        if (success) {
            alert('Simulação salva no histórico!');
        } else {
            alert('Erro ao salvar a simulação.');
        }
    } catch {
        alert('Erro ao se conectar com a API.');
    } finally {
        saving.value = false;
    }
};
</script>
