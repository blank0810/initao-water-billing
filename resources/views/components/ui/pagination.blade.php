<!-- Standardized Pagination Component -->
<div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
        <span>Showing</span>
        <select id="{{ $id ?? 'pageSize' }}" onchange="{{ $onchange ?? 'updatePageSize()' }}" class="mx-2 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span>of <span id="{{ $totalId ?? 'totalRecords' }}">0</span> entries</span>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="{{ $prevClick ?? 'prevPage()' }}" id="{{ $prevId ?? 'prevBtn' }}" disabled class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <i class="fas fa-chevron-left"></i>
        </button>
        <span class="text-sm text-gray-700 dark:text-gray-300">
            Page <span id="{{ $currentPageId ?? 'currentPage' }}">1</span> of <span id="{{ $totalPagesId ?? 'totalPages' }}">1</span>
        </span>
        <button onclick="{{ $nextClick ?? 'nextPage()' }}" id="{{ $nextId ?? 'nextBtn' }}" disabled class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>
