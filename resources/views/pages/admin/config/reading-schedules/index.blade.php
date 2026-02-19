<x-app-layout>
    <div x-data="readingScheduleManager()" class="p-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Reading Schedules</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and manage meter reading schedules for each area and period</p>
            </div>
            <button @click="openCreateScheduleModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Schedule
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_schedules"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-sm font-medium text-yellow-500">Pending</div>
                <div class="text-2xl font-bold text-yellow-600" x-text="stats.pending"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-sm font-medium text-blue-500">In Progress</div>
                <div class="text-2xl font-bold text-blue-600" x-text="stats.in_progress"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-sm font-medium text-green-500">Completed</div>
                <div class="text-2xl font-bold text-green-600" x-text="stats.completed"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-sm font-medium text-red-500">Delayed</div>
                <div class="text-2xl font-bold text-red-600" x-text="stats.delayed"></div>
            </div>
        </div>

        <!-- Status Filter -->
        <div class="mb-4">
            <select x-model="scheduleStatusFilter" @change="filterByStatus()" class="text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="delayed">Delayed</option>
            </select>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading schedules...</p>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <x-ui.admin.config.reading-schedule.table />
        </div>

        <!-- Modals -->
        <x-ui.admin.config.reading-schedule.modals.create-schedule />
        <x-ui.admin.config.reading-schedule.modals.edit-schedule />
        <x-ui.admin.config.reading-schedule.modals.view-schedule />
        <x-ui.admin.config.reading-schedule.modals.complete-schedule />
        <x-ui.admin.config.reading-schedule.modals.delay-schedule />
        <x-ui.admin.config.reading-schedule.modals.delete-schedule />

        <!-- Success Notification -->
        <div x-show="showSuccess"
             x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError"
             x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>
