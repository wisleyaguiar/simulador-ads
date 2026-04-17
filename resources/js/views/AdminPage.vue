<template>
  <div class="bg-background text-on-surface font-body antialiased flex min-h-screen">
    <AdminSidebar />
    
    <!-- Main Content Area -->
    <main class="flex-1 md:ml-64 min-h-screen pb-12">
      <!-- TopAppBar (Simulated via Header) -->
      <header class="h-20 w-full px-8 flex justify-between items-center bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl sticky top-0 z-40 shadow-sm dark:shadow-none">
        <div class="flex items-center gap-4">
          <div class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter">
            Admin Panel
          </div>
        </div>
        <div class="flex items-center gap-6">
          <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-primary-container">
            <img alt="Admin profile avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=0668e1&color=fff" />
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <div class="px-8 mt-8 max-w-7xl mx-auto">
        <!-- Header Title Section -->
        <div class="mb-12">
          <h1 class="text-4xl font-headline font-extrabold tracking-tight text-on-surface mb-2">
            Importação de Dados <span class="text-primary-container">(Banco de Benchmarks)</span>
          </h1>
          <p class="text-on-surface-variant text-lg max-w-2xl font-light">
            Atualize as médias de mercado do Meta Ads e os dados demográficos do IBGE através de planilhas CSV ou Excel.
          </p>
        </div>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <ImportCard 
            title="Planilha de Segmentos (CTR, CPC e Conversão)" 
            description="Configure os benchmarks globais por nicho de mercado."
            templateUrl="/templates/segmentos-template.csv"
            buttonLabel="Importar Segmentos"
            importType="segmentos"
          />
          
          <ImportCard 
            title="Planilha de Regiões (População e CPM)" 
            description="Defina custos de mídia e demografia regional."
            templateUrl="/templates/regioes-template.csv"
            buttonLabel="Importar Regiões"
            importType="regiões"
          />

          <DataHealthPanel />
        </div>
      </div>
    </main>
    
    <BottomNavBar />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import AdminSidebar from '../components/AdminSidebar.vue';
import BottomNavBar from '../components/BottomNavBar.vue';
import ImportCard from '../components/ImportCard.vue';
import DataHealthPanel from '../components/DataHealthPanel.vue';

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
    if (!authStore.isAdmin) {
        alert('Acesso negado.');
        router.push({ name: 'dashboard' });
    }
});
</script>
