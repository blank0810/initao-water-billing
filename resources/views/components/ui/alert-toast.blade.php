<div id="alertToast" class="hidden fixed top-4 right-4 z-[60] max-w-sm animate-slide-in">
    <div id="toastContent" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border p-4 flex items-start gap-3">
        <div id="toastIcon" class="flex-shrink-0 w-5 h-5 flex items-center justify-center mt-0.5">
            <i class="fas text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p id="toastTitle" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
            <p id="toastMessage" class="text-xs text-gray-600 dark:text-gray-400 mt-1"></p>
        </div>
        <button onclick="closeToast()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</div>

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>

<script>
let toastTimeout;

function showToast(title, message, type = 'info') {
    const toast = document.getElementById('alertToast');
    const content = document.getElementById('toastContent');
    const icon = document.getElementById('toastIcon');
    const iconEl = icon.querySelector('i');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');
    
    const types = {
        success: {
            border: 'border-green-200 dark:border-green-900/50',
            iconColor: 'text-green-600 dark:text-green-400',
            icon: 'fa-check-circle'
        },
        error: {
            border: 'border-red-200 dark:border-red-900/50',
            iconColor: 'text-red-600 dark:text-red-400',
            icon: 'fa-exclamation-circle'
        },
        warning: {
            border: 'border-orange-200 dark:border-orange-900/50',
            iconColor: 'text-orange-600 dark:text-orange-400',
            icon: 'fa-exclamation-triangle'
        },
        info: {
            border: 'border-blue-200 dark:border-blue-900/50',
            iconColor: 'text-blue-600 dark:text-blue-400',
            icon: 'fa-info-circle'
        }
    };
    
    const config = types[type] || types.info;
    
    content.className = `bg-white dark:bg-gray-800 rounded-lg shadow-lg border p-4 flex items-start gap-3 ${config.border}`;
    icon.className = `flex-shrink-0 w-5 h-5 flex items-center justify-center mt-0.5`;
    iconEl.className = `fas ${config.icon} text-sm ${config.iconColor}`;
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    toast.classList.remove('hidden');
    
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        closeToast();
    }, 5000);
}

function closeToast() {
    document.getElementById('alertToast').classList.add('hidden');
    clearTimeout(toastTimeout);
}

window.showToast = showToast;
window.closeToast = closeToast;

window.addEventListener('show-alert', (e) => {
    const { title, message, type } = e.detail;
    showToast(title || 'Notification', message, type || 'info');
});
</script>
