import { defineStore } from 'pinia';

export const useParameterStore = defineStore('parameter', {
    state: () => ({
        segments: [],
        regions: [],
        loaded: false,
    }),
    getters: {
        getSegmentById: (state) => (id) => state.segments.find(s => s.id === id),
        getRegionById: (state) => (id) => state.regions.find(r => r.id === id),
    },
    actions: {
        async fetchParameters() {
            try {
                const response = await fetch('/api/parameters', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.segments = data.segments;
                this.regions = data.regions;
                this.loaded = true;
            } catch (error) {
                console.error("Failed to fetch parameters", error);
            }
        }
    }
});
