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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Activity Log</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View system activity and user login/logout history</p>
                    </div>
                </div>
                <button onclick="loadActivityLogs()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>

            <!-- Table Card -->
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                <!-- Filter Bar -->
                <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <!-- Left: Search -->
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search logs..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                    
                    <!-- Right: Filters -->
                    <div class="flex gap-2">
                        <select id="user-filter"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 appearance-none pr-10">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="date-from" placeholder="From"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <input type="date" id="date-to" placeholder="To"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <select id="per-page"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 appearance-none pr-10">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <button onclick="clearFilters()" title="Clear Filters"
                            class="inline-flex items-center justify-center w-9 h-9 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                        <button onclick="loadActivityLogs()" title="Apply Filters"
                            class="inline-flex items-center justify-center w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </div>
                </div>
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white dark:bg-gray-900">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Timestamp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">User Agent</th>
                            </tr>
                        </thead>
                        <tbody id="activity-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Skeleton rows -->
                            @for($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-48"></div></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-400">
                            Showing <span id="showing-from" class="font-semibold">0</span> to
                            <span id="showing-to" class="font-semibold">0</span> of
                            <span id="total-records" class="font-semibold">0</span> entries
                        </span>
                        <nav>
                            <ul id="pagination-controls" class="inline-flex -space-x-px text-sm"></ul>
                        </nav>
                    </div>
                </div>
            </div>

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
                    emptyRow.innerHTML = `
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <span class="text-gray-500 dark:text-gray-400">No activity logs found</span>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }

                updatePagination(data);
            } catch (error) {
                console.error('Error loading logs:', error);
                skeletonRows.forEach(row => row.classList.add('hidden'));

                const errorRow = document.createElement('tr');
                errorRow.innerHTML = `
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-exclamation-circle text-3xl text-red-500 mb-3"></i>
                            <span class="text-red-600 dark:text-red-400">Error loading activity logs</span>
                            <button onclick="loadActivityLogs()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                <i class="fas fa-redo mr-2"></i>Retry
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(errorRow);
            }
        }

        function createActivityRow(activity) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-800/50';

            const actionBadge = getActionBadge(activity.description);
            const userAgent = activity.user_agent || '';
            const truncatedUserAgent = userAgent.length > 60
                ? userAgent.substring(0, 60) + '...'
                : userAgent;

            row.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="font-medium text-gray-900 dark:text-white">${escapeHtml(activity.created_at)}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(activity.created_at_human)}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                    <div class="font-medium text-gray-900 dark:text-white">${escapeHtml(activity.causer_name)}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(activity.causer_email)}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">${actionBadge}</td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-mono">${escapeHtml(activity.ip_address)}</code>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate" title="${escapeHtml(userAgent)}">
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
                <button class="flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    ${data.current_page === 1 ? 'disabled' : ''}>
                    Previous
                </button>`;
            if (data.current_page > 1) {
                prev.querySelector('button').onclick = () => { currentPage--; loadActivityLogs(); };
            }
            container.appendChild(prev);

            // Page numbers (simplified)
            const pageInfo = document.createElement('li');
            pageInfo.innerHTML = `
                <span class="flex items-center justify-center px-3 py-2 text-sm text-gray-700 dark:text-gray-400">
                    Page <span class="font-semibold mx-1">${data.current_page}</span> of <span class="font-semibold ml-1">${data.last_page}</span>
                </span>`;
            container.appendChild(pageInfo);

            // Next button
            const next = document.createElement('li');
            next.innerHTML = `
                <button class="flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    ${data.current_page === data.last_page ? 'disabled' : ''}>
                    Next
                </button>`;
            if (data.current_page < data.last_page) {
                next.querySelector('button').onclick = () => { currentPage++; loadActivityLogs(); };
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
