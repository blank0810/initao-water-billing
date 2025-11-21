import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chart-dash.js',
                'resources/js/data/billing/bill-data.js',
                'resources/js/data/user/user.js',
                'resources/js/data/customer/customer.js',
                'resources/js/data/customer/add-customer.js',
                'resources/js/data/customer/payment.js',
                'resources/js/data/customer/approve.js',
                'resources/js/data/customer/enhanced-approval.js',
                'resources/js/data/customer/simple-customer-list.js',
                'resources/js/data/customer/application-process.js',
                'resources/js/data/customer/declined-customers.js',
                'resources/js/data/connection/service.js',
                'resources/js/data/connection/connection.js',
                'resources/js/data/consumer/consumer.js',
                'resources/js/unified-print.js',
            ],
            refresh: true,
        }),
    ],
});
