import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chart-dash.js',
                'resources/js/utils/print-form.js',
                'resources/js/payment.js',
            ],
            refresh: true,
        }),
    ],
});
