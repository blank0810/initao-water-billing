import { enhancedCustomerData } from './enhanced-customer-data.js';

window.enhancedCustomerData = enhancedCustomerData;

(function () {
    if (!document.getElementById('applicationTable')) return;

    const tableBody = document.getElementById('applicationTable');
    let rowsPerPage = 10;
    let currentPage = 1;
    let filteredCustomers = [...enhancedCustomerData];

    const prevPageBtn = document.getElementById('prevPageApp');
    const nextPageBtn = document.getElementById('nextPageApp');
    const searchInput = document.getElementById('searchInput');
    const pageSizeSelect = document.getElementById('appPageSize');

    function renderTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageCustomers = filteredCustomers.slice(start, end);

        pageCustomers.forEach(customer => {
            const fullName = `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`.replace(/\s+/g, ' ');
            const dateCreated = new Date(customer.create_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';

            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            ${fullName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${customer.phone}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.id_type}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.id_number}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.address}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.registration_type}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${customer.meterReader}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.area}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">${dateCreated}</div>
                </td>
                <td class="px-4 py-3 text-center">
                    <button onclick="printBothForms('${customer.customer_code}')" 
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" 
                            title="Print Application & Contract">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="viewCustomer('${customer.customer_code}')" 
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" 
                                title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editCustomer('${customer.customer_code}')" 
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" 
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCustomer('${customer.customer_code}')" 
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors" 
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });

        updatePagination();
    }

    if (searchInput) {
        searchInput.addEventListener('input', e => {
            const query = e.target.value.toLowerCase();
            filteredCustomers = enhancedCustomerData.filter(c =>
                c.customer_code.toLowerCase().includes(query) ||
                `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase().includes(query) ||
                c.address.toLowerCase().includes(query)
            );
            currentPage = 1;
            renderTable();
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredCustomers.length / rowsPerPage) || 1;
        const start = Math.min((currentPage - 1) * rowsPerPage + 1, filteredCustomers.length);
        const end = Math.min(currentPage * rowsPerPage, filteredCustomers.length);
        const total = filteredCustomers.length;
        
        document.getElementById('currentPageApp').textContent = currentPage;
        document.getElementById('totalPagesApp').textContent = totalPages;
        document.getElementById('startRecordApp').textContent = total === 0 ? 0 : start;
        document.getElementById('endRecordApp').textContent = end;
        document.getElementById('totalRecordsApp').textContent = total;
        
        if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage >= totalPages;
    }
    
    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', (e) => {
            rowsPerPage = parseInt(e.target.value) || 10;
            currentPage = 1;
            renderTable();
        });
    }
    
    prevPageBtn?.addEventListener('click', () => { if(currentPage>1) currentPage--; renderTable(); });
    nextPageBtn?.addEventListener('click', () => { if(currentPage*rowsPerPage<filteredCustomers.length) currentPage++; renderTable(); });

    function getPrintCount(customerCode) {
        const counts = JSON.parse(localStorage.getItem('printCounts') || '{}');
        const count = counts[customerCode] || 0;
        return count > 0 ? count : '';
    }

    function incrementPrintCount(customerCode) {
        const counts = JSON.parse(localStorage.getItem('printCounts') || '{}');
        counts[customerCode] = (counts[customerCode] || 0) + 1;
        localStorage.setItem('printCounts', JSON.stringify(counts));
        return counts[customerCode];
    }

    window.getPrintCount = getPrintCount;

    window.viewCustomer = function(customerCode) {
        const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`,
                Email: customer.email || 'N/A',
                AreaCode: customer.address,
                DateApplied: customer.create_date,
                Status: customer.workflow_status
            };
            window.dispatchEvent(new CustomEvent('view-customer', { detail: customerData }));
        }
    };

    window.editCustomer = function(customerCode) {
        const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`,
                suffix: customer.cust_suffix || '',
                Email: customer.email || '',
                AreaCode: customer.address,
                Status: customer.workflow_status
            };
            window.dispatchEvent(new CustomEvent('edit-customer', { detail: customerData }));
        }
    };

    window.deleteCustomer = function(customerCode) {
        window.dispatchEvent(new CustomEvent('delete-customer', { detail: customerCode }));
    };

    window.printBothForms = async function(customerCode) {
        const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`,
                Email: customer.email || 'N/A',
                AreaCode: customer.address,
                DateApplied: customer.create_date,
                Status: customer.workflow_status
            };

            if (window.UnifiedPrintSystem && window.MEEDOContractPrint) {
                // Fetch signatory data for MEEDO contract
                let signatories = {};
                try {
                    const params = new URLSearchParams();
                    params.append('keys[]', 'MEEDO_OFFICER');
                    const res = await fetch(`/config/document-signatories/js-data?${params}`);
                    if (res.ok) {
                        const json = await res.json();
                        if (json.data) {
                            // Map uppercase keys to lowercase for JS usage
                            Object.keys(json.data).forEach(key => {
                                signatories[key.toLowerCase()] = json.data[key];
                            });
                        }
                    }
                } catch (e) {
                    console.warn('Could not load signatory data:', e);
                }

                // Print Application Form first
                window.UnifiedPrintSystem.printServiceApplicationForm(customerData);

                // Print MEEDO Contract after a short delay
                setTimeout(() => {
                    window.MEEDOContractPrint.printWaterServiceContract(customerData, signatories);
                }, 2000);

                incrementPrintCount(customerCode);
                showAlert('Application Form & MEEDO Contract printed successfully!', 'success');
            } else {
                showAlert('Print systems not loaded', 'error');
            }
        }
    };

    window.printCustomerForm = function(customerCode) {
        printBothForms(customerCode);
    };

    function showAlert(message, type = 'info') {
        const alertColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };

        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 ${alertColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }

    window.addEventListener('save-customer', function(e) {
        const customerData = e.detail;
        const customer = enhancedCustomerData.find(c => c.customer_code === customerData.id);
        if (customer) {
            const nameParts = customerData.CustomerName.split(' ');
            customer.cust_first_name = nameParts[0] || '';
            customer.cust_middle_name = nameParts[1] || '';
            customer.cust_last_name = nameParts.slice(2).join(' ') || nameParts[1] || '';
            customer.email = customerData.Email;
            customer.address = customerData.AreaCode;

            renderTable();
            showAlert('Customer updated successfully!', 'success');
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
    });

    window.addEventListener('confirm-delete-customer', function(e) {
        const customerCode = e.detail;
        const customerIndex = enhancedCustomerData.findIndex(c => c.customer_code === customerCode);
        if (customerIndex !== -1) {
            const customer = enhancedCustomerData[customerIndex];
            enhancedCustomerData.splice(customerIndex, 1);
            filteredCustomers = enhancedCustomerData.filter(c => {
                const query = searchInput?.value.toLowerCase() || '';
                return c.customer_code.toLowerCase().includes(query) ||
                       `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase().includes(query) ||
                       c.address.toLowerCase().includes(query);
            });

            renderTable();
            showAlert(`Customer ${customer.cust_first_name} ${customer.cust_last_name} deleted successfully!`, 'success');
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
    });

    renderTable();
})();
