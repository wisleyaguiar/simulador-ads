import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        isAdmin: (state) => state.user?.role === 'admin',
    },
    actions: {
        async register(payload) {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                const data = await response.json();
                this.setToken(data.token);
                this.user = data.user;
                return true;
            }
            return false;
        },
        async login(payload) {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                const data = await response.json();
                this.setToken(data.token);
                this.user = data.user;
                return true;
            }
            return false;
        },
        async logout() {
            if (this.token) {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' }
                });
            }
            this.setToken(null);
            this.user = null;
        },
        async fetchUser() {
            try {
                const response = await fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' }
                });
                if (response.ok) {
                    this.user = await response.json();
                } else {
                    this.setToken(null);
                }
            } catch (e) {
                this.setToken(null);
            }
        },
        setToken(token) {
            this.token = token;
            if (token) {
                localStorage.setItem('auth_token', token);
            } else {
                localStorage.removeItem('auth_token');
            }
        }
    }
});
