@push('styles')
<style>
    /* Skeleton loader animation */
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

    /* Action buttons hover effect */
    .action-buttons {
        opacity: 1; /* Changed from 0 to always visible */
        transition: opacity 0.2s ease;
    }

    /* Smooth checkbox animation */
    input[type="checkbox"] {
        transition: all 0.2s ease;
    }

    /* Sticky header */
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background: inherit;
    }

    /* Badge pulse animation */
    @keyframes pulse-badge {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .badge-new {
        animation: pulse-badge 2s ease-in-out infinite;
    }
</style>
@endpush

<x-app-layout>
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-4"></div>

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto">

                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Customer List</h1>
                                <p class="text-gray-600 dark:text-gray-400">Manage all registered customers and their service applications</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button onclick="showKeyboardShortcuts()" title="Keyboard Shortcuts (Press ?)" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('connection.service-application.create') }}"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Application
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Toolbar (Hidden by default) -->
                    <div id="bulkActionsToolbar" class="hidden mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-300">
                                    <span id="selectedCount">0</span> customer(s) selected
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="exportSelected('csv')" class="text-sm px-3 py-2 text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export CSV
                                </button>
                                <button onclick="exportSelected('excel')" class="text-sm px-3 py-2 text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-500 dark:focus:ring-green-800">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export Excel
                                </button>
                                <button onclick="bulkDelete()" class="text-sm px-3 py-2 text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete Selected
                                </button>
                                <button onclick="clearSelection()" class="text-sm px-3 py-2 text-gray-700 hover:text-white border border-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-4 mb-4">
                            <!-- Search -->
                            <div class="flex-1">
                                <label for="table-search" class="sr-only">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>
                                    <input type="text" id="table-search"
                                        class="block w-full p-2.5 pl-10 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search customers... (Press / to focus)">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">/</kbd>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <select id="status-filter"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="ACTIVE">Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                    <option value="PENDING">Pending</option>
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div>
                                <select id="per-page"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>

                            <!-- Column Visibility Toggle -->
                            <div>
                                <button id="columnToggleButton" type="button"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Columns
                                </button>
                                <!-- Dropdown -->
                                <div id="columnToggleDropdown" class="hidden absolute z-10 mt-2 w-48 bg-white rounded-lg shadow-lg dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                <input type="checkbox" checked data-column="id" class="column-toggle w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <span class="ms-2">ID</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                <input type="checkbox" checked data-column="name" class="column-toggle w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <span class="ms-2">Customer Name</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                <input type="checkbox" checked data-column="location" class="column-toggle w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <span class="ms-2">Location</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                <input type="checkbox" checked data-column="created" class="column-toggle w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <span class="ms-2">Created On</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                <input type="checkbox" checked data-column="status" class="column-toggle w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <span class="ms-2">Status</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Export All Button -->
                            <div>
                                <button id="exportAllButton" type="button"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export
                                </button>
                                <!-- Dropdown -->
                                <div id="exportAllDropdown" class="hidden absolute z-10 mt-2 w-44 bg-white rounded-lg shadow-lg dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <a href="#" onclick="exportAll('csv')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Export as CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="exportAll('excel')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Export as Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="exportAll('pdf')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                Export as PDF
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky-header">
                                    <tr>
                                        <!-- Bulk Selection Checkbox -->
                                        <th scope="col" class="px-4 py-3">
                                            <input id="select-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </th>
                                        <th scope="col" data-column-name="id" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="cust_id">
                                            <div class="flex items-center">
                                                ID
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" data-column-name="name" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="name">
                                            <div class="flex items-center">
                                                Customer Name
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" data-column-name="location" class="px-6 py-3">Location</th>
                                        <th scope="col" data-column-name="created" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="created_at">
                                            <div class="flex items-center">
                                                Created On
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" data-column-name="status" class="px-6 py-3 text-center">Status</th>
                                        <th scope="col" class="px-6 py-3 text-center sticky right-0 bg-gray-50 dark:bg-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customer-table-body">
                                    <!-- Skeleton Loading State -->
                                    <tr id="skeleton-row-1" class="skeleton-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-4"><div class="skeleton w-4 h-4 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-16 rounded"></div></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="skeleton h-10 w-10 rounded-full mr-4"></div>
                                                <div class="space-y-2">
                                                    <div class="skeleton h-4 w-32 rounded"></div>
                                                    <div class="skeleton h-3 w-24 rounded"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-40 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-24 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-6 w-16 rounded-full mx-auto"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-32 rounded mx-auto"></div></td>
                                    </tr>
                                    <tr id="skeleton-row-2" class="skeleton-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-4"><div class="skeleton w-4 h-4 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-16 rounded"></div></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="skeleton h-10 w-10 rounded-full mr-4"></div>
                                                <div class="space-y-2">
                                                    <div class="skeleton h-4 w-32 rounded"></div>
                                                    <div class="skeleton h-3 w-24 rounded"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-40 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-24 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-6 w-16 rounded-full mx-auto"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-32 rounded mx-auto"></div></td>
                                    </tr>
                                    <tr id="skeleton-row-3" class="skeleton-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-4"><div class="skeleton w-4 h-4 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-16 rounded"></div></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="skeleton h-10 w-10 rounded-full mr-4"></div>
                                                <div class="space-y-2">
                                                    <div class="skeleton h-4 w-32 rounded"></div>
                                                    <div class="skeleton h-3 w-24 rounded"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-40 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-24 rounded"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-6 w-16 rounded-full mx-auto"></div></td>
                                        <td class="px-6 py-4"><div class="skeleton h-4 w-32 rounded mx-auto"></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-700 dark:text-gray-400 mb-4 sm:mb-0">
                                Showing <span id="showing-from" class="font-semibold text-gray-900 dark:text-white">0</span> to
                                <span id="showing-to" class="font-semibold text-gray-900 dark:text-white">0</span> of
                                <span id="total-records" class="font-semibold text-gray-900 dark:text-white">0</span> customers
                            </div>
                            <nav aria-label="Page navigation">
                                <ul id="pagination-controls" class="inline-flex -space-x-px text-sm">
                                    <!-- Pagination buttons will be inserted here -->
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- View Service Applications Modal -->
    <div id="view-applications-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Service Applications
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="view-customer-name"></p>
                    </div>
                    <button type="button" onclick="closeViewApplicationsModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <div id="applications-list" class="space-y-3">
                        <!-- Applications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="edit-customer-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Customer Information
                    </h3>
                    <button type="button" onclick="closeEditCustomerModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form id="edit-customer-form" class="p-4 md:p-5">
                    <input type="hidden" id="edit-customer-id" name="cust_id">

                    <div class="grid gap-4 mb-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="edit-first-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name</label>
                                <input type="text" id="edit-first-name" name="cust_first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white uppercase" required>
                            </div>
                            <div>
                                <label for="edit-middle-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Middle Name</label>
                                <input type="text" id="edit-middle-name" name="cust_middle_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white uppercase">
                            </div>
                            <div>
                                <label for="edit-last-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                                <input type="text" id="edit-last-name" name="cust_last_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white uppercase" required>
                            </div>
                        </div>
                        <div>
                            <label for="edit-customer-type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer Type</label>
                            <select id="edit-customer-type" name="c_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                                <option value="RESIDENTIAL">Residential</option>
                                <option value="COMMERCIAL">Commercial</option>
                                <option value="INDUSTRIAL">Industrial</option>
                                <option value="GOVERNMENT">Government</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit-landmark" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Landmark</label>
                            <input type="text" id="edit-landmark" name="land_mark" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white uppercase">
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 border-t border-gray-200 dark:border-gray-600 pt-4">
                        <button type="button" onclick="closeEditCustomerModal()" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeDeleteModal()" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this customer?</h3>
                    <p id="delete-warning" class="mb-5 text-sm text-gray-500 dark:text-gray-400"></p>
                    <button id="confirm-delete-btn" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, delete
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @vite(['resources/js/utils/customer-print.js'])
    <script>
        // ============================================
        // STATE MANAGEMENT
        // ============================================
        let currentPage = 1;
        let perPage = 10;
        let searchQuery = '';
        let statusFilter = '';
        let sortColumn = 'created_at';
        let sortDirection = 'desc';
        let selectedCustomers = new Set();
        let allCustomersData = [];
        let hiddenColumns = new Set();

        // Modal instances
        let viewApplicationsModal;
        let editCustomerModal;
        let deleteModal;

        document.addEventListener('DOMContentLoaded', function() {
            // Note: Modal initialization removed - using vanilla JS show/hide instead
            // initModals();

            // Load hidden columns from localStorage
            const savedHiddenColumns = localStorage.getItem('hiddenColumns');
            if (savedHiddenColumns) {
                hiddenColumns = new Set(JSON.parse(savedHiddenColumns));
                applyColumnVisibility();
            }

            // Initialize event listeners
            initBulkSelection();
            initDropdowns();
            initKeyboardShortcuts();
            initColumnToggles();

            loadCustomers();

            // Search
            document.getElementById('table-search').addEventListener('input', debounce(function(e) {
                searchQuery = e.target.value;
                currentPage = 1;
                loadCustomers();
            }, 300));

            // Status filter
            document.getElementById('status-filter').addEventListener('change', function(e) {
                statusFilter = e.target.value;
                currentPage = 1;
                loadCustomers();
            });

            // Per page
            document.getElementById('per-page').addEventListener('change', function(e) {
                perPage = parseInt(e.target.value);
                currentPage = 1;
                loadCustomers();
            });

            // Sort
            document.querySelectorAll('[data-sort]').forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.dataset.sort;
                    if (sortColumn === column) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortColumn = column;
                        sortDirection = 'asc';
                    }
                    loadCustomers();
                });
            });

            // Edit form submission
            document.getElementById('edit-customer-form').addEventListener('submit', handleEditSubmit);
        });

        // ============================================
        // MODAL INITIALIZATION
        // ============================================
        // DEPRECATED: Removed Flowbite Modal class initialization
        // Using vanilla JS show/hide instead (see close functions below)
        /*
        function initModals() {
            // Initialize View Applications Modal
            const viewAppModalEl = document.getElementById('view-applications-modal');
            if (viewAppModalEl) {
                viewApplicationsModal = new Modal(viewAppModalEl, {
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        console.log('View applications modal is hidden');
                    },
                    onShow: () => {
                        console.log('View applications modal is shown');
                    }
                });
            }

            // Initialize Edit Customer Modal
            const editModalEl = document.getElementById('edit-customer-modal');
            if (editModalEl) {
                editCustomerModal = new Modal(editModalEl, {
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        console.log('Edit customer modal is hidden');
                    },
                    onShow: () => {
                        console.log('Edit customer modal is shown');
                    }
                });
            }

            // Initialize Delete Modal
            const deleteModalEl = document.getElementById('delete-modal');
            if (deleteModalEl) {
                deleteModal = new Modal(deleteModalEl, {
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        console.log('Delete modal is hidden');
                    },
                    onShow: () => {
                        console.log('Delete modal is shown');
                    }
                });
            }
        }
        */

        // ============================================
        // MODAL CLOSE FUNCTIONS (Vanilla JS)
        // ============================================
        function closeViewApplicationsModal() {
            const modalEl = document.getElementById('view-applications-modal');
            modalEl.classList.add('hidden');
            modalEl.classList.remove('flex');
            modalEl.removeAttribute('aria-modal');
            modalEl.removeAttribute('role');
        }

        function closeEditCustomerModal() {
            const modalEl = document.getElementById('edit-customer-modal');
            modalEl.classList.add('hidden');
            modalEl.classList.remove('flex');
            modalEl.removeAttribute('aria-modal');
            modalEl.removeAttribute('role');
        }

        function closeDeleteModal() {
            const modalEl = document.getElementById('delete-modal');
            modalEl.classList.add('hidden');
            modalEl.classList.remove('flex');
            modalEl.removeAttribute('aria-modal');
            modalEl.removeAttribute('role');
        }

        // ============================================
        // TOAST NOTIFICATIONS
        // ============================================
        function showToast(type, message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const colors = {
                success: 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200',
                error: 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200',
                warning: 'text-orange-500 bg-orange-100 dark:bg-orange-700 dark:text-orange-200',
                info: 'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200'
            };

            const icons = {
                success: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>',
                error: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>',
                warning: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>',
                info: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>'
            };

            toast.className = `flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 transition-all duration-300 transform translate-x-0`;
            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${colors[type]} rounded-lg">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        ${icons[type]}
                    </svg>
                </div>
                <div class="ms-3 text-sm font-normal">${message}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.remove()">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // ============================================
        // BULK SELECTION
        // ============================================
        function initBulkSelection() {
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', handleSelectAll);
            }
        }

        function handleSelectAll(e) {
            const isChecked = e.target.checked;
            const checkboxes = document.querySelectorAll('.customer-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                const customerId = parseInt(checkbox.dataset.customerId);
                if (isChecked) {
                    selectedCustomers.add(customerId);
                } else {
                    selectedCustomers.delete(customerId);
                }
            });

            updateBulkToolbar();
        }

        function handleRowCheckbox(checkbox) {
            const customerId = parseInt(checkbox.dataset.customerId);

            if (checkbox.checked) {
                selectedCustomers.add(customerId);
            } else {
                selectedCustomers.delete(customerId);
                document.getElementById('select-all').checked = false;
            }

            updateBulkToolbar();

            // Check if all checkboxes are selected
            const allCheckboxes = document.querySelectorAll('.customer-checkbox');
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            if (allChecked && allCheckboxes.length > 0) {
                document.getElementById('select-all').checked = true;
            }
        }

        function updateBulkToolbar() {
            const toolbar = document.getElementById('bulkActionsToolbar');
            const count = selectedCustomers.size;

            if (count > 0) {
                toolbar.classList.remove('hidden');
                document.getElementById('selectedCount').textContent = count;
            } else {
                toolbar.classList.add('hidden');
            }
        }

        function clearSelection() {
            selectedCustomers.clear();
            document.getElementById('select-all').checked = false;
            document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false);
            updateBulkToolbar();
            showToast('info', 'Selection cleared');
        }

        // ============================================
        // EXPORT FUNCTIONALITY
        // ============================================
        function exportSelected(format) {
            if (selectedCustomers.size === 0) {
                showToast('warning', 'Please select customers to export');
                return;
            }

            const selectedData = allCustomersData.filter(c => selectedCustomers.has(c.cust_id));
            performExport(selectedData, format, `customers_selected_${selectedCustomers.size}`);
        }

        function exportAll(format) {
            if (allCustomersData.length === 0) {
                showToast('warning', 'No data to export');
                return;
            }

            performExport(allCustomersData, format, 'customers_all');
        }

        function performExport(data, format, filename) {
            if (format === 'csv') {
                exportToCSV(data, filename);
            } else if (format === 'excel') {
                exportToExcel(data, filename);
            } else if (format === 'pdf') {
                exportToPDF(data, filename);
            }
        }

        function exportToCSV(data, filename) {
            const headers = ['ID', 'First Name', 'Middle Name', 'Last Name', 'Customer Type', 'Location', 'Resolution No', 'Created At', 'Status'];
            const rows = data.map(c => [
                c.cust_id,
                c.cust_first_name || '',
                c.cust_middle_name || '',
                c.cust_last_name || '',
                c.c_type || '',
                c.location || '',
                c.resolution_no || '',
                c.created_at || '',
                c.status_text || ''
            ]);

            const csvContent = [
                headers.join(','),
                ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
            ].join('\n');

            downloadFile(csvContent, `${filename}.csv`, 'text/csv');
            showToast('success', `Exported ${data.length} customers to CSV`);
        }

        function exportToExcel(data, filename) {
            // Simple Excel export using HTML table method
            const headers = ['ID', 'First Name', 'Middle Name', 'Last Name', 'Customer Type', 'Location', 'Resolution No', 'Created At', 'Status'];
            let excelContent = '<table><thead><tr>';
            headers.forEach(h => excelContent += `<th>${h}</th>`);
            excelContent += '</tr></thead><tbody>';

            data.forEach(c => {
                excelContent += '<tr>';
                excelContent += `<td>${c.cust_id}</td>`;
                excelContent += `<td>${c.cust_first_name || ''}</td>`;
                excelContent += `<td>${c.cust_middle_name || ''}</td>`;
                excelContent += `<td>${c.cust_last_name || ''}</td>`;
                excelContent += `<td>${c.c_type || ''}</td>`;
                excelContent += `<td>${c.location || ''}</td>`;
                excelContent += `<td>${c.resolution_no || ''}</td>`;
                excelContent += `<td>${c.created_at || ''}</td>`;
                excelContent += `<td>${c.status_text || ''}</td>`;
                excelContent += '</tr>';
            });

            excelContent += '</tbody></table>';
            downloadFile(excelContent, `${filename}.xls`, 'application/vnd.ms-excel');
            showToast('success', `Exported ${data.length} customers to Excel`);
        }

        function exportToPDF(data, filename) {
            showToast('info', 'PDF export feature coming soon!');
            // PDF export would require a library like jsPDF
            // For now, we'll show a message
        }

        function downloadFile(content, filename, mimeType) {
            const blob = new Blob([content], { type: mimeType });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function bulkDelete() {
            if (selectedCustomers.size === 0) {
                showToast('warning', 'Please select customers to delete');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedCustomers.size} customer(s)? This action cannot be undone.`)) {
                // Here you would make API calls to delete the selected customers
                showToast('info', `Bulk delete functionality will be implemented with backend API`);
                // For now, just show message
            }
        }

        // ============================================
        // COLUMN VISIBILITY
        // ============================================
        function initColumnToggles() {
            const toggles = document.querySelectorAll('.column-toggle');
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const columnName = this.dataset.column;
                    if (this.checked) {
                        hiddenColumns.delete(columnName);
                    } else {
                        hiddenColumns.add(columnName);
                    }
                    saveColumnVisibility();
                    applyColumnVisibility();
                });
            });
        }

        function applyColumnVisibility() {
            // Apply to headers
            document.querySelectorAll('th[data-column-name]').forEach(th => {
                const columnName = th.dataset.columnName;
                if (hiddenColumns.has(columnName)) {
                    th.classList.add('hidden');
                } else {
                    th.classList.remove('hidden');
                }
            });

            // Apply to all table cells
            const columnIndexMap = {
                'id': 1,
                'name': 2,
                'location': 3,
                'created': 4,
                'status': 5
            };

            hiddenColumns.forEach(columnName => {
                const index = columnIndexMap[columnName];
                if (index) {
                    document.querySelectorAll(`tbody tr td:nth-child(${index + 1})`).forEach(td => {
                        td.classList.add('hidden');
                    });
                }
            });

            // Show visible columns
            Object.keys(columnIndexMap).forEach(columnName => {
                if (!hiddenColumns.has(columnName)) {
                    const index = columnIndexMap[columnName];
                    document.querySelectorAll(`tbody tr td:nth-child(${index + 1})`).forEach(td => {
                        td.classList.remove('hidden');
                    });
                }
            });

            // Update checkboxes
            document.querySelectorAll('.column-toggle').forEach(toggle => {
                toggle.checked = !hiddenColumns.has(toggle.dataset.column);
            });
        }

        function saveColumnVisibility() {
            localStorage.setItem('hiddenColumns', JSON.stringify(Array.from(hiddenColumns)));
        }

        // ============================================
        // KEYBOARD SHORTCUTS
        // ============================================
        function initKeyboardShortcuts() {
            document.addEventListener('keydown', function(e) {
                // Focus search on '/' key
                if (e.key === '/' && !isInputFocused()) {
                    e.preventDefault();
                    document.getElementById('table-search').focus();
                }

                // Clear search on 'Esc' key
                if (e.key === 'Escape') {
                    const searchInput = document.getElementById('table-search');
                    if (searchInput.value) {
                        searchInput.value = '';
                        searchQuery = '';
                        currentPage = 1;
                        loadCustomers();
                    }
                    // Also clear selection
                    if (selectedCustomers.size > 0) {
                        clearSelection();
                    }
                }

                // Show shortcuts modal on '?' key
                if (e.key === '?' && !isInputFocused()) {
                    e.preventDefault();
                    showKeyboardShortcuts();
                }
            });
        }

        function isInputFocused() {
            const activeElement = document.activeElement;
            return activeElement.tagName === 'INPUT' ||
                   activeElement.tagName === 'TEXTAREA' ||
                   activeElement.tagName === 'SELECT';
        }

        function showKeyboardShortcuts() {
            const shortcuts = [
                { key: '/', description: 'Focus search bar' },
                { key: 'Esc', description: 'Clear search and selections' },
                { key: '?', description: 'Show this help' }
            ];

            let message = '<div class="text-left"><strong class="block mb-2">Keyboard Shortcuts:</strong>';
            shortcuts.forEach(s => {
                message += `<div class="mb-1"><kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded dark:bg-gray-600 dark:text-gray-100">${s.key}</kbd> - ${s.description}</div>`;
            });
            message += '</div>';

            showToast('info', message);
        }

        // ============================================
        // DROPDOWN TOGGLES
        // ============================================
        function initDropdowns() {
            // Column toggle dropdown
            const columnToggleButton = document.getElementById('columnToggleButton');
            const columnToggleDropdown = document.getElementById('columnToggleDropdown');

            if (columnToggleButton && columnToggleDropdown) {
                columnToggleButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    columnToggleDropdown.classList.toggle('hidden');
                });
            }

            // Export all dropdown
            const exportAllButton = document.getElementById('exportAllButton');
            const exportAllDropdown = document.getElementById('exportAllDropdown');

            if (exportAllButton && exportAllDropdown) {
                exportAllButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    exportAllDropdown.classList.toggle('hidden');
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (columnToggleDropdown && !columnToggleDropdown.contains(e.target)) {
                    columnToggleDropdown.classList.add('hidden');
                }
                if (exportAllDropdown && !exportAllDropdown.contains(e.target)) {
                    exportAllDropdown.classList.add('hidden');
                }
            });
        }

        // ============================================
        // LOAD CUSTOMERS (Enhanced)
        // ============================================
        async function loadCustomers() {
            const tbody = document.getElementById('customer-table-body');
            const skeletonRows = document.querySelectorAll('.skeleton-row');

            // Show skeleton loading
            skeletonRows.forEach(row => row.classList.remove('hidden'));

            try {
                const params = new URLSearchParams({
                    page: currentPage,
                    per_page: perPage,
                    search: searchQuery,
                    status: statusFilter,
                    sort_column: sortColumn,
                    sort_direction: sortDirection
                });

                const response = await fetch(`{{ route('customer.list') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                // Store all customers data for export
                allCustomersData = data.data || [];

                // Hide skeleton loading
                skeletonRows.forEach(row => row.classList.add('hidden'));

                // Clear tbody (keep skeleton rows)
                Array.from(tbody.children).forEach(row => {
                    if (!row.classList.contains('skeleton-row')) {
                        row.remove();
                    }
                });

                // Populate table
                if (data.data && data.data.length > 0) {
                    data.data.forEach(customer => {
                        const row = createCustomerRow(customer);
                        tbody.appendChild(row);
                    });

                    // Apply column visibility after rendering
                    applyColumnVisibility();
                } else {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="mt-2">No customers found</p>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }

                // Update pagination
                updatePaginationInfo(data);
                renderPagination(data);

            } catch (error) {
                console.error('Error loading customers:', error);
                skeletonRows.forEach(row => row.classList.add('hidden'));
                showToast('error', 'Failed to load customers. Please try again.');
            }
        }

        function createCustomerRow(customer) {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';

            const initials = customer.cust_first_name.charAt(0) + customer.cust_last_name.charAt(0);

            // Check if customer is new (created within last 24 hours)
            const createdDate = new Date(customer.created_at);
            const now = new Date();
            const hoursDiff = (now - createdDate) / (1000 * 60 * 60);
            const isNew = hoursDiff < 24;

            // Check if customer is in selected list
            const isSelected = selectedCustomers.has(customer.cust_id);

            row.innerHTML = `
                <td class="px-4 py-4">
                    <input type="checkbox" ${isSelected ? 'checked' : ''}
                           class="customer-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                           data-customer-id="${customer.cust_id}"
                           onchange="handleRowCheckbox(this)">
                </td>
                <td data-column-name="id" class="px-6 py-4 font-mono text-gray-900 dark:text-white">
                    ${customer.cust_id}
                </td>
                <td data-column-name="name" class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            ${initials}
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">${customer.customer_name}</div>
                                ${isNew ? '<span class="ml-2 px-2 py-0.5 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300 badge-new">NEW</span>' : ''}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${customer.resolution_no}</div>
                        </div>
                    </div>
                </td>
                <td data-column-name="location" class="px-6 py-4 text-gray-900 dark:text-white">
                    ${customer.location || '-'}
                </td>
                <td data-column-name="created" class="px-6 py-4 text-gray-900 dark:text-white">
                    ${customer.created_at}
                </td>
                <td data-column-name="status" class="px-6 py-4 text-center">
                    ${customer.status_badge}
                </td>
                <td class="px-6 py-4 text-center sticky right-0 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-center space-x-2 action-buttons">
                        <button onclick="viewCustomer(${customer.cust_id})"
                            class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors"
                            title="View Customer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button onclick="editCustomer(${customer.cust_id})"
                            class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors"
                            title="Edit Customer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="deleteCustomer(${customer.cust_id})"
                            class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                            title="Delete Customer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            return row;
        }

        function updatePaginationInfo(data) {
            document.getElementById('showing-from').textContent = data.from || 0;
            document.getElementById('showing-to').textContent = data.to || 0;
            document.getElementById('total-records').textContent = data.total || 0;
        }

        function renderPagination(data) {
            const container = document.getElementById('pagination-controls');
            container.innerHTML = '';

            if (!data.last_page || data.last_page <= 1) return;

            // Previous button
            const prevBtn = document.createElement('li');
            prevBtn.innerHTML = `
                <a href="#" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${data.current_page === 1 ? 'pointer-events-none opacity-50' : ''}">
                    Previous
                </a>
            `;
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (data.current_page > 1) {
                    currentPage = data.current_page - 1;
                    loadCustomers();
                }
            });
            container.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= data.last_page; i++) {
                if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                    const pageBtn = document.createElement('li');
                    pageBtn.innerHTML = `
                        <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight ${i === data.current_page ? 'text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'}">
                            ${i}
                        </a>
                    `;
                    pageBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        loadCustomers();
                    });
                    container.appendChild(pageBtn);
                } else if (i === data.current_page - 2 || i === data.current_page + 2) {
                    const dots = document.createElement('li');
                    dots.innerHTML = `<span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">...</span>`;
                    container.appendChild(dots);
                }
            }

            // Next button
            const nextBtn = document.createElement('li');
            nextBtn.innerHTML = `
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${data.current_page === data.last_page ? 'pointer-events-none opacity-50' : ''}">
                    Next
                </a>
            `;
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (data.current_page < data.last_page) {
                    currentPage = data.current_page + 1;
                    loadCustomers();
                }
            });
            container.appendChild(nextBtn);
        }

        // View customer service applications
        async function viewCustomer(id) {
            try {
                const response = await fetch(`/api/customers/${id}/applications`);
                const data = await response.json();

                document.getElementById('view-customer-name').textContent = `Customer: ${data.customer.customer_name}`;

                const applicationsList = document.getElementById('applications-list');
                applicationsList.innerHTML = '';

                if (data.applications && data.applications.length > 0) {
                    data.applications.forEach(app => {
                        const appCard = document.createElement('div');
                        appCard.className = 'p-4 bg-gray-50 dark:bg-gray-600 rounded-lg';
                        appCard.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Application #${app.application_number}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Submitted: ${app.submitted_at}</p>
                                </div>
                                <span class="${app.status_class}">${app.status_text}</span>
                            </div>
                        `;
                        applicationsList.appendChild(appCard);
                    });
                } else {
                    applicationsList.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">No service applications found</p>';
                }

                // Open modal using vanilla JS
                const modalEl = document.getElementById('view-applications-modal');
                modalEl.classList.remove('hidden');
                modalEl.classList.add('flex');
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.setAttribute('role', 'dialog');
            } catch (error) {
                console.error('Error loading applications:', error);
                showToast('error', 'Error loading service applications');
            }
        }

        // Edit customer
        async function editCustomer(id) {
            try {
                const response = await fetch(`/api/customers/${id}`);
                const customer = await response.json();

                document.getElementById('edit-customer-id').value = customer.cust_id;
                document.getElementById('edit-first-name').value = customer.cust_first_name;
                document.getElementById('edit-middle-name').value = customer.cust_middle_name || '';
                document.getElementById('edit-last-name').value = customer.cust_last_name;
                document.getElementById('edit-customer-type').value = customer.c_type;
                document.getElementById('edit-landmark').value = customer.land_mark || '';

                // Open modal using vanilla JS
                const modalEl = document.getElementById('edit-customer-modal');
                modalEl.classList.remove('hidden');
                modalEl.classList.add('flex');
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.setAttribute('role', 'dialog');
            } catch (error) {
                console.error('Error loading customer:', error);
                showToast('error', 'Error loading customer details');
            }
        }

        // Handle edit form submission
        async function handleEditSubmit(e) {
            e.preventDefault();

            const customerId = document.getElementById('edit-customer-id').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/api/customers/${customerId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // Close modal
                    closeEditCustomerModal();

                    // Reload table
                    loadCustomers();

                    showToast('success', 'Customer updated successfully!');
                } else {
                    throw new Error(result.message || 'Failed to update customer');
                }
            } catch (error) {
                console.error('Error updating customer:', error);
                showToast('error', 'Error: ' + error.message);
            }
        }

        // Delete customer
        async function deleteCustomer(id) {
            try {
                // Check if customer can be deleted
                const checkResponse = await fetch(`/api/customers/${id}/can-delete`);
                const checkData = await checkResponse.json();

                const warningEl = document.getElementById('delete-warning');

                if (!checkData.can_delete) {
                    warningEl.textContent = checkData.message;
                    warningEl.classList.add('text-red-600', 'dark:text-red-400', 'font-medium');
                    document.getElementById('confirm-delete-btn').disabled = true;
                    document.getElementById('confirm-delete-btn').classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    warningEl.textContent = 'This customer has no active service applications and can be safely deleted.';
                    warningEl.classList.remove('text-red-600', 'dark:text-red-400', 'font-medium');
                    document.getElementById('confirm-delete-btn').disabled = false;
                    document.getElementById('confirm-delete-btn').classList.remove('opacity-50', 'cursor-not-allowed');

                    // Set up delete confirmation
                    document.getElementById('confirm-delete-btn').onclick = async function() {
                        try {
                            const response = await fetch(`/api/customers/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (response.ok) {
                                // Close modal
                                closeDeleteModal();

                                // Reload table
                                loadCustomers();

                                showToast('success', 'Customer deleted successfully!');
                            } else {
                                throw new Error(result.message || 'Failed to delete customer');
                            }
                        } catch (error) {
                            console.error('Error deleting customer:', error);
                            showToast('error', 'Error: ' + error.message);
                        }
                    };
                }

                // Open modal using vanilla JS
                const modalEl = document.getElementById('delete-modal');
                modalEl.classList.remove('hidden');
                modalEl.classList.add('flex');
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.setAttribute('role', 'dialog');
            } catch (error) {
                console.error('Error checking delete eligibility:', error);
                showToast('error', 'Error checking if customer can be deleted');
            }
        }

        // Debounce helper
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
    @endpush
</x-app-layout>
