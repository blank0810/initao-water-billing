<x-app-layout>
    <div class="p-6">
        <x-ui.admin.config.shared.page-header
            title="Penalty Configuration"
            subtitle="Configure late payment penalty rate and view penalty history"
        />

        @include('components.ui.billing.penalty-section')

        <!-- Penalty History Table -->
        <div x-data="penaltyHistory()" x-init="fetchHistory()" class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-history text-gray-400"></i>
                        Rate Change History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rate</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Effective Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Created By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && history.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No penalty configuration history found.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="item in history" :key="item.penalty_config_id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="item.rate_percentage + '%'"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="item.effective_date"></td>
                                    <td class="px-4 py-3">
                                        <span x-show="item.is_active" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                        <span x-show="!item.is_active" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="item.created_by_name || 'System'"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="item.created_at"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function penaltyHistory() {
        return {
            history: [],
            loading: false,

            async fetchHistory() {
                this.loading = true;
                try {
                    const response = await fetch('{{ route("penalties.config.history") }}');
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const data = await response.json();
                    if (data.success) {
                        this.history = data.data;
                    }
                } catch (error) {
                    console.error('Error fetching penalty history:', error);
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    </script>
</x-app-layout>
