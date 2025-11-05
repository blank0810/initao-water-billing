<x-app-layout>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">

        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-full mx-auto">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Customer List</h1>
                            <p class="text-gray-600 dark:text-gray-400">Manage all registered customers</p>
                        </div>
                        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                            <!-- Add Customer Button -->
                            <a href="{{ route('customer.add') }}"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg> Add Customer
                            </a>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="overflow-x-auto p-6">
                            <table id="customersTable" class="w-full display responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer Name</th>
                                        <th>Location</th>
                                        <th>Created On</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        /* Dark mode support for DataTables */
        .dark #customersTable {
            color: #fff;
        }

        .dark .dataTables_wrapper .dataTables_filter input,
        .dark .dataTables_wrapper .dataTables_length select {
            background-color: #374151;
            color: #fff;
            border-color: #4b5563;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #fff !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #374151;
            color: #fff !important;
        }

        .dark .dataTables_wrapper .dataTables_info {
            color: #9ca3af;
        }

        /* Custom styling for action buttons */
        .action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.15s;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }
    </style>
    @endpush

    @push('scripts')
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('customer.list') }}',
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                columns: [
                    {
                        data: 'cust_id',
                        name: 'cust_id',
                        width: '80px',
                        render: function(data) {
                            return '<span class="font-mono text-gray-900 dark:text-white">' + data + '</span>';
                        }
                    },
                    {
                        data: 'customer_name',
                        name: 'cust_last_name',
                        render: function(data, type, row) {
                            const initials = row.first_name.charAt(0) + row.last_name.charAt(0);
                            return `
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="text-gray-900 dark:text-white font-medium">${data}</div>
                                        <div class="text-gray-500 dark:text-gray-400 text-sm">${row.resolution_no}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'location',
                        name: 'land_mark',
                        render: function(data) {
                            return '<span class="text-gray-900 dark:text-white">' + data + '</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'create_date',
                        width: '120px',
                        render: function(data) {
                            return '<span class="text-gray-900 dark:text-white">' + data + '</span>';
                        }
                    },
                    {
                        data: 'status_badge',
                        name: 'stat_id',
                        width: '100px',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'cust_id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '150px',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="viewCustomer(${data})"
                                        class="action-btn text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900">
                                        View
                                    </button>
                                    <button onclick="editCustomer(${data})"
                                        class="action-btn text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900">
                                        Edit
                                    </button>
                                    <button onclick="deleteCustomer(${data})"
                                        class="action-btn text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                        Delete
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[3, 'desc']], // Order by created_at descending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search customers...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ customers",
                    infoEmpty: "No customers found",
                    infoFiltered: "(filtered from _MAX_ total customers)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        });

        // Action functions
        function viewCustomer(id) {
            alert('View customer ' + id);
            // TODO: Implement view customer modal or redirect
        }

        function editCustomer(id) {
            alert('Edit customer ' + id);
            // TODO: Implement edit customer modal or redirect
        }

        function deleteCustomer(id) {
            if(confirm('Are you sure you want to delete this customer?')) {
                alert('Delete customer ' + id);
                // TODO: Implement delete customer functionality
            }
        }
    </script>
    @endpush
</x-app-layout>
