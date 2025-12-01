<div id="alertToast" class="hidden fixed top-4 right-4 z-[60] max-w-md animate-slide-in">
    <div id="toastContent" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border-l-4 p-4 flex items-start gap-3">
        <div id="toastIcon" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
            <i class="fas text-lg"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h4 id="toastTitle" class="font-semibold text-gray-900 dark:text-white text-sm"></h4>
            <p id="toastMessage" class="text-sm text-gray-600 dark:text-gray-400 mt-1"></p>
        </div>
        <button onclick="closeToast()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-times"></i>
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
            border: 'border-green-500',
            iconBg: 'bg-green-100 dark:bg-green-900/30',
            iconColor: 'text-green-600 dark:text-green-400',
            icon: 'fa-check-circle'
        },
        error: {
            border: 'border-red-500',
            iconBg: 'bg-red-100 dark:bg-red-900/30',
            iconColor: 'text-red-600 dark:text-red-400',
            icon: 'fa-exclamation-circle'
        },
        warning: {
            border: 'border-yellow-500',
            iconBg: 'bg-yellow-100 dark:bg-yellow-900/30',
            iconColor: 'text-yellow-600 dark:text-yellow-400',
            icon: 'fa-exclamation-triangle'
        },
        info: {
            border: 'border-blue-500',
            iconBg: 'bg-blue-100 dark:bg-blue-900/30',
            iconColor: 'text-blue-600 dark:text-blue-400',
            icon: 'fa-info-circle'
        }
    };
    
    const config = types[type] || types.info;
    
    content.className = `bg-white dark:bg-gray-800 rounded-xl shadow-2xl border-l-4 ${config.border} p-4 flex items-start gap-3`;
    icon.className = `flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ${config.iconBg}`;
    iconEl.className = `fas ${config.icon} text-lg ${config.iconColor}`;
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
