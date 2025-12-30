/**
 * Declined Customers Data & Management
 */

const declinedCustomersData = [
    {
        customer_code: 'CUST-2024-013',
        customer_name: 'Michael Santos',
        address: 'Purok 7, Riverside, Barangay East',
        area: 'Zone A',
        meterReader: 'John Smith',
        date_declined: '2024-01-18',
        reason: 'Incomplete documentation - Missing proof of residence',
        reference_no: 'REF-2024-001',
        declined_by: 'Admin User'
    },
    {
        customer_code: 'CUST-2024-014',
        customer_name: 'Jennifer Cruz',
        address: 'Purok 3, Maligaya, Barangay West',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        date_declined: '2024-01-17',
        reason: 'Invalid ID provided',
        reference_no: 'REF-2024-002',
        declined_by: 'Admin User'
    },
    {
        customer_code: 'CUST-2024-015',
        customer_name: 'Ricardo Mendoza',
        address: 'Purok 5, Dampol, Barangay South',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        date_declined: '2024-01-16',
        reason: 'Duplicate application detected',
        reference_no: 'REF-2024-003',
        declined_by: 'Admin User'
    },
    {
        customer_code: 'CUST-2024-016',
        customer_name: 'Angela Reyes',
        address: 'Purok 2, San Jose, Barangay East',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        date_declined: '2024-01-15',
        reason: 'Property ownership verification failed',
        reference_no: 'REF-2024-004',
        declined_by: 'Admin User'
    },
    {
        customer_code: 'CUST-2024-017',
        customer_name: 'Thomas Anderson',
        address: 'Purok 8, Greenfield, Barangay North',
        area: 'Zone A',
        meterReader: 'John Smith',
        date_declined: '2024-01-14',
        reason: 'Outstanding balance from previous account',
        reference_no: 'REF-2024-005',
        declined_by: 'Admin User'
    },
    {
        customer_code: 'CUST-2024-018',
        customer_name: 'Patricia Gonzales',
        address: 'Purok 1, Sunset View, Barangay West',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        date_declined: '2024-01-13',
        reason: 'Area not yet covered by service',
        reference_no: 'REF-2024-006',
        declined_by: 'Admin User'
    }
];

class DeclinedCustomerManager {
    constructor() {
        this.declinedCustomers = [...declinedCustomersData];
        this.filteredCustomers = [...this.declinedCustomers];
        this.currentPage = 1;
        this.pageSize = 10;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.init();
    }

    init() {
        this.renderTable();
        this.bindEvents();
        this.updatePagination();
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value.toLowerCase());
            });
        }

        const pageSizeSelect = document.getElementById('pageSizeSelect');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (e) => {
                this.pageSize = parseInt(e.target.value);
                this.currentPage = 1;
                this.renderTable();
                this.updatePagination();
            });
        }

        const prevBtn = document.getElementById('prevPageBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevPage());
        }

        const nextBtn = document.getElementById('nextPageBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        window.addEventListener('restore-application', (e) => {
            this.openRestoreModal(e.detail);
        });

        window.addEventListener('confirm-restore', (e) => {
            this.handleRestore(e.detail);
        });
    }

    handleSearch(query) {
        if (!query) {
            this.filteredCustomers = [...this.declinedCustomers];
        } else {
            this.filteredCustomers = this.declinedCustomers.filter(c =>
                c.customer_name.toLowerCase().includes(query) ||
                c.customer_code.toLowerCase().includes(query) ||
                c.address.toLowerCase().includes(query) ||
                c.reason.toLowerCase().includes(query)
            );
        }
        this.currentPage = 1;
        this.renderTable();
        this.updatePagination();
    }

    sortBy(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        this.filteredCustomers.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (column === 'date_declined') {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
            }

            if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        this.renderTable();
    }

    get paginatedData() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredCustomers.slice(start, start + this.pageSize);
    }

    get totalPages() {
        return Math.ceil(this.filteredCustomers.length / this.pageSize) || 1;
    }

    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderTable();
            this.updatePagination();
        }
    }

    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.renderTable();
            this.updatePagination();
        }
    }

    updatePagination() {
        const start = this.filteredCustomers.length === 0 ? 0 : ((this.currentPage - 1) * this.pageSize) + 1;
        const end = Math.min(this.currentPage * this.pageSize, this.filteredCustomers.length);

        document.getElementById('currentPageNum').textContent = this.currentPage;
        document.getElementById('totalPagesNum').textContent = this.totalPages;
        document.getElementById('startRecord').textContent = start;
        document.getElementById('endRecord').textContent = end;
        document.getElementById('totalRecords').textContent = this.filteredCustomers.length;

        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === this.totalPages;
    }

    renderTable() {
        const tableBody = document.getElementById('declinedTable');
        if (!tableBody) return;

        const data = this.paginatedData;

        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-lg font-medium">No declined customers</p>
                        <p class="text-sm">All applications are in good standing</p>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = data.map(customer => {
            const initials = customer.customer_name.split(' ').map(n => n[0]).join('');
            
            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white font-semibold">
                                ${initials}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${customer.customer_name}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customer.address}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ${customer.area || 'N/A'}
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${new Date(customer.date_declined).toLocaleDateString()}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customer.reason}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            By: ${customer.declined_by}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">${customer.reference_no}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="window.declinedManager.restoreApplication('${customer.customer_code}')" 
                                class="w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center mx-auto"
                                title="Restore">
                            <i class="fas fa-undo"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    restoreApplication(customerCode) {
        window.dispatchEvent(new CustomEvent('restore-application', { detail: customerCode }));
    }

    openRestoreModal(customerCode) {
        const customer = this.declinedCustomers.find(c => c.customer_code === customerCode);
        if (customer) {
            setTimeout(() => {
                document.getElementById('restore-customer-name').textContent = customer.customer_name;
                document.getElementById('restore-customer-code').textContent = customer.customer_code;
                document.getElementById('restore-decline-reason').textContent = customer.reason;
            }, 100);
        }
    }

    handleRestore(detail) {
        const customerCode = typeof detail === 'string' ? detail : detail.customerCode;
        const index = this.declinedCustomers.findIndex(c => c.customer_code === customerCode);
        
        if (index !== -1) {
            const customer = this.declinedCustomers[index];
            this.declinedCustomers.splice(index, 1);
            this.filteredCustomers = this.filteredCustomers.filter(c => c.customer_code !== customerCode);
            
            if (this.paginatedData.length === 0 && this.currentPage > 1) {
                this.currentPage--;
            }
            
            this.renderTable();
            this.updatePagination();
            this.showToast(`${customer.customer_name}'s application restored successfully`, 'success');
        }
    }

    showToast(message, type = 'info') {
        const colors = { 
            success: 'bg-green-500', 
            error: 'bg-red-500', 
            info: 'bg-blue-500' 
        };
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity flex items-center gap-2`;
        toast.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.declinedManager = new DeclinedCustomerManager();
    window.declinedCustomers = declinedCustomersData;
});

export { declinedCustomersData };
