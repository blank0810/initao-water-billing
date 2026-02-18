import './bootstrap';
import './theme.js';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Admin Config Components
import './components/admin/config/barangays/barangayManager.js';
import './components/admin/config/areas/areaManager.js';
import './components/admin/config/water-rates/waterRateManager.js';
import './components/admin/config/puroks/purokManager.js';
import './components/admin/config/account-types/accountTypeManager.js';
import './components/admin/config/charge-items/chargeItemManager.js';
import './components/admin/config/reading-schedules/readingScheduleManager.js';

Alpine.plugin(collapse);

window.Alpine = Alpine;
if (!window.__alpineStarted) {
    window.__alpineStarted = true;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Alpine.start());
    } else {
        Alpine.start();
    }
}

// Initialize chart
document.addEventListener('DOMContentLoaded', () => {
    initializeCharts();
});

// --- Chart initialization (placeholder) ---
function initializeCharts() {
    // Dashboard chart initialization lives here (keep dashboard-specific code only).
}


//Hello