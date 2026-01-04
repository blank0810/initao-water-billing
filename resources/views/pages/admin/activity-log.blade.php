@push('styles')
<style>
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite linear;
    }

    .dark .skeleton {
        background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
        background-size: 1000px 100%;
    }
</style>
@endpush

<x-app-layout>
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-4"></div>

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto">

                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    <i class="fas fa-history mr-3 text-blue-600"></i>Activity Log
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400">
                                    View system activity and user login/logout history
                                </p>
                            </div>
                            <div>
                                <button onclick="loadActivityLogs()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                <input type="text" id="search" placeholder="Search logs..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- User Filter -->
                            <div>
                                <label for="user-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                                <select id="user-filter"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label for="date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                                <input type="date" id="date-from"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                                <input type="date" id="date-to"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Per Page -->
                            <div>
                                <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per Page</label>
                                <select id="per-page"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button onclick="clearFilters()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mr-2 transition">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </button>
                            <button onclick="loadActivityLogs()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-search mr-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold">Timestamp</th>
                                        <th class="px-6 py-3 font-semibold">User</th>
                                        <th class="px-6 py-3 font-semibold">Action</th>
                                        <th class="px-6 py-3 font-semibold">IP Address</th>
                                        <th class="px-6 py-3 font-semibold">User Agent</th>
                                    </tr>
                                </thead>
                                <tbody id="activity-table-body">
                                    <!-- Skeleton rows -->
                                    @for($i = 0; $i < 5; $i++)
                                    <tr class="skeleton-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-32 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-24 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-20 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-28 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-48 rounded"></div></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-700 dark:text-gray-400 mb-4 sm:mb-0">
                                Showing <span id="showing-from" class="font-semibold">0</span> to
                                <span id="showing-to" class="font-semibold">0</span> of
                                <span id="total-records" class="font-semibold">0</span> entries
                            </div>
                            <nav>
                                <ul id="pagination-controls" class="inline-flex -space-x-px text-sm"></ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadActivityLogs();

            // Debounced search
            document.getElementById('search').addEventListener('input', debounce(loadActivityLogs, 300));

            // Filter change handlers
            document.getElementById('per-page').addEventListener('change', function() {
                currentPage = 1;
                loadActivityLogs();
            });
        });

        async function loadActivityLogs() {
            const tbody = document.getElementById('activity-table-body');
            const skeletonRows = document.querySelectorAll('.skeleton-row');

            // Show skeletons
            skeletonRows.forEach(row => row.classList.remove('hidden'));

            // Remove existing data rows
            Array.from(tbody.children).forEach(row => {
                if (!row.classList.contains('skeleton-row')) {
                    row.remove();
                }
            });

            const params = new URLSearchParams({
                page: currentPage,
                per_page: document.getElementById('per-page').value,
                search: document.getElementById('search').value,
                user_id: document.getElementById('user-filter').value,
                date_from: document.getElementById('date-from').value,
                date_to: document.getElementById('date-to').value,
            });

            try {
                const response = await fetch(`{{ route('admin.activity-log') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                // Hide skeletons
                skeletonRows.forEach(row => row.classList.add('hidden'));

                if (data.data && data.data.length > 0) {
                    data.data.forEach(activity => {
                        tbody.appendChild(createActivityRow(activity));
                    });
                } else {
                    const emptyRow = document.createElement('tr');
                    emptyRow.className = 'bg-white dark:bg-gray-800';
                    emptyRow.innerHTML = `
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-400 block"></i>
                            <p class="text-lg font-medium">No activity logs found</p>
                            <p class="text-sm mt-1">Activity will appear here when users login or logout</p>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }

                updatePagination(data);
            } catch (error) {
                console.error('Error loading logs:', error);
                skeletonRows.forEach(row => row.classList.add('hidden'));

                const errorRow = document.createElement('tr');
                errorRow.className = 'bg-white dark:bg-gray-800';
                errorRow.innerHTML = `
                    <td colspan="5" class="px-6 py-12 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-4xl mb-4 block"></i>
                        <p class="text-lg font-medium">Error loading activity logs</p>
                        <p class="text-sm mt-1">Please try again later</p>
                    </td>
                `;
                tbody.appendChild(errorRow);
            }
        }

        function createActivityRow(activity) {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';

            const actionBadge = getActionBadge(activity.description);
            const truncatedUserAgent = activity.user_agent.length > 60
                ? activity.user_agent.substring(0, 60) + '...'
                : activity.user_agent;

            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${activity.created_at}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${activity.created_at_human}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(activity.causer_name)}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(activity.causer_email)}</div>
                </td>
                <td class="px-6 py-4">${actionBadge}</td>
                <td class="px-6 py-4">
                    <code class="text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-mono">${escapeHtml(activity.ip_address)}</code>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate" title="${escapeHtml(activity.user_agent)}">
                    ${escapeHtml(truncatedUserAgent)}
                </td>
            `;

            return row;
        }

        function getActionBadge(description) {
            const badges = {
                'User logged in': `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <i class="fas fa-sign-in-alt mr-1.5"></i>Login
                    </span>`,
                'User logged out': `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        <i class="fas fa-sign-out-alt mr-1.5"></i>Logout
                    </span>`,
                'Failed login attempt': `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                        <i class="fas fa-exclamation-triangle mr-1.5"></i>Failed
                    </span>`,
            };
            return badges[description] || `<span class="text-gray-600 dark:text-gray-400">${escapeHtml(description)}</span>`;
        }

        function updatePagination(data) {
            document.getElementById('showing-from').textContent = data.from || 0;
            document.getElementById('showing-to').textContent = data.to || 0;
            document.getElementById('total-records').textContent = data.total || 0;

            const container = document.getElementById('pagination-controls');
            container.innerHTML = '';

            if (data.last_page <= 1) return;

            // Previous button
            const prev = document.createElement('li');
            prev.innerHTML = `
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight border rounded-l-lg
                    ${data.current_page === 1
                        ? 'text-gray-400 bg-gray-100 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed'
                        : 'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'}
                    border-gray-300 dark:border-gray-600">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>`;
            if (data.current_page > 1) {
                prev.onclick = (e) => { e.preventDefault(); currentPage--; loadActivityLogs(); };
            }
            container.appendChild(prev);

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, data.current_page - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(data.last_page, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const page = document.createElement('li');
                const isActive = i === data.current_page;
                page.innerHTML = `
                    <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight border
                        ${isActive
                            ? 'text-blue-600 bg-blue-50 border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-600'
                            : 'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white border-gray-300 dark:border-gray-600'}">
                        ${i}
                    </a>`;
                page.onclick = (e) => { e.preventDefault(); currentPage = i; loadActivityLogs(); };
                container.appendChild(page);
            }

            // Next button
            const next = document.createElement('li');
            next.innerHTML = `
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight border rounded-r-lg
                    ${data.current_page === data.last_page
                        ? 'text-gray-400 bg-gray-100 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed'
                        : 'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'}
                    border-gray-300 dark:border-gray-600">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>`;
            if (data.current_page < data.last_page) {
                next.onclick = (e) => { e.preventDefault(); currentPage++; loadActivityLogs(); };
            }
            container.appendChild(next);
        }

        function clearFilters() {
            document.getElementById('search').value = '';
            document.getElementById('user-filter').value = '';
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            document.getElementById('per-page').value = '25';
            currentPage = 1;
            loadActivityLogs();
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), wait);
            };
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
    @endpush
</x-app-layout>
