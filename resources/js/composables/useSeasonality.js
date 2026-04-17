export function useSeasonality() {
    const SEASONALITY_MULTIPLIERS = {
        1: 0.85, 2: 1.0, 3: 1.0, 4: 1.0, 5: 1.0, 6: 1.0,
        7: 1.0, 8: 1.0, 9: 1.0, 10: 1.0, 11: 1.5, 12: 1.3,
    };

    const getMultiplier = (month) => SEASONALITY_MULTIPLIERS[month] || 1.0;

    const getSeasonLabel = (month) => {
        if (month === 11) return 'Black Friday';
        if (month === 12) return 'Natal';
        if (month === 1) return 'Ressaca Comercial';
        return 'Normal';
    };

    const isHighSeason = (month) => month === 11 || month === 12;

    return {
        getMultiplier,
        getSeasonLabel,
        isHighSeason
    };
}
