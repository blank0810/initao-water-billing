<!-- Recent Activities Card-->
<div x-data="recentActivities()" x-init="init()" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 flex flex-col" style="height: 500px;">
    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activities</h3>
        <div class="flex gap-2">
            <select x-model="dateFilter" @change="applyFilters()" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
            <select x-model="sortOrder" @change="applyFilters()" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>
        </div>
    </div>

    <!-- Loading Skeleton -->
    <template x-if="loading">
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="i in 5" :key="i">
                <div class="animate-pulse flex items-center gap-4 py-3">
                    <div class="flex-1">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40 mb-2"></div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-32"></div>
                    </div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-24"></div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full w-16"></div>
                </div>
            </template>
        </div>
    </template>

    <!-- Data Table -->
    <template x-if="!loading">
        <div class="flex-1 overflow-y-auto">
            <table class="min-w-full">
                <thead class="sticky top-0 bg-white dark:bg-gray-900 z-10">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="transaction in paginatedTransactions" :key="transaction.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="transaction.name"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="transaction.notes"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="transaction.type"></td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="transaction.amount"></td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400" x-text="transaction.date"></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" :class="getStatusClass(transaction.status)" x-text="transaction.status"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredTransactions.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No transactions found for the selected period
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <!-- Pagination -->
    <template x-if="!loading && filteredTransactions.length > 0">
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center justify-between">
                <button @click="prevPage" :disabled="currentPage === 1" 
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z" fill="currentColor"/>
                    </svg>
                    Previous
                </button>
                <span class="text-sm text-gray-700 dark:text-gray-400">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                    Next
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function recentActivities() {
    return {
        transactions: [],
        loading: true,
        dateFilter: 'all',
        sortOrder: 'newest',
        itemsPerPage: 5,
        currentPage: 1,

        init() {
            this.loadDummyData();
        },

        loadDummyData() {
            this.transactions = [
                    { id: 1, name: 'Juan Dela Cruz', type: 'Payment', amount: '₱1,250.50', date: 'Jan 15, 2025 10:30 AM', status: 'Paid', notes: 'Monthly bill payment', created_at: '2025-01-15T10:30:00' },
                    { id: 2, name: 'Maria Santos', type: 'Service Connection', amount: '₱3,500.00', date: 'Jan 14, 2025 02:15 PM', status: 'Approved', notes: 'New connection request', created_at: '2025-01-14T14:15:00' },
                    { id: 3, name: 'Pedro Reyes', type: 'Meter Reading', amount: '₱850.00', date: 'Jan 14, 2025 09:45 AM', status: 'Paid', notes: 'Quarterly reading', created_at: '2025-01-14T09:45:00' },
                    { id: 4, name: 'Ana Garcia', type: 'Payment', amount: '₱2,100.75', date: 'Jan 13, 2025 03:20 PM', status: 'Pending', notes: 'Awaiting confirmation', created_at: '2025-01-13T15:20:00' },
                    { id: 5, name: 'Jose Rizal', type: 'Account Update', amount: '₱0.00', date: 'Jan 13, 2025 11:00 AM', status: 'Approved', notes: 'Address change', created_at: '2025-01-13T11:00:00' },
                    { id: 6, name: 'Rosa Mendoza', type: 'Payment', amount: '₱1,890.25', date: 'Jan 12, 2025 04:30 PM', status: 'Failed', notes: 'Insufficient funds', created_at: '2025-01-12T16:30:00' },
                    { id: 7, name: 'Carlos Bautista', type: 'Service Connection', amount: '₱3,500.00', date: 'Jan 12, 2025 10:15 AM', status: 'Pending', notes: 'Under review', created_at: '2025-01-12T10:15:00' },
                    { id: 8, name: 'Elena Cruz', type: 'Payment', amount: '₱1,450.00', date: 'Jan 11, 2025 01:45 PM', status: 'Paid', notes: 'Monthly bill payment', created_at: '2025-01-11T13:45:00' },
                    { id: 9, name: 'Miguel Torres', type: 'Meter Reading', amount: '₱920.50', date: 'Jan 11, 2025 09:30 AM', status: 'Paid', notes: 'Regular reading', created_at: '2025-01-11T09:30:00' },
                    { id: 10, name: 'Sofia Ramos', type: 'Payment', amount: '₱1,675.00', date: 'Jan 10, 2025 02:00 PM', status: 'Paid', notes: 'Monthly bill payment', created_at: '2025-01-10T14:00:00' },
                    { id: 11, name: 'Diego Fernandez', type: 'Account Update', amount: '₱0.00', date: 'Jan 10, 2025 11:30 AM', status: 'Declined', notes: 'Invalid documents', created_at: '2025-01-10T11:30:00' },
                    { id: 12, name: 'Isabel Morales', type: 'Service Connection', amount: '₱3,500.00', date: 'Jan 09, 2025 03:45 PM', status: 'Approved', notes: 'New connection approved', created_at: '2025-01-09T15:45:00' },
                    { id: 13, name: 'Roberto Aquino', type: 'Payment', amount: '₱2,340.80', date: 'Jan 09, 2025 10:20 AM', status: 'Paid', notes: 'Monthly bill payment', created_at: '2025-01-09T10:20:00' },
                    { id: 14, name: 'Carmen Villanueva', type: 'Meter Reading', amount: '₱780.00', date: 'Jan 08, 2025 02:30 PM', status: 'Paid', notes: 'Quarterly reading', created_at: '2025-01-08T14:30:00' },
                    { id: 15, name: 'Luis Santiago', type: 'Payment', amount: '₱1,560.25', date: 'Jan 08, 2025 09:00 AM', status: 'Pending', notes: 'Processing payment', created_at: '2025-01-08T09:00:00' }
                ];
            this.loading = false;
        },

        get filteredTransactions() {
            let filtered = [...this.transactions];

            // Date filtering
            if (this.dateFilter !== 'all') {
                const now = new Date();
                filtered = filtered.filter(t => {
                    const transDate = new Date(t.created_at);
                    if (this.dateFilter === 'today') {
                        return transDate.toDateString() === now.toDateString();
                    } else if (this.dateFilter === 'week') {
                        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                        return transDate >= weekAgo;
                    } else if (this.dateFilter === 'month') {
                        return transDate.getMonth() === now.getMonth() && transDate.getFullYear() === now.getFullYear();
                    }
                    return true;
                });
            }

            // Sorting
            filtered.sort((a, b) => {
                const dateA = new Date(a.created_at);
                const dateB = new Date(b.created_at);
                return this.sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
            });

            return filtered;
        },

        get totalPages() {
            return Math.ceil(this.filteredTransactions.length / this.itemsPerPage) || 1;
        },

        get paginatedTransactions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredTransactions.slice(start, end);
        },

        applyFilters() {
            this.currentPage = 1;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        getStatusClass(status) {
            const classes = {
                'Paid': 'bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-300',
                'Pending': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                'Failed': 'bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-300',
                'Approved': 'bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                'Declined': 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
            };
            return classes[status] || '';
        }
    };
}
</script>
