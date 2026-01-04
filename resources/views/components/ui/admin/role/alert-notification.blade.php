<div id="roleAlertContainer" class="fixed top-4 right-4 z-[60] space-y-2">
    <!-- Alerts will be dynamically inserted here -->
</div>

<script>
    function showRoleAlert(type, message, duration = 5000) {
        const container = document.getElementById('roleAlertContainer');
        const alertId = 'alert-' + Date.now();

        const alertColors = {
            success: 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
            error: 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
            warning: 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200',
            info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
        };

        const alertIcons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
        };

        const alertHtml = `
            <div id="${alertId}" class="flex items-center gap-3 p-4 rounded-lg border shadow-lg min-w-80 max-w-md ${alertColors[type]} transform translate-x-full opacity-0 transition-all duration-300">
                <i class="fas ${alertIcons[type]} text-lg flex-shrink-0"></i>
                <p class="flex-1 text-sm font-medium">${message}</p>
                <button onclick="dismissRoleAlert('${alertId}')" class="flex-shrink-0 hover:opacity-70 transition-opacity">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', alertHtml);

        // Animate in
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.classList.remove('translate-x-full', 'opacity-0');
            }
        }, 10);

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => dismissRoleAlert(alertId), duration);
        }
    }

    function dismissRoleAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => alertElement.remove(), 300);
        }
    }
</script>
