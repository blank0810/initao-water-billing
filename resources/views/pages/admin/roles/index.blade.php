<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto flex flex-col space-y-6">

                    <!-- Header Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="mb-4 lg:mb-0">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 flex items-center gap-3">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                Role Management
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                Manage user roles and their associated permissions
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" id="roleSearchInput" placeholder="Search roles..."
                                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <button onclick="openCreateRoleModal()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create Role
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalRoles">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Roles</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <i class="fas fa-cog text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statSystemRoles">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">System Roles</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <i class="fas fa-key text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalPermissions">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Permissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <i class="fas fa-users text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="statAssignedUsers">-</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Users with Roles</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Permissions
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Users
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="rolesTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Loading skeleton -->
                                    <tr id="loadingRow">
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-spinner fa-spin text-xl"></i>
                                                <span>Loading roles...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Row Template -->
                    <template id="role-row-template">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4" data-col="name">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" data-field="role_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" data-field="role_type"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400" data-col="description"></td>
                            <td class="px-6 py-4 text-center" data-col="permissions">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200"></span>
                            </td>
                            <td class="px-6 py-4 text-center" data-col="users">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200"></span>
                            </td>
                            <td class="px-6 py-4 text-center" data-col="actions">
                                <div class="flex items-center justify-center gap-1"></div>
                            </td>
                        </tr>
                    </template>

                    <!-- Include Modals -->
                    <x-ui.admin.role.modals.view-role />
                    <x-ui.admin.role.modals.edit-role />
                    <x-ui.admin.role.modals.create-role />
                    <x-ui.admin.role.modals.delete-role />
                    <x-ui.admin.role.alert-notification />

                </div>
            </main>
        </div>
    </div>

    @vite('resources/js/data/admin/roles.js')
</x-app-layout>
