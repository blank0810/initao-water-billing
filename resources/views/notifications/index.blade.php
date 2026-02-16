<x-app-layout>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto" x-data="notificationManager()" x-init="init()">
                <div class="max-w-5xl mx-auto">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span x-text="total"></span> total notifications
                            </p>
                        </div>
                        <button @click="markAllRead()" x-show="counts.unread > 0"
                                class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                            Mark all as read
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <!-- Unread -->
                        <button @click="setFilter(filter === 'unread' ? null : 'unread'); category = null;"
                                class="p-4 rounded-xl border-2 transition-all text-left"
                                :class="filter === 'unread'
                                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20'
                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="counts.unread"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Unread</p>
                                </div>
                            </div>
                        </button>

                        <!-- Applications -->
                        <button @click="setCategory(category === 'applications' ? null : 'applications'); filter = null;"
                                class="p-4 rounded-xl border-2 transition-all text-left"
                                :class="category === 'applications'
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="counts.applications"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Applications</p>
                                </div>
                            </div>
                        </button>

                        <!-- Payments -->
                        <button @click="setCategory(category === 'payments' ? null : 'payments'); filter = null;"
                                class="p-4 rounded-xl border-2 transition-all text-left"
                                :class="category === 'payments'
                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="counts.payments"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Payments</p>
                                </div>
                            </div>
                        </button>

                        <!-- Connections -->
                        <button @click="setCategory(category === 'connections' ? null : 'connections'); filter = null;"
                                class="p-4 rounded-xl border-2 transition-all text-left"
                                :class="category === 'connections'
                                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20'
                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="counts.connections"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Connections</p>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Filter Bar -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                        <!-- Tab Pills -->
                        <div class="flex gap-1 bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                            <button @click="filter = null; category = null; currentPage = 1; fetchNotifications();"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition"
                                    :class="!filter && !category
                                        ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                                All
                            </button>
                            <button @click="filter = 'unread'; category = null; currentPage = 1; fetchNotifications();"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition"
                                    :class="filter === 'unread'
                                        ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                                Unread
                            </button>
                            <button @click="filter = 'read'; category = null; currentPage = 1; fetchNotifications();"
                                    class="px-4 py-1.5 text-sm font-medium rounded-md transition"
                                    :class="filter === 'read'
                                        ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                                Read
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" x-model="search" @input="onSearch()"
                                   placeholder="Search notifications..."
                                   class="pl-10 pr-4 py-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- Active Filter Indicator -->
                    <div x-show="category" x-cloak class="mb-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                            Filtering by: <span class="font-medium capitalize" x-text="category"></span>
                            <button @click="category = null; currentPage = 1; fetchNotifications();" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <!-- Loading -->
                    <template x-if="loading">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && notifications.length === 0">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <svg class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No notifications</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span x-show="filter || category || search">No notifications match your current filters.</span>
                                <span x-show="!filter && !category && !search">You're all caught up!</span>
                            </p>
                        </div>
                    </template>

                    <!-- Notification List -->
                    <template x-if="!loading && notifications.length > 0">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="notification in notifications" :key="notification.id">
                                <div @click="markRead(notification)"
                                     class="flex items-start gap-4 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                     :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notification.read_at }">
                                    <!-- Color Dot -->
                                    <span class="mt-1.5 h-3 w-3 rounded-full flex-shrink-0"
                                          :class="getColorClass(notification.category_color)"></span>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="notification.title"></p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5" x-text="notification.message"></p>
                                            </div>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0" x-text="notification.time_ago"></span>
                                        </div>
                                    </div>

                                    <!-- Unread indicator -->
                                    <span x-show="!notification.read_at" class="mt-2 h-2 w-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Pagination -->
                    <div x-show="lastPage > 1" x-cloak class="flex items-center justify-between mt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
                        </p>
                        <div class="flex gap-2">
                            <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                                    class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                Previous
                            </button>
                            <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage"
                                    class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                Next
                            </button>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        function notificationManager() {
            return {
                loading: false,
                notifications: [],
                counts: { unread: 0, applications: 0, payments: 0, connections: 0 },
                filter: null,
                category: null,
                search: '',
                currentPage: 1,
                lastPage: 1,
                total: 0,
                searchTimeout: null,

                init() {
                    this.fetchNotifications();
                    this.fetchCounts();
                },

                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        params.set('page', this.currentPage);
                        if (this.filter) params.set('filter', this.filter);
                        if (this.category) params.set('category', this.category);
                        if (this.search) params.set('search', this.search);

                        const res = await fetch(`/api/notifications/?${params}`);
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = data.data;
                            this.currentPage = data.meta.current_page;
                            this.lastPage = data.meta.last_page;
                            this.total = data.meta.total;
                        }
                    } catch (e) {
                        console.error('Failed to fetch notifications:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchCounts() {
                    try {
                        const res = await fetch('/api/notifications/category-counts');
                        const data = await res.json();
                        if (data.success) {
                            this.counts = data.data;
                        }
                    } catch (e) {
                        console.error('Failed to fetch counts:', e);
                    }
                },

                setFilter(value) {
                    this.filter = this.filter === value ? null : value;
                    this.currentPage = 1;
                    this.fetchNotifications();
                },

                setCategory(value) {
                    this.category = this.category === value ? null : value;
                    this.currentPage = 1;
                    this.fetchNotifications();
                },

                onSearch() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.currentPage = 1;
                        this.fetchNotifications();
                    }, 300);
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.lastPage) {
                        this.currentPage = page;
                        this.fetchNotifications();
                    }
                },

                async markRead(notification) {
                    if (!notification.read_at) {
                        try {
                            await fetch(`/api/notifications/${notification.id}/read`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                            });
                            notification.read_at = new Date().toISOString();
                            this.counts.unread = Math.max(0, this.counts.unread - 1);
                        } catch (e) {
                            console.error('Failed to mark as read:', e);
                        }
                    }
                    if (notification.link) {
                        window.location.href = notification.link;
                    }
                },

                async markAllRead() {
                    try {
                        await fetch('/api/notifications/read-all', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        this.notifications.forEach(n => n.read_at = new Date().toISOString());
                        this.counts.unread = 0;
                        this.fetchNotifications();
                    } catch (e) {
                        console.error('Failed to mark all as read:', e);
                    }
                },

                getColorClass(color) {
                    const map = {
                        blue: 'bg-blue-500',
                        green: 'bg-green-500',
                        red: 'bg-red-500',
                        amber: 'bg-amber-500',
                        indigo: 'bg-indigo-500',
                        gray: 'bg-gray-400'
                    };
                    return map[color] || map.gray;
                }
            };
        }
    </script>
</x-app-layout>
