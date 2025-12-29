import './bootstrap';
import './theme.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
if (!window.__alpineStarted) {
    window.__alpineStarted = true;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Alpine.start());
    } else {
        Alpine.start();
    }
}

// Initialize charts
document.addEventListener('DOMContentLoaded', () => {
    initializeCharts();
    // Customer-list specific script was moved to `resources/js/data/customer/customer.js`.
});

// --- Chart initialization (placeholder) ---
function initializeCharts() {
    // Dashboard chart initialization lives here (keep dashboard-specific code only).
}
