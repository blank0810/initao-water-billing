<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                title="Application List"
                icon="fas fa-file-alt">
                <x-slot name="actions">
                    <a href="{{ route('connection.service-application.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i>New Application
                    </a>
                </x-slot>
            </x-ui.page-header>

            <x-ui.action-functions 
                searchPlaceholder="Search applications by name, code, or address..."
                filterLabel="All Types"
                :filterOptions="[
                    ['value' => 'Residential', 'label' => 'Residential'],
                    ['value' => 'Commercial', 'label' => 'Commercial'],
                    ['value' => 'Industrial', 'label' => 'Industrial'],
                    ['value' => 'Institutional', 'label' => 'Institutional']
                ]"
                :showDateFilter="false"
                :showExport="true"
                tableId="applicationTable"
            />

            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
 <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Customer Info
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    ID Type & Number
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Address & Type
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Meter Reader & Area
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Date Created
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Print
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="applicationTable" class="divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select id="appPageSize" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button id="prevPageApp" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span id="currentPageApp">1</span> of <span id="totalPagesApp">1</span>
                    </div>
                    <button id="nextPageApp" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" id="startRecordApp">0</span> to 
                    <span class="font-semibold text-gray-900 dark:text-white" id="endRecordApp">0</span> of 
                    <span class="font-semibold text-gray-900 dark:text-white" id="totalRecordsApp">0</span> results
                </div>
            </div>
        </div>
    </div>

    <script>
        window.AppProcessConfig = { processPayment: false, printForm: true };
    </script>

    <x-ui.application.modals.view-customer />
    <x-ui.application.modals.edit-customer />
    <x-ui.application.modals.delete-customer />
    <x-ui.customer.alert-notification />

    @vite(['resources/js/utils/action-functions.js', 'resources/js/utils/print-form.js', 'resources/js/data/application/application-list.js'])
</x-app-layout>
