<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                <i class="fas fa-tachometer-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                            Meter Assignment
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Assign and manage customer meters</p>
                    </div>
                    <a href="{{ route('meter.management') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Meter Management
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <input type="text" id="searchInput" placeholder="Search by customer name or meter ID..."
                    class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
            </div>

            <!-- Meter Assignment Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Meter ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Installation Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Initial Reading</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const meterAssignments = [
            { meter_id: 'MTR-1001', customer_code: 'CUST-2024-009', customer_name: 'David Wilson', installation_date: '2024-01-10', initial_reading: 0, status: 'ACTIVE' },
            { meter_id: 'MTR-1002', customer_code: 'CUST-2024-010', customer_name: 'Emma Brown', installation_date: '2024-01-09', initial_reading: 0, status: 'ACTIVE' },
            { meter_id: 'MTR-1003', customer_code: 'CUST-2024-011', customer_name: 'Robert Taylor', installation_date: '2024-01-08', initial_reading: 0, status: 'ACTIVE' },
            { meter_id: 'MTR-1004', customer_code: 'CUST-2024-012', customer_name: 'Jennifer Davis', installation_date: '2024-01-07', initial_reading: 0, status: 'ACTIVE' },
        ];

        let filteredAssignments = [...meterAssignments];

        function renderTable() {
            const tableBody = document.getElementById('assignmentTable');

            if (filteredAssignments.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No meter assignments found</td></tr>';
                return;
            }

            tableBody.innerHTML = filteredAssignments.map(assignment => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">${assignment.meter_id}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${assignment.customer_name}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">${assignment.customer_code}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${new Date(assignment.installation_date).toLocaleDateString()}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-gray-100">${assignment.initial_reading.toFixed(2)} m³</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                            ${assignment.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="viewDetails('${assignment.meter_id}')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editAssignment('${assignment.meter_id}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function viewDetails(meterId) {
            const assignment = meterAssignments.find(a => a.meter_id === meterId);
            if (assignment) {
                alert(`Meter Details:\n\nMeter ID: ${assignment.meter_id}\nCustomer: ${assignment.customer_name}\nInstallation Date: ${new Date(assignment.installation_date).toLocaleDateString()}\nInitial Reading: ${assignment.initial_reading} m³\nStatus: ${assignment.status}`);
            }
        }

        function editAssignment(meterId) {
            alert('Edit functionality will be implemented');
        }

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            filteredAssignments = meterAssignments.filter(a =>
                a.customer_name.toLowerCase().includes(query) ||
                a.meter_id.toLowerCase().includes(query) ||
                a.customer_code.toLowerCase().includes(query)
            );
            renderTable();
        });

        renderTable();
    </script>
</x-app-layout>
