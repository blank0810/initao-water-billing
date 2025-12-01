<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto flex flex-col space-y-6">

                    <!-- Header Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                        <div class="mb-4 lg:mb-0">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                System Users
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300">Manage administrators and user accounts</p>
                        </div>

                        <!-- Standardized Toolbar -->
                        <x-ui.standard-toolbar 
                            searchId="searchInput"
                            searchPlaceholder="Search users..."
                            :primaryAction="route('user.add')"
                            primaryActionText="Add User"
                            primaryActionIcon="user-plus">
                            <x-slot name="filters">
                                <select id="roleFilter" onchange="filterUsers()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    <option value="">All Roles</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </x-slot>
                        </x-ui.standard-toolbar>
                    </div>

                    <!-- User Table -->
                    <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created On</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Table rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show</span>
                            <select id="userPageSize" onchange="userPagination.updatePageSize(this.value)" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-sm text-gray-700 dark:text-gray-300">entries</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button id="userPrevBtn" onclick="userPagination.prevPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fas fa-chevron-left mr-1"></i>Previous
                            </button>
                            <div class="text-sm text-gray-700 dark:text-gray-300 px-3">
                                Page <span id="userCurrentPage">1</span> of <span id="userTotalPages">1</span>
                            </div>
                            <button id="userNextBtn" onclick="userPagination.nextPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                Next<i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                        
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing <span id="userTotalRecords">0</span> results
                        </div>
                    </div>

                    <!-- User Row Template -->
                    <template id="user-row-template">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" data-col="id"></td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100" data-col="name"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" data-col="email"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" data-col="role"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" data-col="date"></td>
                            <td class="px-4 py-3 text-center" data-col="status"></td>
                            <td class="px-4 py-3 text-center" data-col="actions"></td>
                        </tr>
                    </template>

                    {{-- Alerts and Modals --}}
                    <x-ui.user.alert-notification />
                    <x-ui.user.modals.view-user />
                    <x-ui.user.modals.edit-user />
                    <x-ui.user.modals.delete-user />
                </div>
            </main>
        </div>
    </div>

    @vite('resources/js/data/user/user.js')
</x-app-layout>
