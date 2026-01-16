import { consumerData } from './consumer.js';

(function() {
    const tbody = document.getElementById('consumer-documents-tbody');
    if (!tbody) return;

    let currentPage = 1;
    let pageSize = 10;
    let filteredData = [...consumerData];

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    function renderConsumersTable() {
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const pageData = filteredData.slice(start, end);

        tbody.innerHTML = '';

        pageData.forEach(consumer => {
            const statusColor = consumer.status === 'Active' 
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' 
                : consumer.status === 'Overdue'
                ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            ${getInitials(consumer.name)}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${consumer.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">ID: ${consumer.cust_id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${consumer.address}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">${consumer.meter_no}</div>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">${consumer.total_bill}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                        ${consumer.status}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/consumer/${consumer.cust_id}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        document.getElementById('consumerCurrentPage').textContent = currentPage;
        document.getElementById('consumerTotalPages').textContent = totalPages;
        document.getElementById('consumerTotalRecords').textContent = filteredData.length;

        document.getElementById('consumerPrevBtn').disabled = currentPage === 1;
        document.getElementById('consumerNextBtn').disabled = currentPage === totalPages;
    }

    // Global functions for pagination
    window.consumerPagination = {
        nextPage() {
            const totalPages = Math.ceil(filteredData.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                renderConsumersTable();
            }
        },
        prevPage() {
            if (currentPage > 1) {
                currentPage--;
                renderConsumersTable();
            }
        },
        updatePageSize(newSize) {
            pageSize = parseInt(newSize);
            currentPage = 1;
            renderConsumersTable();
        }
    };

    // Search and filter functionality
    window.searchAndFilterConsumers = function(searchTerm, filterValue) {
        filteredData = consumerData.filter(consumer => {
            const matchesSearch = !searchTerm || 
                consumer.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                consumer.address.toLowerCase().includes(searchTerm.toLowerCase()) ||
                consumer.cust_id.toString().includes(searchTerm);
            
            const matchesFilter = !filterValue || consumer.status === filterValue;
            
            return matchesSearch && matchesFilter;
        });
        
        currentPage = 1;
        renderConsumersTable();
    };

    // Initial render
    renderConsumersTable();
})();
