<!-- Alpine.js Pagination Component for Admin Config Pages -->
<div x-show="!loading && pagination.total > 0" class="mt-4">
    <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Per Page Selector & Info -->
        <div class="flex items-center gap-4">
            <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                <span>Show</span>
                <select
                    x-model="pagination.perPage"
                    @change="fetchItems()"
                    class="mx-2 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
            <div class="text-sm text-gray-700 dark:text-gray-300">
                <span>Showing</span>
                <span class="font-medium" x-text="pagination.from"></span>
                <span>to</span>
                <span class="font-medium" x-text="pagination.to"></span>
                <span>of</span>
                <span class="font-medium" x-text="pagination.total"></span>
                <span>total</span>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-2">
            <!-- First Page -->
            <button
                @click="goToPage(1)"
                :disabled="pagination.currentPage === 1"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === 1 }"
            >
                <i class="fas fa-angle-double-left"></i>
            </button>

            <!-- Previous Page -->
            <button
                @click="goToPage(pagination.currentPage - 1)"
                :disabled="pagination.currentPage === 1"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === 1 }"
            >
                <i class="fas fa-chevron-left"></i>
            </button>

            <!-- Page Info -->
            <span class="text-sm text-gray-700 dark:text-gray-300 px-3">
                Page <span class="font-medium" x-text="pagination.currentPage"></span>
                of <span class="font-medium" x-text="pagination.lastPage"></span>
            </span>

            <!-- Next Page -->
            <button
                @click="goToPage(pagination.currentPage + 1)"
                :disabled="pagination.currentPage === pagination.lastPage"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === pagination.lastPage }"
            >
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Last Page -->
            <button
                @click="goToPage(pagination.lastPage)"
                :disabled="pagination.currentPage === pagination.lastPage"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === pagination.lastPage }"
            >
                <i class="fas fa-angle-double-right"></i>
            </button>
        </div>
    </div>
</div>
