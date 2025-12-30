import { paymentApplications, normalizeCustomerStatus, getCustomerStatusBadgeClass, formatCurrency } from './payment-data.js';

window.paymentApplications = paymentApplications;

class PaymentTableManager {
    constructor() {
        this.allData = [...paymentApplications];
        this.filteredData = [...this.allData];
        this.filters = { status: '', dateFrom: '', dateTo: '', search: '' };
        this.tableInstance = null;
        this.init();
    }

    init() {
        const checkTable = setInterval(() => {
            const instance = window.tableInstances?.paymentTable;
            if (instance) {
                clearInterval(checkTable);
                this.tableInstance = instance;
                this.updateTable();
            }
        }, 100);
    }

    handleSearch(query) {
        this.filters.search = query.toLowerCase();
        this.applyFilters();
    }

    handleFilter(type, value) {
        this.filters[type] = value;
        this.applyFilters();
    }

    applyFilters() {
        this.filteredData = this.allData.filter(app => {
            // Search filter
            if (this.filters.search) {
                const searchText = `${app.customer_name} ${app.customer_code} ${app.address}`.toLowerCase();
                if (!searchText.includes(this.filters.search)) return false;
            }

            // Status filter
            if (this.filters.status && normalizeCustomerStatus(app.status) !== this.filters.status) {
                return false;
            }

            // Date filters
            if (this.filters.dateFrom) {
                const appDate = new Date(app.processed_at);
                const fromDate = new Date(this.filters.dateFrom);
                if (appDate < fromDate) return false;
            }

            if (this.filters.dateTo) {
                const appDate = new Date(app.processed_at);
                const toDate = new Date(this.filters.dateTo);
                toDate.setHours(23, 59, 59);
                if (appDate > toDate) return false;
            }

            return true;
        });

        this.updateTable();
    }

    clearFilters() {
        this.filters = { status: '', dateFrom: '', dateTo: '', search: '' };
        const searchInput = document.getElementById('paymentTable_search');
        const filterSelect = document.getElementById('paymentTable_filter');
        const dateFrom = document.getElementById('paymentTable_dateFrom');
        const dateTo = document.getElementById('paymentTable_dateTo');
        
        if (searchInput) searchInput.value = '';
        if (filterSelect) filterSelect.value = '';
        if (dateFrom) dateFrom.value = '';
        if (dateTo) dateTo.value = '';
        
        this.applyFilters();
    }

    updateTable() {
        if (!this.tableInstance) return;

        this.tableInstance.data = this.filteredData.map(app => ({
            id: app.customer_code,
            customer_info: `<div class="text-sm font-medium text-gray-900 dark:text-white">${app.customer_name}</div><div class="text-xs text-gray-500">${app.customer_code}</div>`,
            address: app.address,
            status: `<span class="${getCustomerStatusBadgeClass(app.status)} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">${normalizeCustomerStatus(app.status)}</span>`,
            date_processed: new Date(app.processed_at).toLocaleDateString(),
            payment: `<span class="font-semibold text-gray-900 dark:text-white">${formatCurrency(app.amount_due)}</span>`,
            actions: `<button onclick="processPayment('${app.customer_code}')" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors"><i class="fas fa-money-bill-wave mr-1.5"></i>Process Payment</button>`
        }));
    }

    exportToExcel() {
        const data = this.filteredData.map(app => ({
            'Customer Name': app.customer_name,
            'Customer Code': app.customer_code,
            'Address': app.address,
            'Status': normalizeCustomerStatus(app.status),
            'Date': new Date(app.processed_at).toLocaleDateString(),
            'Amount': app.amount_due
        }));

        const csv = [
            Object.keys(data[0]).join(','),
            ...data.map(row => Object.values(row).map(val => `"${val}"`).join(','))
        ].join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `payment-management-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    exportToPDF() {
        const printWindow = window.open('', '_blank');
        const data = this.filteredData;

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Payment Management Report</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 30px; }
                    h1 { color: #1f2937; margin-bottom: 10px; }
                    .meta { color: #6b7280; font-size: 14px; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; font-size: 13px; }
                    th { background-color: #f3f4f6; font-weight: 600; color: #374151; }
                    tr:nth-child(even) { background-color: #f9fafb; }
                    .amount { text-align: right; font-weight: 600; }
                    @media print { body { padding: 15px; } }
                </style>
            </head>
            <body>
                <h1>Payment Management Report</h1>
                <div class="meta">Generated: ${new Date().toLocaleString()} | Total Records: ${data.length}</div>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(app => `
                            <tr>
                                <td><strong>${app.customer_name}</strong><br><small>${app.customer_code}</small></td>
                                <td>${app.address}</td>
                                <td>${normalizeCustomerStatus(app.status)}</td>
                                <td>${new Date(app.processed_at).toLocaleDateString()}</td>
                                <td class="amount">${formatCurrency(app.amount_due)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => printWindow.print(), 250);
    }
}

window.processPayment = function(code) {
    const customer = paymentApplications.find(c => c.customer_code === code);
    if (customer) {
        const nameParts = customer.customer_name.split(' ');
        sessionStorage.setItem('selectedCustomer', JSON.stringify({
            customer_code: customer.customer_code,
            cust_first_name: nameParts[0],
            cust_last_name: nameParts.slice(1).join(' '),
            address: customer.address,
            status: customer.status,
            account_no: customer.account_no || 'N/A',
            meter_no: customer.meter_no || 'N/A',
            create_date: customer.processed_at
        }));
        window.location.href = `/customer/payment/${code}`;
    }
};

if (document.getElementById('paymentTableWrapper')) {
    window.paymentTableManager = new PaymentTableManager();
    
    // Connect action-functions to payment manager
    setTimeout(() => {
        const searchInput = document.getElementById('paymentTable_search');
        const filterSelect = document.getElementById('paymentTable_filter');
        const dateFrom = document.getElementById('paymentTable_dateFrom');
        const dateTo = document.getElementById('paymentTable_dateTo');
        const clearBtn = document.getElementById('paymentTable_clearBtn');
        const exportExcel = document.getElementById('paymentTable_exportExcel');
        const exportPDF = document.getElementById('paymentTable_exportPDF');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => window.paymentTableManager.handleSearch(e.target.value));
        }
        if (filterSelect) {
            filterSelect.addEventListener('change', (e) => window.paymentTableManager.handleFilter('status', e.target.value));
        }
        if (dateFrom) {
            dateFrom.addEventListener('change', (e) => window.paymentTableManager.handleFilter('dateFrom', e.target.value));
        }
        if (dateTo) {
            dateTo.addEventListener('change', (e) => window.paymentTableManager.handleFilter('dateTo', e.target.value));
        }
        if (clearBtn) {
            clearBtn.addEventListener('click', () => window.paymentTableManager.clearFilters());
        }
        if (exportExcel) {
            exportExcel.addEventListener('click', () => window.paymentTableManager.exportToExcel());
        }
        if (exportPDF) {
            exportPDF.addEventListener('click', () => window.paymentTableManager.exportToPDF());
        }
    }, 500);
}
