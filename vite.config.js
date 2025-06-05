import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite'


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),

        vue()
    ],
    server: {
        host: '192.168.5.250',
        port: 5173,
        cors: {
            origin: 'http://192.168.5.250:8000',
            credentials: true,
        },
    },
});
