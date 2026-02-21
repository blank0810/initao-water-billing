<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">System Users</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage administrators and user accounts</p>
                    </div>
                </div>
                <a href="{{ route('user.add') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Add User
                </a>
            </div>

            <!-- Table Card -->
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                <!-- Filter Bar -->
                <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <!-- Left: Search -->
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Search user..."
                               class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                    
                    <!-- Right: Role Filter -->
                    <div class="relative">
                        <select id="roleFilter"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 pr-10 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 appearance-none">
                            <option value="">All Roles</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white dark:bg-gray-900">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody id="userTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Skeleton Loaders -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <button id="userPrevBtn" onclick="userPagination.prevPage()" 
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            Previous
                        </button>
                        <span class="text-sm text-gray-700 dark:text-gray-400">
                            Page <span id="userCurrentPage">1</span> of <span id="userTotalPages">1</span>
                        </span>
                        <button id="userNextBtn" onclick="userPagination.nextPage()" 
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            Next
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- User Row Template -->
    <template id="user-row-template">
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" data-col="id"></td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" data-col="name"></td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-col="email"></td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-col="role"></td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-col="date"></td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-col="status"></td>
            <td class="px-4 py-3 text-center" data-col="actions"></td>
        </tr>
    </template>

    {{-- Alerts and Modals --}}
    <x-ui.user.alert-notification />
    <x-ui.user.modals.view-user />
    <x-ui.user.modals.edit-user />
    <x-ui.user.modals.delete-user />

    @vite('resources/js/data/user/user.js')
</x-app-layout>
