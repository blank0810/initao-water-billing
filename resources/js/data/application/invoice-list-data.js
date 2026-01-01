/**
 * Invoice List Data & Management
 */

const invoiceListData = [
    { 
        invoice_id: 'INV-2024-1001', 
        customer_code: 'CUST-2024-003', 
        customer_name: 'Pedro Garcia', 
        invoice_date: '2024-01-16', 
        due_date: '2024-01-26',
        total_amount: 3500, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1002', 
        customer_code: 'CUST-2024-004', 
        customer_name: 'Ana Rodriguez', 
        invoice_date: '2024-01-15', 
        due_date: '2024-01-25',
        total_amount: 3500, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1003', 
        customer_code: 'CUST-2024-005', 
        customer_name: 'Carlos Lopez', 
        invoice_date: '2024-01-14', 
        due_date: '2024-01-24',
        total_amount: 3500, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1004', 
        customer_code: 'CUST-2024-006', 
        customer_name: 'Lisa Chen', 
        invoice_date: '2024-01-13', 
        due_date: '2024-01-23',
        total_amount: 3800, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200},
            {description: 'Inspection Fee', amount: 300}
        ]
    },
    { 
        invoice_id: 'INV-2024-1005', 
        customer_code: 'CUST-2024-007', 
        customer_name: 'Mark Johnson', 
        invoice_date: '2024-01-12', 
        due_date: '2024-01-22',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-20',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1006', 
        customer_code: 'CUST-2024-008', 
        customer_name: 'Sofia Martinez', 
        invoice_date: '2024-01-11', 
        due_date: '2024-01-21',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-19',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1007', 
        customer_code: 'CUST-2024-009', 
        customer_name: 'David Wilson', 
        invoice_date: '2024-01-10', 
        due_date: '2024-01-20',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-18',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1008', 
        customer_code: 'CUST-2024-010', 
        customer_name: 'Emma Brown', 
        invoice_date: '2024-01-09', 
        due_date: '2024-01-19',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-17',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1009', 
        customer_code: 'CUST-2024-011', 
        customer_name: 'Robert Taylor', 
        invoice_date: '2024-01-08', 
        due_date: '2024-01-18',
        total_amount: 4200, 
        payment_status: 'PAID',
        paid_date: '2024-01-16',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1500},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200},
            {description: 'Express Service', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1010', 
        customer_code: 'CUST-2024-012', 
        customer_name: 'Sarah Johnson', 
        invoice_date: '2024-01-07', 
        due_date: '2024-01-17',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-15',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1011', 
        customer_code: 'CUST-2024-013', 
        customer_name: 'Michael Santos', 
        invoice_date: '2024-01-06', 
        due_date: '2024-01-16',
        total_amount: 3500, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1012', 
        customer_code: 'CUST-2024-014', 
        customer_name: 'Jennifer Cruz', 
        invoice_date: '2024-01-05', 
        due_date: '2024-01-15',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-14',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1013', 
        customer_code: 'CUST-2024-015', 
        customer_name: 'Ricardo Mendoza', 
        invoice_date: '2024-01-04', 
        due_date: '2024-01-14',
        total_amount: 3800, 
        payment_status: 'PAID',
        paid_date: '2024-01-13',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200},
            {description: 'Inspection Fee', amount: 300}
        ]
    },
    { 
        invoice_id: 'INV-2024-1014', 
        customer_code: 'CUST-2024-016', 
        customer_name: 'Angela Reyes', 
        invoice_date: '2024-01-03', 
        due_date: '2024-01-13',
        total_amount: 3500, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1015', 
        customer_code: 'CUST-2024-017', 
        customer_name: 'Thomas Anderson', 
        invoice_date: '2024-01-02', 
        due_date: '2024-01-12',
        total_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-11',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    }
];

