import './bootstrap';
import './theme.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Initialize charts
document.addEventListener('DOMContentLoaded', () => {
    initializeCharts();
    // Customer-list specific script was moved to `resources/js/data/customer/customer.js`.
});

// --- Chart initialization (placeholder) ---
function initializeCharts() {
    // Dashboard chart initialization lives here (keep dashboard-specific code only).
}
