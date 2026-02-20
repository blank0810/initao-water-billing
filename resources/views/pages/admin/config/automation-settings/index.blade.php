<x-app-layout>
    <div x-data="automationSettings()" class="p-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Automation Settings</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure automated tasks that run on a schedule. Toggle each setting to enable or disable the corresponding automation.</p>
            </div>
        </div>

        <!-- Setting Cards -->
        <div class="space-y-4">
            <template x-for="(setting, key) in settings" :key="key">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <!-- Setting Info -->
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                 :class="setting.value ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-100 dark:bg-gray-700'">
                                <i class="fas" :class="[getIcon(key), setting.value ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500']"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="getLabel(key)"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" x-text="setting.description"></p>
                            </div>
                        </div>

                        <!-- Toggle Switch -->
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-xs font-medium"
                                  :class="setting.value ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'"
                                  x-text="setting.value ? 'Enabled' : 'Disabled'"></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer"
                                       :checked="setting.value"
                                       @change="toggleSetting(key)"
                                       :disabled="saving[key]">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                            <!-- Saving indicator -->
                            <div x-show="saving[key]" class="w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Status Bar -->
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs" :class="setting.value ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                            <i class="fas mr-1" :class="setting.value ? 'fa-check-circle' : 'fa-pause-circle'"></i>
                            <span x-text="setting.value ? 'This automation is active and will run on its scheduled interval.' : 'This automation is currently paused and will not run.'"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="Object.keys(settings).length === 0" class="text-center py-12">
            <i class="fas fa-robot text-4xl text-gray-300 dark:text-gray-600"></i>
            <p class="mt-4 text-gray-500 dark:text-gray-400">No automation settings found.</p>
            <p class="text-sm text-gray-400 dark:text-gray-500">Run the system settings seeder to populate automation settings.</p>
        </div>

        <!-- Success Notification -->
        <div x-show="showSuccess" x-transition
             class="fixed top-4 right-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800 dark:text-green-200" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError" x-transition
             class="fixed top-4 right-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800 dark:text-red-200" x-text="errorMessage"></p>
            </div>
        </div>
    </div>

    <script>
    function automationSettings() {
        return {
            settings: @json($settings),
            saving: {},
            showSuccess: false,
            successMessage: '',
            showError: false,
            errorMessage: '',

            init() {
                Object.keys(this.settings).forEach(key => {
                    this.saving[key] = false;
                });
            },

            getLabel(key) {
                const labels = {
                    'auto_create_period': 'Auto-Create Period',
                    'auto_apply_penalties': 'Auto-Apply Penalties',
                    'auto_close_reading_schedule': 'Auto-Close Reading Schedule',
                };
                return labels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            },

            getIcon(key) {
                const icons = {
                    'auto_create_period': 'fa-calendar-plus',
                    'auto_apply_penalties': 'fa-gavel',
                    'auto_close_reading_schedule': 'fa-clipboard-check',
                };
                return icons[key] || 'fa-cog';
            },

            async toggleSetting(key) {
                this.saving[key] = true;
                const newValue = !this.settings[key].value;

                try {
                    const res = await fetch('{{ route("config.automation-settings.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify({
                            key: key,
                            value: newValue ? '1' : '0',
                        }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.settings[key].value = newValue;
                        this.showSuccessNotification(data.message || 'Setting updated successfully.');
                    } else {
                        this.showErrorNotification(data.message || 'Failed to update setting.');
                    }
                } catch (e) {
                    console.error('Error updating setting:', e);
                    this.showErrorNotification('Network error. Please try again.');
                } finally {
                    this.saving[key] = false;
                }
            },

            showSuccessNotification(msg) {
                this.successMessage = msg;
                this.showSuccess = true;
                setTimeout(() => this.showSuccess = false, 3000);
            },

            showErrorNotification(msg) {
                this.errorMessage = msg;
                this.showError = true;
                setTimeout(() => this.showError = false, 5000);
            },
        };
    }
    </script>
</x-app-layout>
