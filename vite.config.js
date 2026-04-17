import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/images/logo-aplicativo.png', 'resources/images/fundo-esquerda-login.png', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public',
            scope: '/',
            base: '/',
            manifest: {
                name: 'Simulador Ads',
                short_name: 'Simulador',
                start_url: '/',
                scope: '/',
                theme_color: '#0668E1',
                background_color: '#faf9ff',
                display: 'standalone',
                orientation: 'portrait',
                lang: 'pt-BR',
                icons: [
                    {
                        src: '/icons/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/icons/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable'
                    }
                ]
            },
            workbox: {
                navigateFallback: null,
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: { maxEntries: 30, maxAgeSeconds: 60 * 60 * 24 * 365 }
                        }
                    },
                    {
                        urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'gstatic-fonts-cache',
                            expiration: { maxEntries: 30, maxAgeSeconds: 60 * 60 * 24 * 365 }
                        }
                    },
                    {
                        urlPattern: /\/api\//i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            expiration: { maxEntries: 50, maxAgeSeconds: 60 * 5 }
                        }
                    },
                    {
                        urlPattern: /\/build\/assets\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'build-assets-cache',
                            expiration: { maxEntries: 60, maxAgeSeconds: 60 * 60 * 24 * 30 }
                        }
                    }
                ]
            }
        })
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
