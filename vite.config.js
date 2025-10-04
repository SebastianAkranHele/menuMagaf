import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/menuDigital/',  // <-- Adicione esta linha
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home.css',
                'resources/css/menu.css',
                'resources/css/admin.css',
                'resources/css/homehero-index.css',
                'resources/js/app.js',
                'resources/js/home.js',
                'resources/js/menu.js',
                'resources/js/admin.js',
                'resources/js/homehero-index.js'
            ],
            refresh: true,
        }),
    ],
});
