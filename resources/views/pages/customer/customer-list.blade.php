<x-app-layout>
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
                            <a href="{{ route('customer.add') }}"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Customer
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-4">
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
                                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search customers...">
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
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="cust_id">
                                            <div class="flex items-center">
                                                ID
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="name">
                                            <div class="flex items-center">
                                                Customer Name
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3">Location</th>
                                        <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" data-sort="created_at">
                                            <div class="flex items-center">
                                                Created On
                                                <svg class="w-3 h-3 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">Status</th>
                                        <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customer-table-body">
                                    <!-- Loading state -->
                                    <tr id="loading-row">
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <svg class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                            </svg>
                                            <p class="mt-2 text-gray-500 dark:text-gray-400">Loading customers...</p>
                                        </td>
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
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="view-applications-modal">
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
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-customer-modal">
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
                        <button type="button" data-modal-hide="edit-customer-modal" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
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
                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal">
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
                    <button type="button" data-modal-hide="delete-modal" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // State management
        let currentPage = 1;
        let perPage = 10;
        let searchQuery = '';
        let statusFilter = '';
        let sortColumn = 'created_at';
        let sortDirection = 'desc';

        document.addEventListener('DOMContentLoaded', function() {
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

        async function loadCustomers() {
            const tbody = document.getElementById('customer-table-body');
            const loadingRow = document.getElementById('loading-row');

            // Show loading
            loadingRow.classList.remove('hidden');

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

                // Hide loading
                loadingRow.classList.add('hidden');

                // Clear tbody (keep loading row)
                Array.from(tbody.children).forEach(row => {
                    if (row.id !== 'loading-row') {
                        row.remove();
                    }
                });

                // Populate table
                if (data.data && data.data.length > 0) {
                    data.data.forEach(customer => {
                        const row = createCustomerRow(customer);
                        tbody.appendChild(row);
                    });
                } else {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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
                loadingRow.classList.add('hidden');
            }
        }

        function createCustomerRow(customer) {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';

            const initials = customer.first_name.charAt(0) + customer.last_name.charAt(0);

            row.innerHTML = `
                <td class="px-6 py-4 font-mono text-gray-900 dark:text-white">
                    ${customer.cust_id}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            ${initials}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${customer.customer_name}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${customer.resolution_no}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-900 dark:text-white">
                    ${customer.location || '-'}
                </td>
                <td class="px-6 py-4 text-gray-900 dark:text-white">
                    ${customer.created_at}
                </td>
                <td class="px-6 py-4 text-center">
                    ${customer.status_badge}
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <button onclick="viewCustomer(${customer.cust_id})"
                            class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            View
                        </button>
                        <button onclick="editCustomer(${customer.cust_id})"
                            class="text-green-600 dark:text-green-400 hover:underline font-medium">
                            Edit
                        </button>
                        <button onclick="deleteCustomer(${customer.cust_id})"
                            class="text-red-600 dark:text-red-400 hover:underline font-medium">
                            Delete
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

                // Open modal
                const modal = FlowbiteInstances.getInstance('Modal', 'view-applications-modal');
                if (modal) {
                    modal.show();
                }
            } catch (error) {
                console.error('Error loading applications:', error);
                alert('Error loading service applications');
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

                // Open modal
                const modal = FlowbiteInstances.getInstance('Modal', 'edit-customer-modal');
                if (modal) {
                    modal.show();
                }
            } catch (error) {
                console.error('Error loading customer:', error);
                alert('Error loading customer details');
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
                    const modal = FlowbiteInstances.getInstance('Modal', 'edit-customer-modal');
                    if (modal) {
                        modal.hide();
                    }

                    // Reload table
                    loadCustomers();

                    alert('Customer updated successfully!');
                } else {
                    throw new Error(result.message || 'Failed to update customer');
                }
            } catch (error) {
                console.error('Error updating customer:', error);
                alert('Error: ' + error.message);
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
                                const modal = FlowbiteInstances.getInstance('Modal', 'delete-modal');
                                if (modal) {
                                    modal.hide();
                                }

                                // Reload table
                                loadCustomers();

                                alert('Customer deleted successfully!');
                            } else {
                                throw new Error(result.message || 'Failed to delete customer');
                            }
                        } catch (error) {
                            console.error('Error deleting customer:', error);
                            alert('Error: ' + error.message);
                        }
                    };
                }

                // Open modal
                const modal = FlowbiteInstances.getInstance('Modal', 'delete-modal');
                if (modal) {
                    modal.show();
                }
            } catch (error) {
                console.error('Error checking delete eligibility:', error);
                alert('Error checking if customer can be deleted');
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
