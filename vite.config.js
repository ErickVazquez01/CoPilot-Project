import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
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
    ],
    /*server: {
        host: '0.0.0.0', // Permite conexiones desde otros dispositivos
        port: 5173, // Puerto (puedes cambiarlo si es necesario)
        https: {
            key: fs.readFileSync('./certificates/vite.key'),
            cert: fs.readFileSync('./certificates/vite.crt'),
        },
        hmr: {
            host: process.env.VITE_DEV_SERVER_URL?.replace('https://', '').replace('http://', ''),
            protocol: process.env.VITE_DEV_SERVER_URL?.startsWith('https') ? 'wss' : 'ws',
        },
    },*/
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
