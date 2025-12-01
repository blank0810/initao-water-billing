<!-- User Alert Notification Component -->
<div x-data="{ 
    show: false, 
    message: '', 
    type: 'info',
    timeout: null 
}" 
x-show="show" 
x-cloak
@user-alert.window="
    show = true; 
    message = $event.detail.message; 
    type = $event.detail.type || 'info';
    clearTimeout(timeout);
    timeout = setTimeout(() => show = false, 3000);
"
class="fixed top-4 right-4 z-50 transition-all duration-300"
x-transition:enter="transform ease-out duration-300 transition"
x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
x-transition:leave="transition ease-in duration-100"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0">

    <div class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
         :class="{
             'bg-green-50 border-green-200': type === 'success',
             'bg-red-50 border-red-200': type === 'error',
             'bg-yellow-50 border-yellow-200': type === 'warning',
             'bg-blue-50 border-blue-200': type === 'info'
         }">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="text-xl"
                       :class="{
                           'fas fa-check-circle text-green-400': type === 'success',
                           'fas fa-exclamation-circle text-red-400': type === 'error',
                           'fas fa-exclamation-triangle text-yellow-400': type === 'warning',
                           'fas fa-info-circle text-blue-400': type === 'info'
                       }"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium"
                       :class="{
                           'text-green-900': type === 'success',
                           'text-red-900': type === 'error',
                           'text-yellow-900': type === 'warning',
                           'text-blue-900': type === 'info'
                       }"
                       x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false" 
                            class="rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'text-green-500 hover:text-green-600 focus:ring-green-500': type === 'success',
                                'text-red-500 hover:text-red-600 focus:ring-red-500': type === 'error',
                                'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': type === 'warning',
                                'text-blue-500 hover:text-blue-600 focus:ring-blue-500': type === 'info'
                            }">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Helper function to show user alerts
window.showUserAlert = function(message, type = 'info') {
    window.dispatchEvent(new CustomEvent('user-alert', {
        detail: { message, type }
    }));
};
</script>