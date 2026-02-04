<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="{ activeTab: 'reports' }">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-pie text-[#3D90D7]"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Printables</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Management reports, historical data, and official documents</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:inline-flex items-center bg-gray-100 dark:bg-gray-800 px-3 py-1.5 rounded-lg">
                            <i class="fas fa-calendar-alt mr-2 text-[#3D90D7]"></i>
                            {{ now()->format('F d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Summary Cards - Reports Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">2,458</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-peso-sign text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Collection (MTD)</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">₱485,230</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Billed (MTD)</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">₱612,450</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Outstanding Balance</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">₱127,220</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-Tab Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Tab Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <nav class="flex" aria-label="Tabs">
                        <button @click="activeTab = 'reports'" 
                            :class="activeTab === 'reports' 
                                ? 'border-[#3D90D7] text-[#3D90D7] bg-white dark:bg-gray-800' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="flex-1 sm:flex-none group inline-flex items-center justify-center px-8 py-4 border-b-2 font-medium text-sm transition-all">
                            <i class="fas fa-file-alt mr-2"></i>
                            <span>Reports</span>
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">5</span>
                        </button>
                        <button @click="activeTab = 'printables'" 
                            :class="activeTab === 'printables' 
                                ? 'border-[#3D90D7] text-[#3D90D7] bg-white dark:bg-gray-800' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="flex-1 sm:flex-none group inline-flex items-center justify-center px-8 py-4 border-b-2 font-medium text-sm transition-all">
                            <i class="fas fa-print mr-2"></i>
                            <span>Printables</span>
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">3</span>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    
                    <!-- ========================================== -->
                    <!-- REPORTS TAB - Interactive Tables -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'reports'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        
                        <!-- Reports Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Report Library</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Interactive reports with sorting, filtering, and export capabilities</p>
                            </div>
                        </div>

                        <!-- Reports Template List - Clean Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- Report Card: Aging of Accounts -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Aging of Accounts</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accounts receivable aging analysis with 30/60/90+ day buckets.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.aging') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Report Card: Account Masterlist -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Account Masterlist</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Complete consumer master list with account status and details.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.masterlist') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Report Card: Monthly Billing Summary -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-invoice text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Monthly Billing Summary</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Monthly billing totals grouped by area with collection stats.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.billing') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Report Card: Monthly Collection Summary -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-hand-holding-usd text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Monthly Collection Summary</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Historical collection summary with payment totals and trends.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.collection') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Report Card: Monthly Status Report -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-pie text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Monthly Status Report</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comprehensive billing vs collection performance breakdown.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.status') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- PRINTABLES TAB - Fixed Print-Ready Tables -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'printables'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        
                        <!-- Printables Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Printable Documents</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Fixed print-ready tables optimized for A4/Letter paper printing</p>
                            </div>
                        </div>

                        <!-- Printables Template List - Clean Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- Printable Card: Abstract of Collections -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-invoice-dollar text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Abstract of Collections</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Official collection abstract with OR numbers and payment breakdown.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.abstract') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Printable Card: Bill History -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-history text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Bill History</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Complete billing history per consumer with meter readings.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.bill-history') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                            <!-- Printable Card: Billing Statement -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-receipt text-[#3D90D7]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Billing Statement</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Individual consumer billing statement for distribution.</p>
                                    </div>
                                </div>
                                <a href="{{ route('reports.tables.statement') }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-table mr-2"></i>View Table
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                <p><i class="fas fa-shield-alt mr-1"></i> Reports are read-only and generated from live system data. For operational tasks, please use the <a href="{{ route('billing.management') }}" class="text-[#3D90D7] hover:underline">Billing & Collections</a> module.</p>
            </div>

        </div>
    </div>
</x-app-layout>
