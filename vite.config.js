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
                'resources/js/data/customer/customer-list-simple.js',
                'resources/js/data/customer/customer-details-data.js',
                'resources/js/data/consumer/consumer-list.js',
                'resources/js/data/billing/billing.js',
                'resources/js/utils/action-functions.js',
                'resources/js/utils/report-export.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        cors: true,
        hmr: {
            host: 'localhost',
        },
        // Override the URL written to the hot file
        origin: 'http://localhost:5173',
    },
});