class InvoiceListManager {
    constructor() {
        this.invoices = [...invoiceListData];
        this.filteredInvoices = [...this.invoices];
        this.statusFilter = 'ALL';
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

        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.statusFilter = e.target.value;
                this.applyFilters();
            });
        }

        const pageSizeSelect = document.getElementById('invoicePageSize');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (e) => {
                this.pageSize = parseInt(e.target.value);
                this.currentPage = 1;
                this.renderTable();
                this.updatePagination();
            });
        }

        const prevBtn = document.getElementById('invoicePrevBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevPage());
        }

        const nextBtn = document.getElementById('invoiceNextBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }
    }

    handleSearch(query) {
        this.searchQuery = query;
        this.applyFilters();
    }

    applyFilters() {
        let filtered = [...this.invoices];

        // Apply status filter
        if (this.statusFilter !== 'ALL') {
            filtered = filtered.filter(inv => inv.payment_status === this.statusFilter);
        }

        // Apply search filter
        if (this.searchQuery) {
            filtered = filtered.filter(inv =>
                inv.invoice_id.toLowerCase().includes(this.searchQuery) ||
                inv.customer_name.toLowerCase().includes(this.searchQuery) ||
                inv.customer_code.toLowerCase().includes(this.searchQuery)
            );
        }

        this.filteredInvoices = filtered;
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

        this.filteredInvoices.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (column === 'invoice_date' || column === 'due_date' || column === 'paid_date') {
                aVal = aVal ? new Date(aVal) : new Date(0);
                bVal = bVal ? new Date(bVal) : new Date(0);
            }

            if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        this.renderTable();
    }

    get paginatedData() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredInvoices.slice(start, start + this.pageSize);
    }

    get totalPages() {
        return Math.ceil(this.filteredInvoices.length / this.pageSize) || 1;
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
        const start = this.filteredInvoices.length === 0 ? 0 : ((this.currentPage - 1) * this.pageSize) + 1;
        const end = Math.min(this.currentPage * this.pageSize, this.filteredInvoices.length);

        const currentPageEl = document.getElementById('invoiceCurrentPage');
        const totalPagesEl = document.getElementById('invoiceTotalPages');
        const startRecordEl = document.getElementById('invoiceStartRecord');
        const endRecordEl = document.getElementById('invoiceEndRecord');
        const totalRecordsEl = document.getElementById('invoiceTotalRecords');

        if (currentPageEl) currentPageEl.textContent = this.currentPage;
        if (totalPagesEl) totalPagesEl.textContent = this.totalPages;
        if (startRecordEl) startRecordEl.textContent = start;
        if (endRecordEl) endRecordEl.textContent = end;
        if (totalRecordsEl) totalRecordsEl.textContent = this.filteredInvoices.length;

        const prevBtn = document.getElementById('invoicePrevBtn');
        const nextBtn = document.getElementById('invoiceNextBtn');
        
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === this.totalPages;
    }

    renderTable() {
        const tableBody = document.getElementById('invoiceTable');
        if (!tableBody) return;

        const data = this.paginatedData;

        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p class="text-lg font-medium">No invoices found</p>
                        <p class="text-sm">Try adjusting your filters</p>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = data.map(invoice => {
            const initials = invoice.customer_name.split(' ').map(n => n[0]).join('');
            const statusColor = invoice.payment_status === 'PAID' 
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' 
                : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200';

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">${invoice.invoice_id}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                ${initials}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${invoice.customer_name}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${invoice.customer_code}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${new Date(invoice.invoice_date).toLocaleDateString()}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${invoice.paid_date ? new Date(invoice.paid_date).toLocaleDateString() : '-'}</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">₱${invoice.total_amount.toLocaleString()}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                            ${invoice.payment_status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="window.invoiceManager.viewInvoice('${invoice.invoice_id}')" 
                                class="w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center mx-auto"
                                title="View Invoice">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    viewInvoice(invoiceId) {
        const invoice = this.invoices.find(i => i.invoice_id === invoiceId);
        if (!invoice) return;

        window.dispatchEvent(new CustomEvent('show-invoice', { 
            detail: {
                invoice_id: invoice.invoice_id,
                customer_name: invoice.customer_name,
                customer_code: invoice.customer_code,
                amount: invoice.total_amount,
                status: invoice.payment_status,
                invoice_date: invoice.invoice_date,
                due_date: invoice.due_date,
                paid_date: invoice.paid_date,
                items: invoice.items
            }
        }));
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.invoiceManager = new InvoiceListManager();
});

export { invoiceListData };
