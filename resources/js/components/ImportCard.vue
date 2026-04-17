<template>
  <section class="bg-surface-container-lowest rounded-xl p-8 shadow-xl shadow-slate-200/50 flex flex-col gap-6 dark:shadow-none">
    <div class="flex flex-col gap-1">
      <h2 class="text-xl font-headline font-bold text-on-surface">{{ title }}</h2>
      <p class="text-sm text-on-surface-variant">{{ description }}</p>
    </div>
    <a class="inline-flex items-center gap-2 text-primary font-bold text-sm hover:underline w-fit" :href="templateUrl" target="_blank">
      <span class="material-symbols-outlined text-lg" data-icon="download">download</span>
      Baixar Template Padrão (.csv)
    </a>
    
    <UploadZone @file-selected="file = $event" />
    
    <StatusBanner v-if="status" :is-success="status.success" :message="status.message" />
    
    <div class="flex justify-end mt-4">
      <button @click="handleImport" :disabled="!file || loading" class="bg-gradient-to-br from-primary to-primary-container text-white px-8 py-4 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-95 transition-all duration-300 disabled:opacity-50 disabled:hover:scale-100">
        {{ loading ? 'Importando...' : buttonLabel }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import UploadZone from './UploadZone.vue';
import StatusBanner from './StatusBanner.vue';

const props = defineProps({
    title: String,
    description: String,
    templateUrl: String,
    buttonLabel: String,
    importType: String // 'segments' or 'regions'
});

const file = ref(null);
const loading = ref(false);
const status = ref(null);

const handleImport = async () => {
    if (!file.value) return;
    loading.value = true;
    status.value = null;
    
    try {
        const formData = new FormData();
        formData.append('file', file.value);

        const endpoint = (props.importType === 'segmentos' || props.importType === 'segments')
            ? '/api/admin/import/segments' 
            : '/api/admin/import/regions';
            
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Falha na importação');
        }

        status.value = {
            success: true,
            message: `Sucesso! Base de ${props.importType} atualizada no banco.`
        };
        
        // Recarregar os parâmetros caso a loja esteja ativa (ou reload simples para garantir os novos dados no dashboard)
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 2000);

    } catch (e) {
        status.value = {
            success: false,
            message: 'Erro: Colunas originais ausentes ou formato inválido.'
        };
    } finally {
        loading.value = false;
        file.value = null;
    }
};
</script>
