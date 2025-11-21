<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <x-ui.page-header
                title="Application Process"
                subtitle="Track and manage customer applications in progress"
                icon="fas fa-file-alt">
            </x-ui.page-header>

            <!-- Breadcrumb Process Guide -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg">
                            <i class="fas fa-file-alt"></i>
                            <span class="font-medium">1. Application</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-credit-card"></i>
                            <span class="font-medium">2. Payment</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-check-circle"></i>
                            <span class="font-medium">3. Approval</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-plug"></i>
                            <span class="font-medium">4. Connection</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('customer.list') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-list mr-2"></i>Customer List
                        </a>
                        <a href="{{ route('application.process') }}" class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-file-alt mr-2"></i>Application Process
                        </a>
                    </nav>
                </div>
            </div>


            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="searchInputApp" 
                           placeholder="Search applications by name, code, or address..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                </div>
            </div>

            <!-- Application Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer Info</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address & Type</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter Reader & Area</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status & Progress</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Payment</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicationTable" class="divide-y divide-gray-100 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select id="appPageSize" 
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button id="prevPageApp" 
                            class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span id="currentPageApp">1</span> of <span id="totalPagesApp">1</span>
                    </div>
                    <button id="nextPageApp" 
                            class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
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

    <!-- Modals -->
    <x-ui.customer.modals.view-customer />
    <x-ui.customer.modals.edit-customer />
    <x-ui.customer.modals.delete-customer />

    <!-- Alert Notifications -->
    <x-ui.customer.alert-notification />

    @vite(['resources/js/unified-print.js', 'resources/js/data/customer/application-process.js'])
</x-app-layout>
