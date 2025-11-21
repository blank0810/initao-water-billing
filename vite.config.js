import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
<<<<<<< HEAD
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
=======
            input: ["resources/css/app.css", "resources/js/app.js"],
>>>>>>> d495afb1c6251dddf501f93e05fce3c8006270e2
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0", // accessible inside Docker network
        port: 5173,
        hmr: {
            host: "localhost", // where your browser connects (host machine)
        },
        watch: {
            usePolling: true, // ensures HMR works on mounted volumes
        },
    },
});
