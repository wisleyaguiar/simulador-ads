import { defineStore } from 'pinia';
import { useCalculator } from '../composables/useCalculator';
import { useSeasonality } from '../composables/useSeasonality';
import { useParameterStore } from './parameter';

export const useSimulationStore = defineStore('simulation', {
    state: () => ({
        grossBudget: 5000,
        taxEnabled: true,
        paymentType: 'Pré-paga PIX',
        campaignDays: 30,
        regionId: null,
        segmentId: null,
        goal: 'Leads',
        maturityLevel: 'Iniciante',
        campaignMonth: new Date().getMonth() + 1,
    }),
    getters: {
        netBudget() {
            const { calculateNetBudget } = useCalculator();
            return calculateNetBudget(this.grossBudget, this.taxEnabled);
        },
        seasonLabel() {
            const { getSeasonLabel } = useSeasonality();
            return getSeasonLabel(this.campaignMonth);
        },
        seasonMultiplier() {
            const { getMultiplier } = useSeasonality();
            return getMultiplier(this.campaignMonth);
        },
        seasonalCosts() {
            const paramStore = useParameterStore();
            const region = paramStore.getRegionById(this.regionId);
            const segment = paramStore.getSegmentById(this.segmentId);
            if (!region || !segment) return { cpm: 0, cpc: 0 };
            
            const { applySeasonality } = useCalculator();
            return applySeasonality(region.avg_cpm, segment.avg_cpc, this.campaignMonth);
        },
        adjustedMetrics() {
            const paramStore = useParameterStore();
            const segment = paramStore.getSegmentById(this.segmentId);
            if (!segment) return { ctr: 0, convRate: 0 };
            
            const { applyMaturityMultiplier, applyDaysMultiplier } = useCalculator();
            const maturity = applyMaturityMultiplier(segment.avg_ctr, segment.avg_conversion_rate, this.maturityLevel);
            return applyDaysMultiplier(maturity.ctr, maturity.convRate, this.campaignDays);
        },
        scenarios() {
            const { generateScenarios, applyGoalMultiplier } = useCalculator();
            
            // Adjust costs based on goal
            const goalAdjusted = applyGoalMultiplier(
                this.seasonalCosts.cpm, 
                this.adjustedMetrics.ctr,
                this.adjustedMetrics.convRate,
                this.goal
            );

            return generateScenarios(
                this.netBudget, 
                goalAdjusted.cpm, 
                goalAdjusted.ctr, 
                goalAdjusted.convRate
            );
        }
    },
    actions: {
        async saveSimulation() {
            const payload = {
                budget: this.grossBudget,
                payment_type: this.paymentType,
                campaign_days: this.campaignDays,
                region_id: this.regionId,
                segment_id: this.segmentId,
                goal: this.goal,
                maturity_level: this.maturityLevel,
                campaign_month: this.campaignMonth,
                deduct_tax: this.taxEnabled
            };
            
            const response = await fetch('/api/simulations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            return response.ok;
        },
        async loadHistory() {
            const response = await fetch('/api/simulations', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });
            return await response.json();
        }
    }
});
