// Global Action Functions Handler
class ActionFunctionsManager {
    constructor(tableId) {
        this.tableId = tableId;
        this.searchInput = document.getElementById(`${tableId}_search`);
        this.filterSelect = document.getElementById(`${tableId}_filter`);
        this.dateFromInput = document.getElementById(`${tableId}_dateFrom`);
        this.dateToInput = document.getElementById(`${tableId}_dateTo`);
        this.clearBtn = document.getElementById(`${tableId}_clearBtn`);
        this.exportExcelBtn = document.getElementById(`${tableId}_exportExcel`);
        this.exportPDFBtn = document.getElementById(`${tableId}_exportPDF`);
        this.tableBody = document.getElementById(tableId);
        
        this.init();
    }

    init() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        }
        if (this.filterSelect) {
            this.filterSelect.addEventListener('change', (e) => this.handleFilter(e.target.value));
        }
        if (this.dateFromInput) {
            this.dateFromInput.addEventListener('change', () => this.handleDateFilter());
        }
        if (this.dateToInput) {
            this.dateToInput.addEventListener('change', () => this.handleDateFilter());
        }
        if (this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clearFilters());
        }
        if (this.exportExcelBtn) {
            this.exportExcelBtn.addEventListener('click', () => this.exportToExcel());
        }
        if (this.exportPDFBtn) {
            this.exportPDFBtn.addEventListener('click', () => this.exportToPDF());
        }
    }

    handleSearch(query) {
        // Check if it's a table component instance
        const tableInstance = window.tableInstances?.[this.tableId];
        if (tableInstance) {
            tableInstance.searchQuery = query;
            return;
        }
        
        if (window.paymentTableManager) {
            window.paymentTableManager.handleSearch(query);
            return;
        }
        
        const rows = this.tableBody?.querySelectorAll('tr');
        if (!rows) return;

        const searchTerm = query.toLowerCase().trim();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    handleFilter(value) {
        if (window.paymentTableManager) {
            window.paymentTableManager.handleFilter('status', value);
            return;
        }
        
        const rows = this.tableBody?.querySelectorAll('tr');
        if (!rows) return;

        rows.forEach(row => {
            if (!value) {
                row.style.display = '';
            } else {
                const text = row.textContent;
                row.style.display = text.includes(value) ? '' : 'none';
            }
        });
    }

    handleDateFilter() {
        if (window.paymentTableManager) {
            const dateFrom = this.dateFromInput?.value;
            const dateTo = this.dateToInput?.value;
            if (dateFrom) window.paymentTableManager.handleFilter('dateFrom', dateFrom);
            if (dateTo) window.paymentTableManager.handleFilter('dateTo', dateTo);
            return;
        }
        
        const dateFrom = this.dateFromInput?.value;
        const dateTo = this.dateToInput?.value;
        const rows = this.tableBody?.querySelectorAll('tr');
        
        if (!rows || (!dateFrom && !dateTo)) return;

        rows.forEach(row => {
            const dateCell = row.querySelector('[data-date]');
            if (!dateCell) return;

            const rowDate = new Date(dateCell.getAttribute('data-date'));
            let show = true;

            if (dateFrom && rowDate < new Date(dateFrom)) show = false;
            if (dateTo && rowDate > new Date(dateTo)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    clearFilters() {
        if (window.paymentTableManager) {
            window.paymentTableManager.clearFilters();
            return;
        }
        
        if (this.searchInput) this.searchInput.value = '';
        if (this.filterSelect) this.filterSelect.value = '';
        if (this.dateFromInput) this.dateFromInput.value = '';
        if (this.dateToInput) this.dateToInput.value = '';

        const rows = this.tableBody?.querySelectorAll('tr');
        rows?.forEach(row => row.style.display = '');
    }

    exportToExcel() {
        if (window.paymentTableManager) {
            window.paymentTableManager.exportToExcel();
            return;
        }
        
        const table = this.tableBody?.closest('table');
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const csvRow = [];
            cols.forEach(col => {
                let text = col.textContent.trim().replace(/\s+/g, ' ');
                csvRow.push(`"${text}"`);
            });
            csv.push(csvRow.join(','));
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${this.tableId}_${new Date().getTime()}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    exportToPDF() {
        if (window.paymentTableManager) {
            window.paymentTableManager.exportToPDF();
            return;
        }
        
        window.print();
    }
}

// Billing-specific modal functions
window.openAdjustmentModal = function() {
    const modal = document.getElementById('addAdjustmentModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
};

window.closeAdjustmentModal = function() {
    const modal = document.getElementById('addAdjustmentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

window.redownloadFile = function(btn) {
    console.log('Re-downloading file...');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Downloading...';
    btn.disabled = true;
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-redo mr-1"></i>Re-download';
        btn.disabled = false;
    }, 1500);
};

window.openBillDetailsModal = function() {
    const modal = document.getElementById('bill-details-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const tables = ['applicationTable', 'approvalTable', 'invoiceTable', 'declinedTable', 'paymentTable', 'connectionsTable', 'areaTable', 'consumer-documents-tbody', 'consumerBillingTable', 'collectionsTable', 'billGenerationTable', 'adjustmentsTable', 'downloadHistoryTable'];
    
    tables.forEach(tableId => {
        if (document.getElementById(tableId)) {
            new ActionFunctionsManager(tableId);
        }
    });
});
