<!-- Alert Notification Component -->
<div x-data="{ 
    show: false, 
    message: '', 
    type: 'info',
    timeout: null
}" 
x-on:show-alert.window="
    message = $event.detail.message || 'Notification';
    type = $event.detail.type || 'info';
    show = true;
    clearTimeout(timeout);
    timeout = setTimeout(() => show = false, $event.detail.duration || 4000);
"
x-show="show"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-x-full scale-95"
x-transition:enter-end="opacity-100 transform translate-x-0 scale-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 transform translate-x-0 scale-100"
x-transition:leave-end="opacity-0 transform translate-x-full scale-95"
class="fixed top-6 right-6 z-50 max-w-sm w-full"
style="display: none;">
    <div class="rounded-xl shadow-2xl border backdrop-blur-sm p-4"
        :class="{
            'bg-green-50/95 border-green-200 text-green-800 dark:bg-green-900/40 dark:border-green-700 dark:text-green-200': type === 'success',
            'bg-red-50/95 border-red-200 text-red-800 dark:bg-red-900/40 dark:border-red-700 dark:text-red-200': type === 'error',
            'bg-yellow-50/95 border-yellow-200 text-yellow-800 dark:bg-yellow-900/40 dark:border-yellow-700 dark:text-yellow-200': type === 'warning',
            'bg-blue-50/95 border-blue-200 text-blue-800 dark:bg-blue-900/40 dark:border-blue-700 dark:text-blue-200': type === 'info'
        }">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                    :class="{
                        'bg-green-100 dark:bg-green-800/50': type === 'success',
                        'bg-red-100 dark:bg-red-800/50': type === 'error',
                        'bg-yellow-100 dark:bg-yellow-800/50': type === 'warning',
                        'bg-blue-100 dark:bg-blue-800/50': type === 'info'
                    }">
                    <i class="fas text-sm"
                        :class="{
                            'fa-check text-green-600 dark:text-green-400': type === 'success',
                            'fa-times text-red-600 dark:text-red-400': type === 'error',
                            'fa-exclamation text-yellow-600 dark:text-yellow-400': type === 'warning',
                            'fa-info text-blue-600 dark:text-blue-400': type === 'info'
                        }"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium leading-5" x-text="message"></p>
            </div>
            <button @click="show = false" class="ml-2 p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>
</div>