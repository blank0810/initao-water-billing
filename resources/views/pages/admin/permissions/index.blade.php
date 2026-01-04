<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto flex flex-col space-y-6">

                    <!-- Header Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="mb-4 lg:mb-0">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 flex items-center gap-3">
                                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                    <i class="fas fa-key text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                Permission Management
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                View all system permissions and their role assignments
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" id="permissionSearchInput" placeholder="Search permissions..."
                                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <a href="{{ route('admin.role-permissions.matrix') }}"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-th mr-2"></i>Permission Matrix
                            </a>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <i class="fas fa-key text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalPermissions">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Permissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <i class="fas fa-folder text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalModules">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Modules</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalRoles">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Roles</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions by Module -->
                    <div id="permissionsContainer" class="space-y-4">
                        <!-- Loading -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center border border-gray-200 dark:border-gray-700">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Loading permissions...</p>
                        </div>
                    </div>

                    <!-- View Permission Modal -->
                    <x-ui.admin.permission.modals.view-permission />

                </div>
            </main>
        </div>
    </div>

    @vite('resources/js/data/admin/permissions.js')
</x-app-layout>
