// Customer dummy data with workflow statuses
const customerAllData = [
    { customer_code: 'CUST-2024-001', cust_first_name: 'Juan', cust_last_name: 'Dela Cruz', address: 'Purok 1, Poblacion', meter_no: null, account_no: null, workflow_status: 'PENDING_DOCS', documents_printed_at: null, payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-15', documents_printed_count: 0 },
    { customer_code: 'CUST-2024-002', cust_first_name: 'Maria', cust_last_name: 'Santos', address: 'Purok 2, Central', meter_no: null, account_no: null, workflow_status: 'PENDING_DOCS', documents_printed_at: null, payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-14', documents_printed_count: 0 },
    { customer_code: 'CUST-2024-003', cust_first_name: 'Pedro', cust_last_name: 'Garcia', address: 'Purok 3, San Jose', meter_no: null, account_no: null, workflow_status: 'DOCS_PRINTED', documents_printed_at: '2024-01-16 10:30:00', payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-13', documents_printed_count: 2 },
    { customer_code: 'CUST-2024-004', cust_first_name: 'Ana', cust_last_name: 'Rodriguez', address: 'Purok 1, Poblacion', meter_no: null, account_no: null, workflow_status: 'DOCS_PRINTED', documents_printed_at: '2024-01-15 14:20:00', payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-12', documents_printed_count: 1 },
    { customer_code: 'CUST-2024-005', cust_first_name: 'Carlos', cust_last_name: 'Lopez', address: 'Purok 4, Maligaya', meter_no: null, account_no: null, workflow_status: 'PAYMENT_PENDING', documents_printed_at: '2024-01-14 09:15:00', payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-11', documents_printed_count: 3 },
    { customer_code: 'CUST-2024-006', cust_first_name: 'Lisa', cust_last_name: 'Chen', address: 'Purok 2, Central', meter_no: null, account_no: null, workflow_status: 'PAYMENT_PENDING', documents_printed_at: '2024-01-13 11:45:00', payment_completed_at: null, requirements_complete: 0, created_at: '2024-01-10', documents_printed_count: 2 },
    { customer_code: 'CUST-2024-007', cust_first_name: 'Mark', cust_last_name: 'Johnson', address: 'Purok 5, San Roque', meter_no: null, account_no: 'ACC-2024-5001', workflow_status: 'PAYMENT_COMPLETED', documents_printed_at: '2024-01-12 08:30:00', payment_completed_at: '2024-01-12 14:00:00', requirements_complete: 1, created_at: '2024-01-09', documents_printed_count: 4 },
    { customer_code: 'CUST-2024-008', cust_first_name: 'Sofia', cust_last_name: 'Martinez', address: 'Purok 1, Poblacion', meter_no: null, account_no: 'ACC-2024-5002', workflow_status: 'PAYMENT_COMPLETED', documents_printed_at: '2024-01-11 15:00:00', payment_completed_at: '2024-01-11 16:30:00', requirements_complete: 1, created_at: '2024-01-08', documents_printed_count: 3 },
    { customer_code: 'CUST-2024-009', cust_first_name: 'David', cust_last_name: 'Wilson', address: 'Purok 6, Dampol', meter_no: 'MTR-1001', account_no: 'ACC-2024-5003', workflow_status: 'APPROVED', documents_printed_at: '2024-01-10 10:00:00', payment_completed_at: '2024-01-10 15:00:00', requirements_complete: 1, created_at: '2024-01-07', documents_printed_count: 5 },
    { customer_code: 'CUST-2024-010', cust_first_name: 'Emma', cust_last_name: 'Brown', address: 'Purok 3, Central', meter_no: 'MTR-1002', account_no: 'ACC-2024-5004', workflow_status: 'APPROVED', documents_printed_at: '2024-01-09 13:30:00', payment_completed_at: '2024-01-09 16:00:00', requirements_complete: 1, created_at: '2024-01-06', documents_printed_count: 2 },
    { customer_code: 'CUST-2024-011', cust_first_name: 'Robert', cust_last_name: 'Taylor', address: 'Purok 2, Poblacion', meter_no: 'MTR-1003', account_no: 'ACC-2024-5005', workflow_status: 'CONNECTED', documents_printed_at: '2024-01-08 09:00:00', payment_completed_at: '2024-01-08 14:00:00', requirements_complete: 1, created_at: '2024-01-05', documents_printed_count: 6 },
    { customer_code: 'CUST-2024-012', cust_first_name: 'Jennifer', cust_last_name: 'Davis', address: 'Purok 4, San Jose', meter_no: 'MTR-1004', account_no: 'ACC-2024-5006', workflow_status: 'CONNECTED', documents_printed_at: '2024-01-07 14:15:00', payment_completed_at: '2024-01-07 16:00:00', requirements_complete: 1, created_at: '2024-01-04', documents_printed_count: 4 },
];

// Export data for other pages
window.customerAllData = customerAllData;

// Workflow status configuration
const workflowStatuses = {
    'PENDING_DOCS': { label: 'Pending Documents', color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200', icon: 'fa-clock' },
    'DOCS_PRINTED': { label: 'Documents Printed', color: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200', icon: 'fa-print' },
    'PAYMENT_PENDING': { label: 'Payment Pending', color: 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200', icon: 'fa-credit-card' },
    'PAYMENT_COMPLETED': { label: 'Payment Completed', color: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200', icon: 'fa-check-circle' },
    'APPROVED': { label: 'Approved', color: 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200', icon: 'fa-thumbs-up' },
    'CONNECTED': { label: 'Connected', color: 'bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-200', icon: 'fa-plug' }
};

// Print customer form using UnifiedPrintSystem
function printCustomerFormDirect(customer) {
    // Use UnifiedPrintSystem if available, fallback to window function
    if (window.UnifiedPrintSystem) {
        window.UnifiedPrintSystem.printCustomerForm(customer);
    } else if (window.printCustomerForm) {
        window.printCustomerForm(customer);
    }
}

// Print requirement receipt
function printRequirementReceipt(customer) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Requirement Receipt - ${customer.customer_code}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info { margin: 10px 0; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Water Service Application</h2>
                <h3>Requirement Receipt</h3>
            </div>
            <div class="info"><strong>Customer Code:</strong> ${customer.customer_code}</div>
            <div class="info"><strong>Name:</strong> ${customer.cust_first_name} ${customer.cust_last_name}</div>
            <div class="info"><strong>Address:</strong> ${customer.address}</div>
            <div class="info"><strong>Date Applied:</strong> ${new Date(customer.created_at).toLocaleDateString()}</div>
            <div class="info"><strong>Date Printed:</strong> ${new Date().toLocaleString()}</div>
            <div class="footer">
                <p>Please proceed to payment processing.</p>
                <p>Keep this receipt for your records.</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Customer list page script
(function () {
    if (!document.getElementById('customerTable')) return;

    const tableBody = document.getElementById('customerTable');
    let rowsPerPage = 10;
    let currentPage = 1;
    let filteredCustomers = [...customerAllData];

    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const searchInput = document.getElementById('searchInput');
    const pageSizeSelect = document.getElementById('customerPageSize');

    function renderTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageCustomers = filteredCustomers.slice(start, end);

        pageCustomers.forEach(customer => {
            const statusConfig = workflowStatuses[customer.workflow_status] || workflowStatuses['PENDING_DOCS'];
            const canSendPayment = ['DOCS_PRINTED', 'PAYMENT_PENDING', 'PAYMENT_COMPLETED', 'APPROVED', 'CONNECTED'].includes(customer.workflow_status);

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';

            // Only show customers that are not PAYMENT_COMPLETED or APPROVED
            if (['PAYMENT_COMPLETED', 'APPROVED', 'CONNECTED'].includes(customer.workflow_status)) {
                return;
            }

            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${customer.cust_first_name} ${customer.cust_last_name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${customer.address}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full ${statusConfig.color}">
                        <i class="fas ${statusConfig.icon}"></i>
                        ${statusConfig.label}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-1">
                        <button onclick="sendForPayment('${customer.customer_code}')" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition-colors">
                            <i class="fas fa-credit-card mr-1"></i>Payment
                        </button>
                        ${['PAYMENT_COMPLETED'].includes(customer.workflow_status) ? `
                            <button onclick="sendForApproval('${customer.customer_code}')" class="px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs transition-colors">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                        ` : ''}
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <button onclick="printCustomerForm('${customer.customer_code}')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 transition-colors" title="Print">
                        <i class="fas fa-print"></i>
                        <span id="print-count-${customer.customer_code}" class="text-xs">${getPrintCount(customer.customer_code)}</span>
                    </button>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="viewCustomer('${customer.customer_code}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editCustomer('${customer.customer_code}')" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCustomer('${customer.customer_code}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });

        updatePagination();
    }


    // --- Search ---
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            const query = e.target.value.toLowerCase();
            filteredCustomers = customerAllData.filter(c =>
                c.customer_code.toLowerCase().includes(query) ||
                (c.cust_first_name + ' ' + c.cust_last_name).toLowerCase().includes(query) ||
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
        
        document.getElementById('currentPage').textContent = currentPage;
        document.getElementById('totalPages').textContent = totalPages;
        document.getElementById('startRecord').textContent = total === 0 ? 0 : start;
        document.getElementById('endRecord').textContent = end;
        document.getElementById('totalRecords').textContent = total;
        
        if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage >= totalPages;
    }
    
    // --- Page Size Change ---
    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', (e) => {
            rowsPerPage = parseInt(e.target.value) || 10;
            currentPage = 1;
            renderTable();
        });
    }
    
    // --- Pagination ---
    prevPageBtn?.addEventListener('click', () => { if(currentPage>1) currentPage--; renderTable(); });
    nextPageBtn?.addEventListener('click', () => { if(currentPage*rowsPerPage<filteredCustomers.length) currentPage++; renderTable(); });

    // Global functions
    window.printDocuments = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (!customer) return;

        // Update workflow status and increment print count
        customer.workflow_status = 'DOCS_PRINTED';
        customer.documents_printed_at = new Date().toISOString();
        customer.documents_printed_count = (customer.documents_printed_count || 0) + 1;

        // Log to workflow history (simulated)
        console.log('Workflow History:', {
            customer_code: customerCode,
            action: 'DOCS_PRINTED',
            timestamp: new Date().toISOString()
        });

        // Print receipt
        printRequirementReceipt(customer);

        // Re-render table
        renderTable();
        showAlert(`Documents printed successfully! Total prints: ${customer.documents_printed_count}`, 'success');
    };

    window.sendForPayment = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (!customer) return;

        // Store customer data in sessionStorage for the payment page
        sessionStorage.setItem('selectedCustomer', JSON.stringify(customer));
        
        window.location.href = `/customer/payment/${encodeURIComponent(customerCode)}`;
    };

    window.sendForApproval = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (!customer) return;

        // Store customer data for approval page
        sessionStorage.setItem('selectedCustomerForApproval', JSON.stringify(customer));
        
        window.location.href = '/customer/approve-customer';
    };

    window.viewCustomer = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                Email: customer.email || 'N/A',
                AreaCode: customer.address,
                DateApplied: customer.created_at,
                Status: workflowStatuses[customer.workflow_status].label
            };
            window.dispatchEvent(new CustomEvent('view-customer', { detail: customerData }));
        }
    };

    window.editCustomer = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                Email: customer.email || '',
                AreaCode: customer.address,
                Status: workflowStatuses[customer.workflow_status].label
            };
            window.dispatchEvent(new CustomEvent('edit-customer', { detail: customerData }));
        }
    };

    window.deleteCustomer = function(customerCode) {
        window.dispatchEvent(new CustomEvent('delete-customer', { detail: customerCode }));
    };

    // Print counter functions using localStorage
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

    window.printCustomerForm = function(customerCode) {
        const customer = customerAllData.find(c => c.customer_code === customerCode);
        if (customer) {
            const customerData = {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                Email: customer.email || 'N/A',
                AreaCode: customer.address,
                DateApplied: customer.created_at,
                Status: workflowStatuses[customer.workflow_status].label
            };

            // Print customer form
            printCustomerFormDirect(customerData);

            // Update print count in localStorage
            const newCount = incrementPrintCount(customerCode);
            
            // Update the display
            const countElement = document.getElementById(`print-count-${customerCode}`);
            if (countElement) {
                countElement.textContent = newCount;
            }
            
            showAlert('Customer form printed successfully!', 'success');
        }
    };

    // Alert function
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


    // Modal event listeners
    window.addEventListener('save-customer', function(e) {
        const customerData = e.detail;
        const customer = customerAllData.find(c => c.customer_code === customerData.id);
        if (customer) {
            const nameParts = customerData.CustomerName.split(' ');
            customer.cust_first_name = nameParts[0] || '';
            customer.cust_last_name = nameParts.slice(1).join(' ') || '';
            customer.email = customerData.Email;
            customer.address = customerData.AreaCode;

            renderTable();
            showAlert('Customer updated successfully!', 'success');
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
    });

    window.addEventListener('confirm-delete-customer', function(e) {
        const customerCode = e.detail;
        const customerIndex = customerAllData.findIndex(c => c.customer_code === customerCode);
        if (customerIndex !== -1) {
            const customer = customerAllData[customerIndex];
            customerAllData.splice(customerIndex, 1);
            filteredCustomers = customerAllData.filter(c => {
                const query = searchInput?.value.toLowerCase() || '';
                return c.customer_code.toLowerCase().includes(query) ||
                       (c.cust_first_name + ' ' + c.cust_last_name).toLowerCase().includes(query) ||
                       c.address.toLowerCase().includes(query);
            });

            renderTable();
            showAlert(`Customer ${customer.cust_first_name} ${customer.cust_last_name} deleted successfully!`, 'success');
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
    });

    // --- Initial render ---
    renderTable();

})();

