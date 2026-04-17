import { useSeasonality } from './useSeasonality';

export function useCalculator() {
    const { getMultiplier } = useSeasonality();

    const calculateNetBudget = (grossBudget, taxEnabled) => {
        return taxEnabled ? grossBudget * (1 - 0.1215) : grossBudget;
    };

    const applySeasonality = (cpm, cpc, month) => {
        const multiplier = getMultiplier(month);
        return {
            cpm: cpm * multiplier,
            cpc: cpc * multiplier
        };
    };

    const applyMaturityMultiplier = (baseCtr, baseConvRate, maturityLevel) => {
        if (maturityLevel === 'Iniciante') {
            return { ctr: baseCtr * 0.85, convRate: baseConvRate * 0.85 };
        }
        if (maturityLevel === 'Avançado') {
            return { ctr: baseCtr * 1.10, convRate: baseConvRate * 1.10 };
        }
        return { ctr: baseCtr, convRate: baseConvRate };
    };

    const applyDaysMultiplier = (baseCtr, baseConvRate, days) => {
        // Algorithm optimization efficiency based on campaign duration
        if (days < 15) {
            return { ctr: baseCtr * 0.85, convRate: baseConvRate * 0.85 }; // Poor optimization
        }
        if (days >= 45) {
            return { ctr: baseCtr * 1.15, convRate: baseConvRate * 1.15 }; // Excellent optimization
        }
        return { ctr: baseCtr, convRate: baseConvRate }; // Standard (15 - 44 days)
    };

    const applyGoalMultiplier = (cpm, ctr, convRate, goal) => {
        if (goal === 'Alcance') {
            return { cpm: cpm * 0.6, ctr: ctr * 0.7, convRate: convRate * 0.4 };
        }
        if (goal === 'Conversões') {
            return { cpm: cpm * 1.4, ctr: ctr * 1.1, convRate: convRate * 1.5 };
        }
        if (goal === 'Tráfego') {
            return { cpm: cpm * 0.9, ctr: ctr * 1.3, convRate: convRate * 0.8 };
        }
        // Leads (default)
        return { cpm, ctr, convRate };
    };

    const calculateMetrics = (budget, adjustedCpm, adjustedCtr, adjustedConvRate) => {
        const impressions = adjustedCpm > 0 ? (budget / adjustedCpm) * 1000 : 0;
        const clicks = impressions * adjustedCtr;
        const leads = clicks * adjustedConvRate;
        const cpa = leads > 0 ? budget / leads : 0;
        
        return {
            impressions: Math.round(impressions),
            clicks: Math.round(clicks),
            leads: Math.round(leads),
            cpa: Math.round(cpa * 100) / 100
        };
    };

    const generateScenarios = (budget, adjustedCpm, adjustedCtr, adjustedConvRate) => {
        return {
            conservative: calculateMetrics(budget, adjustedCpm, adjustedCtr * 0.8, adjustedConvRate * 0.8),
            realistic: calculateMetrics(budget, adjustedCpm, adjustedCtr * 1.0, adjustedConvRate * 1.0),
            optimistic: calculateMetrics(budget, adjustedCpm, adjustedCtr * 1.3, adjustedConvRate * 1.3),
        };
    };

    return {
        calculateNetBudget,
        applySeasonality,
        applyMaturityMultiplier,
        applyDaysMultiplier,
        applyGoalMultiplier,
        calculateMetrics,
        generateScenarios
    };
}
