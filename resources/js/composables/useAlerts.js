import { computed } from 'vue';
import { useParameterStore } from '../stores/parameter';

export function useAlerts() {
    const checkAlerts = (store) => {
        const activeAlerts = [];
        
        // Ensure values exist to avoid errors
        const netBudget = store.netBudget || 0;
        const adjustedCpm = store.seasonalCosts?.cpm || 0;
        const seasonMultiplier = store.seasonMultiplier || 1;
        const seasonLabel = store.seasonLabel || '';
        
        if (netBudget > 0 && adjustedCpm > 0) {
            // Se o orçamento for de teste (ex: menor que 10.000), sugere aumentar para otimização
            if (netBudget < 10000) {
                activeAlerts.push({ type: 'warning', title: 'Orçamento Crítico', message: 'Seu orçamento líquido está abaixo do mínimo recomendado para competitividade no segmento. Considere aumentar 15%.' });
            }
        }
        
        if (store.campaignDays && store.campaignDays < 45) {
            activeAlerts.push({ type: 'tip', title: 'Otimização de Período', message: 'Extender a campanha para 45 dias pode reduzir seu CPA em até 12% devido ao período de aprendizado do algoritmo.' });
        }
        
        if (store.campaignMonth === 11 || store.campaignMonth === 12) {
            activeAlerts.push({ 
                type: 'season', 
                title: 'Sazonalidade Intensa', 
                message: `Custos de mídia aumentados em ${((seasonMultiplier * 100) - 100).toFixed(0)}% devido a ${seasonLabel}.` 
            });
        }
        
        const paramStore = useParameterStore();
        const segment = paramStore.getSegmentById(store.segmentId);
        if (segment && segment.technical_justification) {
            activeAlerts.push({ 
                type: 'insight', 
                title: 'Insight do Especialista', 
                message: segment.technical_justification 
            });
        }
        
        return activeAlerts;
    };

    return { checkAlerts };
}
