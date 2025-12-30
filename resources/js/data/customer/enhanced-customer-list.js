import { enhancedCustomerData } from './enhanced-customer-data.js';

(function() {
    const tbody = document.getElementById('consumer-documents-tbody');
    const connectionsTbody = document.getElementById('connections-tbody');
    if (!tbody) return;

    let currentPage = 1;
    let pageSize = 10;
    let filteredData = [...enhancedCustomerData];

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    function renderCustomersTable() {
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const pageData = filteredData.slice(start, end);

        tbody.innerHTML = '';

        pageData.forEach(customer => {
            const fullName = `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`.replace(/\s+/g, ' ');
            const currentBill = Math.floor(Math.random() * 2000) + 500;
            const status = Math.random() > 0.2 ? 'Active' : 'Overdue';
            const statusColor = status === 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            ${getInitials(fullName)}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${customer.account_no || customer.customer_code}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.address}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.registration_type}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.area}</div>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">₱${currentBill.toLocaleString()}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                        ${status}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="viewCustomer('${customer.customer_code}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editCustomer('${customer.customer_code}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        updatePagination();
    }

    function renderConnectionsTable() {
        if (!connectionsTbody) return;

        const connectionsData = enhancedCustomerData.filter(c => 
            c.workflow_status === 'ACTIVE_CONSUMER' || 
            c.workflow_status === 'METER_ASSIGNED' || 
            c.workflow_status === 'CONNECTION_SCHEDULED'
        );

        connectionsTbody.innerHTML = '';

        connectionsData.forEach(customer => {
            const fullName = `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`.replace(/\s+/g, ' ');
            const statusColor = customer.workflow_status === 'ACTIVE_CONSUMER' 
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                : customer.workflow_status === 'METER_ASSIGNED'
                ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100">${customer.account_no || 'N/A'}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            ${getInitials(fullName)}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${customer.meterReader}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.area}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">${customer.meter_no || 'Not Assigned'}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.meter_installed_date ? new Date(customer.meter_installed_date).toLocaleDateString() : 'Not Installed'}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                        ${customer.workflow_status.replace(/_/g, ' ')}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="viewCustomer('${customer.customer_code}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${customer.workflow_status === 'CONNECTION_SCHEDULED' ? `
                            <button onclick="assignMeter('${customer.customer_code}')" class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300 transition-colors" title="Assign Meter">
                                <i class="fas fa-tools"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            `;
            connectionsTbody.appendChild(row);
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / pageSize) || 1;
        document.getElementById('consumerCurrentPage').textContent = currentPage;
        document.getElementById('consumerTotalPages').textContent = totalPages;
        document.getElementById('consumerTotalRecords').textContent = filteredData.length;

        const prevBtn = document.getElementById('consumerPrevBtn');
        const nextBtn = document.getElementById('consumerNextBtn');
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
    }

    window.consumerPagination = {
        updatePageSize: (size) => {
            pageSize = parseInt(size);
            currentPage = 1;
            renderCustomersTable();
        },
        prevPage: () => {
            if (currentPage > 1) {
                currentPage--;
                renderCustomersTable();
            }
        },
        nextPage: () => {
            const totalPages = Math.ceil(filteredData.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                renderCustomersTable();
            }
        }
    };

    window.switchTab = function(tab) {
        const customersTab = document.getElementById('customers-tab');
        const connectionsTab = document.getElementById('connections-tab');
        const tabCustomers = document.getElementById('tab-customers');
        const tabConnections = document.getElementById('tab-connections');

        if (tab === 'customers') {
            customersTab.classList.remove('hidden');
            connectionsTab.classList.add('hidden');
            tabCustomers.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            tabCustomers.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            tabConnections.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            tabConnections.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        } else {
            customersTab.classList.add('hidden');
            connectionsTab.classList.remove('hidden');
            tabConnections.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            tabConnections.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            tabCustomers.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            tabCustomers.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            renderConnectionsTable();
        }
    };

    window.viewCustomer = function(customerCode) {
        window.location.href = `/customer/details/${customerCode}`;
    };

    window.editCustomer = function(customerCode) {
        const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`,
                Email: customer.email || '',
                AreaCode: customer.address,
                Status: customer.workflow_status
            };
            window.dispatchEvent(new CustomEvent('edit-customer', { detail: customerData }));
        }
    };

    window.assignMeter = function(customerCode) {
        alert(`Assign meter functionality for ${customerCode} - Coming soon!`);
    };

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            filteredData = enhancedCustomerData.filter(c => 
                `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase().includes(query) ||
                c.customer_code.toLowerCase().includes(query) ||
                c.address.toLowerCase().includes(query)
            );
            currentPage = 1;
            renderCustomersTable();
        });
    }

    renderCustomersTable();
    renderConnectionsTable();
})();
