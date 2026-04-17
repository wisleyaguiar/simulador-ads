import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from '../views/LoginPage.vue';
import DashboardPage from '../views/DashboardPage.vue';
import AdminPage from '../views/AdminPage.vue';
import HistoryPage from '../views/HistoryPage.vue';
import SettingsPage from '../views/SettingsPage.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', component: LoginPage, name: 'login' },
    { path: '/dashboard', component: DashboardPage, name: 'dashboard', meta: { requiresAuth: true } },
    { path: '/settings', component: SettingsPage, name: 'settings', meta: { requiresAuth: true } },
    { path: '/admin', component: AdminPage, name: 'admin', meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/history', component: HistoryPage, name: 'history', meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    // Guards
    const authStore = useAuthStore();
    
    // Allow login page access if logged out, or redirect if logged in
    if (to.name === 'login' && authStore.isAuthenticated) {
        return next({ name: 'dashboard' });
    }
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        if (localStorage.getItem('auth_token')) {
            await authStore.fetchUser();
        }
        
        if (!authStore.isAuthenticated) {
            return next({ name: 'login' });
        }
    }
    
    if (to.meta.requiresAdmin && authStore.user?.role !== 'admin') {
        return next({ name: 'dashboard' });
    }
    
    next();
});

export default router;
