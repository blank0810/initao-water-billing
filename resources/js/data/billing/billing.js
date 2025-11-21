// ============================================
// BILLING MODULE - ENHANCED WITH DATABASE SCHEMA
// ============================================

console.log('Loading enhanced billing module...');

// ============================================
// ENUMERATIONS & CONSTANTS
// ============================================

const STATUSES = {
    ACTIVE: 1,
    PAID: 2,
    CANCELLED: 3,
    OVERDUE: 4,
    ADJUSTED: 5,
    PENDING: 6,
    INACTIVE: 0
};

const TARGET_TYPES = {
    BILL: 'BILL',
    CHARGE: 'CHARGE'
};

const SOURCE_TYPES = {
    BILL: 'BILL',
    ADJUSTMENT: 'ADJUSTMENT',
    PAYMENT: 'PAYMENT',
    CHARGE: 'CHARGE'
};

const ADJUSTMENT_DIRECTIONS = {
    INCREASE: '+',
    DECREASE: '-'
};

// ============================================
// DUMMY DATA - BASED ON DATABASE SCHEMA
// ============================================

const accountTypes = [
    { at_id: 1, at_desc: 'Residential Standard', stat_id: STATUSES.ACTIVE },
    { at_id: 2, at_desc: 'Residential Low-Income', stat_id: STATUSES.ACTIVE },
    { at_id: 3, at_desc: 'Commercial', stat_id: STATUSES.ACTIVE },
    { at_id: 4, at_desc: 'Industrial', stat_id: STATUSES.ACTIVE },
    { at_id: 5, at_desc: 'Government', stat_id: STATUSES.ACTIVE }
];

const billAdjustmentTypes = [
    { bill_adjustment_type_id: 1, name: 'Penalty', direction: ADJUSTMENT_DIRECTIONS.INCREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 2, name: 'Discount', direction: ADJUSTMENT_DIRECTIONS.DECREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 3, name: 'Senior Citizen Discount', direction: ADJUSTMENT_DIRECTIONS.DECREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 4, name: 'Late Fee', direction: ADJUSTMENT_DIRECTIONS.INCREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 5, name: 'Surcharge', direction: ADJUSTMENT_DIRECTIONS.INCREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 6, name: 'Waiver', direction: ADJUSTMENT_DIRECTIONS.DECREASE, stat_id: STATUSES.ACTIVE },
    { bill_adjustment_type_id: 7, name: 'Correction', direction: ADJUSTMENT_DIRECTIONS.DECREASE, stat_id: STATUSES.ACTIVE }
];

const chargeItems = [
    { charge_item_id: 1, name: 'Installation Fee', amount: 500.00, stat_id: STATUSES.ACTIVE },
    { charge_item_id: 2, name: 'Reconnection Fee', amount: 300.00, stat_id: STATUSES.ACTIVE },
    { charge_item_id: 3, name: 'Meter Transfer Fee', amount: 250.00, stat_id: STATUSES.ACTIVE },
    { charge_item_id: 4, name: 'Security Deposit', amount: 1000.00, stat_id: STATUSES.ACTIVE },
    { charge_item_id: 5, name: 'Inspection Fee', amount: 150.00, stat_id: STATUSES.ACTIVE }
];

const waterBillHistory = [
    { bill_id: 801, connection_id: 1001, period_id: 1, consumption: 12.500, water_amount: 225.00, due_date: '2024-02-15', adjustment_total: 0.00, total_amount: 225.00, stat_id: STATUSES.PAID, bill_date: '2024-02-01' },
    { bill_id: 802, connection_id: 1001, period_id: 2, consumption: 12.800, water_amount: 230.40, due_date: '2024-03-15', adjustment_total: 0.00, total_amount: 230.40, stat_id: STATUSES.PAID, bill_date: '2024-03-01' },
    { bill_id: 803, connection_id: 1001, period_id: 3, consumption: 13.450, water_amount: 242.10, due_date: '2024-04-15', adjustment_total: 0.00, total_amount: 242.10, stat_id: STATUSES.PAID, bill_date: '2024-04-01' },
    { bill_id: 804, connection_id: 1001, period_id: 4, consumption: 13.350, water_amount: 240.30, due_date: '2024-05-15', adjustment_total: 0.00, total_amount: 240.30, stat_id: STATUSES.ACTIVE, bill_date: '2024-05-01' },
    { bill_id: 805, connection_id: 1002, period_id: 1, consumption: 8.750, water_amount: 157.50, due_date: '2024-02-15', adjustment_total: 0.00, total_amount: 157.50, stat_id: STATUSES.PAID, bill_date: '2024-02-01' },
    { bill_id: 806, connection_id: 1002, period_id: 2, consumption: 9.450, water_amount: 170.10, due_date: '2024-03-15', adjustment_total: -15.00, total_amount: 155.10, stat_id: STATUSES.PAID, bill_date: '2024-03-01' },
    { bill_id: 807, connection_id: 1002, period_id: 3, consumption: 9.700, water_amount: 174.60, due_date: '2024-04-15', adjustment_total: 0.00, total_amount: 174.60, stat_id: STATUSES.PAID, bill_date: '2024-04-01' },
    { bill_id: 808, connection_id: 1002, period_id: 4, consumption: 9.550, water_amount: 171.90, due_date: '2024-05-15', adjustment_total: 0.00, total_amount: 171.90, stat_id: STATUSES.ACTIVE, bill_date: '2024-05-01' },
    { bill_id: 809, connection_id: 1003, period_id: 1, consumption: 15.200, water_amount: 273.60, due_date: '2024-02-15', adjustment_total: 50.00, total_amount: 323.60, stat_id: STATUSES.OVERDUE, bill_date: '2024-02-01' },
    { bill_id: 810, connection_id: 1003, period_id: 2, consumption: 14.900, water_amount: 268.20, due_date: '2024-03-15', adjustment_total: 50.00, total_amount: 318.20, stat_id: STATUSES.OVERDUE, bill_date: '2024-03-01' },
    { bill_id: 811, connection_id: 1003, period_id: 3, consumption: 15.500, water_amount: 279.00, due_date: '2024-04-15', adjustment_total: 50.00, total_amount: 329.00, stat_id: STATUSES.OVERDUE, bill_date: '2024-04-01' },
    { bill_id: 812, connection_id: 1003, period_id: 4, consumption: 15.200, water_amount: 273.60, due_date: '2024-05-15', adjustment_total: 0.00, total_amount: 273.60, stat_id: STATUSES.ACTIVE, bill_date: '2024-05-01' }
];

const billAdjustments = [
    { bill_adjustment_id: 50, bill_id: 809, bill_adjustment_type_id: 4, amount: 50.00, remarks: 'Late payment penalty', user_id: 1, created_at: '2024-03-01', source_type: SOURCE_TYPES.ADJUSTMENT },
    { bill_adjustment_id: 51, bill_id: 810, bill_adjustment_type_id: 4, amount: 50.00, remarks: 'Late payment penalty', user_id: 1, created_at: '2024-04-01', source_type: SOURCE_TYPES.ADJUSTMENT },
    { bill_adjustment_id: 52, bill_id: 811, bill_adjustment_type_id: 4, amount: 50.00, remarks: 'Late payment penalty', user_id: 1, created_at: '2024-05-01', source_type: SOURCE_TYPES.ADJUSTMENT },
    { bill_adjustment_id: 53, bill_id: 806, bill_adjustment_type_id: 3, amount: -15.00, remarks: 'Senior citizen discount', user_id: 1, created_at: '2024-03-01', source_type: SOURCE_TYPES.ADJUSTMENT }
];

const payments = [
    { payment_id: 901, receipt_no: 'RCPT-0010001', payer_id: 1001, payment_date: '2024-02-10', amount_received: 225.00, user_id: 1, stat_id: STATUSES.ACTIVE },
    { payment_id: 902, receipt_no: 'RCPT-0010002', payer_id: 1001, payment_date: '2024-03-12', amount_received: 230.40, user_id: 1, stat_id: STATUSES.ACTIVE },
    { payment_id: 903, receipt_no: 'RCPT-0010003', payer_id: 1001, payment_date: '2024-04-08', amount_received: 242.10, user_id: 1, stat_id: STATUSES.ACTIVE },
    { payment_id: 904, receipt_no: 'RCPT-0010004', payer_id: 1002, payment_date: '2024-02-14', amount_received: 157.50, user_id: 1, stat_id: STATUSES.ACTIVE },
    { payment_id: 905, receipt_no: 'RCPT-0010005', payer_id: 1002, payment_date: '2024-03-10', amount_received: 155.10, user_id: 1, stat_id: STATUSES.ACTIVE },
    { payment_id: 906, receipt_no: 'RCPT-0010006', payer_id: 1002, payment_date: '2024-04-12', amount_received: 174.60, user_id: 1, stat_id: STATUSES.ACTIVE }
];

const paymentAllocations = [
    { payment_allocation_id: 95, payment_id: 901, target_type: TARGET_TYPES.BILL, target_id: 801, amount_applied: 225.00, period_id: 1, connection_id: 1001 },
    { payment_allocation_id: 96, payment_id: 902, target_type: TARGET_TYPES.BILL, target_id: 802, amount_applied: 230.40, period_id: 2, connection_id: 1001 },
    { payment_allocation_id: 97, payment_id: 903, target_type: TARGET_TYPES.BILL, target_id: 803, amount_applied: 242.10, period_id: 3, connection_id: 1001 },
    { payment_allocation_id: 98, payment_id: 904, target_type: TARGET_TYPES.BILL, target_id: 805, amount_applied: 157.50, period_id: 1, connection_id: 1002 },
    { payment_allocation_id: 99, payment_id: 905, target_type: TARGET_TYPES.BILL, target_id: 806, amount_applied: 155.10, period_id: 2, connection_id: 1002 },
    { payment_allocation_id: 100, payment_id: 906, target_type: TARGET_TYPES.BILL, target_id: 807, amount_applied: 174.60, period_id: 3, connection_id: 1002 }
];

const consumers = [
    { connection_id: 1001, name: 'Gelogo, Norben', account_no: 'ACC-2024-1001', location: 'Brgy. 1, Main St', meter_serial: 'MTR-DEF-11223', at_id: 1 },
    { connection_id: 1002, name: 'Sayson, Sarah', account_no: 'ACC-2024-1002', location: 'Brgy. 2, Oak Ave', meter_serial: 'MTR-GHI-44556', at_id: 1 },
    { connection_id: 1003, name: 'Apora, Jose', account_no: 'ACC-2024-1003', location: 'Brgy. 3, Pine Rd', meter_serial: 'MTR-JKL-77889', at_id: 3 },
    { connection_id: 1004, name: 'Ramos, Angela', account_no: 'ACC-2024-1004', location: 'Brgy. 4, Elm St', meter_serial: 'MTR-STU-22110', at_id: 1 }
];

const recentActivities = [
    { action: 'Payment Received', date: '2024-04-15', connection_id: 1001, source_type: SOURCE_TYPES.PAYMENT },
    { action: 'Bill Generated', date: '2024-05-01', connection_id: 1001, source_type: SOURCE_TYPES.BILL },
    { action: 'Adjustment Applied', date: '2024-03-01', connection_id: 1002, source_type: SOURCE_TYPES.ADJUSTMENT },
    { action: 'Payment Received', date: '2024-04-12', connection_id: 1002, source_type: SOURCE_TYPES.PAYMENT }
];

const billingHistory = [
    { month: 'January 2024', dueRead: '2024-01-15', amount: '₱225.00', status: 'PAID', stat_id: STATUSES.PAID },
    { month: 'February 2024', dueRead: '2024-02-15', amount: '₱230.40', status: 'PAID', stat_id: STATUSES.PAID },
    { month: 'March 2024', dueRead: '2024-03-15', amount: '₱242.10', status: 'PAID', stat_id: STATUSES.PAID },
    { month: 'April 2024', dueRead: '2024-04-15', amount: '₱240.30', status: 'UNPAID', stat_id: STATUSES.ACTIVE }
];

const monthlyTrend = {
    labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
    data: [225, 230.40, 242.10, 240.30, 0, 0, 0, 0, 0, 0, 0, 0]
};

// ============================================
// PAGINATION & SORTING STATE
// ============================================

const paginationState = {
    billHistory: { page: 1, perPage: 10, sortCol: 'bill_date', sortDir: 'desc', data: [] },
    payments: { page: 1, perPage: 10, sortCol: 'payment_date', sortDir: 'desc', data: [] },
    adjustments: { page: 1, perPage: 10, sortCol: 'created_at', sortDir: 'desc', data: [] },
    allocations: { page: 1, perPage: 10, sortCol: 'payment_allocation_id', sortDir: 'desc', data: [] },
    billingDetails: { page: 1, perPage: 10, sortCol: 'date', sortDir: 'desc', data: [] }
};

// ============================================
// EXPORT FUNCTIONS
// ============================================

function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId)?.querySelector('table') || document.querySelector(`#${tableId}`);
    if (!table) return;
    const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
    XLSX.writeFile(wb, `${filename}-${new Date().toISOString().split('T')[0]}.xlsx`);
}

function exportToPDF(tableId, title) {
    const element = document.getElementById(tableId);
    if (!element) return;
    const opt = {
        margin: 10,
        filename: `${title.replace(/\s+/g, '-')}-${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(opt).from(element).save();
}

function printTable(tableId) {
    const element = document.getElementById(tableId);
    if (!element) return;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<style>table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f3f4f6;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

function exportToPNG(tableId, filename) {
    const element = document.getElementById(tableId);
    if (!element) return;
    html2canvas(element).then(canvas => {
        const link = document.createElement('a');
        link.download = `${filename}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL();
        link.click();
    });
}

// ============================================
// PAGINATION FUNCTIONS
// ============================================

function paginate(data, page, perPage) {
    const start = (page - 1) * perPage;
    return data.slice(start, start + perPage);
}

function changePage(tableType, page) {
    const state = paginationState[tableType];
    state.page = page;
    const totalPages = Math.ceil(state.data.length / state.perPage);
    const paginationEl = document.getElementById(`${tableType}Pagination`);
    if (!paginationEl) return;
    let html = '<div class="flex justify-between items-center mt-4 px-4">';
    html += `<div class="text-sm text-gray-700 dark:text-gray-300">Showing ${((page - 1) * state.perPage) + 1} to ${Math.min(page * state.perPage, state.data.length)} of ${state.data.length} entries</div>`;
    html += '<div class="flex gap-2">';
    if (page > 1) {
        html += `<button onclick="billing.changePage('${tableType}', ${page - 1}); render${tableType.charAt(0).toUpperCase() + tableType.slice(1)}();" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Previous</button>`;
    }
    for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
        const activeClass = i === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700';
        html += `<button onclick="billing.changePage('${tableType}', ${i}); render${tableType.charAt(0).toUpperCase() + tableType.slice(1)}();" class="px-3 py-1 ${activeClass} rounded hover:bg-blue-500">${i}</button>`;
    }
    if (page < totalPages) {
        html += `<button onclick="billing.changePage('${tableType}', ${page + 1}); render${tableType.charAt(0).toUpperCase() + tableType.slice(1)}();" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Next</button>`;
    }
    html += '</div></div>';
    paginationEl.innerHTML = html;
}

// ============================================
// SORTING FUNCTIONS
// ============================================

function sortData(tableType, column) {
    const state = paginationState[tableType];
    if (state.sortCol === column) {
        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
    } else {
        state.sortCol = column;
        state.sortDir = 'asc';
    }
    state.data.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        if (state.sortDir === 'asc') {
            return aVal > bVal ? 1 : -1;
        } else {
            return aVal < bVal ? 1 : -1;
        }
    });
}

// ============================================
// CONSUMER BILLING HELPERS
// ============================================

function openPaymentModalForConsumer(connectionId) {
    openAddPaymentModal();
    setTimeout(() => {
        document.getElementById('paymentConsumer').value = connectionId;
        loadConsumerBills();
    }, 100);
}

function getConsumerDetails(connectionId) {
    const consumer = consumers.find(c => c.connection_id === connectionId);
    const bills = waterBillHistory.filter(b => b.connection_id === connectionId);
    const consumerPayments = payments.filter(p => p.payer_id === connectionId);
    const consumerAllocations = paymentAllocations.filter(a => a.connection_id === connectionId);
    const consumerAdjustments = billAdjustments.filter(adj => {
        const bill = waterBillHistory.find(b => b.bill_id === adj.bill_id);
        return bill && bill.connection_id === connectionId;
    });
    const activities = recentActivities.filter(a => a.connection_id === connectionId);
    const unpaidBills = bills.filter(b => b.stat_id === STATUSES.ACTIVE || b.stat_id === STATUSES.OVERDUE);
    const currentBill = bills[bills.length - 1];
    const accountType = accountTypes.find(at => at.at_id === consumer?.at_id);
    
    return {
        consumer,
        bills,
        payments: consumerPayments,
        allocations: consumerAllocations,
        adjustments: consumerAdjustments,
        activities,
        name: consumer?.name || 'N/A',
        id: consumer?.account_no || 'N/A',
        class: accountType?.at_desc || 'Residential Standard',
        meterNo: consumer?.meter_serial || 'N/A',
        status: 'Active',
        email: 'consumer@example.com',
        phone: '+63 912 345 6789',
        location: consumer?.location || 'N/A',
        overdueDays: unpaidBills.length > 0 && unpaidBills[0].stat_id === STATUSES.OVERDUE ? Math.floor((new Date() - new Date(unpaidBills[0].due_date)) / (1000 * 60 * 60 * 24)) : 0,
        currentAmountDue: currentBill?.total_amount || 0,
        billingPeriod: currentBill ? `${currentBill.bill_date} to ${currentBill.due_date}` : 'N/A',
        billNo: currentBill?.bill_id || 'N/A',
        billingStatus: currentBill?.stat_id === STATUSES.PAID ? 'PAID' : currentBill?.stat_id === STATUSES.OVERDUE ? 'OVERDUE' : 'UNPAID',
        issuedDate: currentBill?.bill_date || 'N/A',
        dueDate: currentBill?.due_date || 'N/A',
        totalMonthBills: bills.length,
        unpaidMonthBills: unpaidBills.length,
        totalUnpaidAmount: unpaidBills.reduce((sum, b) => sum + b.total_amount, 0),
        overallBillingStatus: unpaidBills.length === 0 ? 'PAID' : unpaidBills.some(b => b.stat_id === STATUSES.OVERDUE) ? 'OVERDUE' : 'UNPAID',
        consumption: currentBill ? `${currentBill.consumption.toFixed(3)} m³` : '0.000 m³',
        currentUsage: currentBill ? `${currentBill.consumption.toFixed(3)} m³` : '0.000 m³',
        meterReading: currentBill ? currentBill.consumption.toFixed(3) : '0.000',
        dateRead: currentBill?.bill_date || 'N/A'
    };
}

// ============================================
// TOAST NOTIFICATION SYSTEM
// ============================================

function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-slide-in`;
    toast.innerHTML = `
        <i class="fas ${icons[type]} text-xl"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 hover:opacity-75">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// ============================================
// UPDATE SUMMARY CARDS
// ============================================

function updateSummaryCards() {
    const unpaidBills = waterBillHistory.filter(b => b.stat_id === STATUSES.ACTIVE || b.stat_id === STATUSES.OVERDUE);
    const paidBills = waterBillHistory.filter(b => b.stat_id === STATUSES.PAID);
    const overdueBills = waterBillHistory.filter(b => b.stat_id === STATUSES.OVERDUE);
    
    const outstanding = unpaidBills.reduce((sum, b) => sum + b.total_amount, 0);
    const totalPaid = payments.reduce((sum, p) => sum + p.amount_received, 0);
    const totalAdjustments = billAdjustments.reduce((sum, a) => sum + Math.abs(a.amount), 0);
    const activeConsumers = consumers.length;
    
    document.getElementById('card-outstanding').textContent = '₱' + outstanding.toFixed(2);
    document.getElementById('card-total-paid').textContent = '₱' + totalPaid.toFixed(2);
    document.getElementById('card-overdue-bills').textContent = overdueBills.length;
    document.getElementById('card-total-adjustments').textContent = '₱' + totalAdjustments.toFixed(2);
    document.getElementById('card-active-consumers').textContent = activeConsumers;
}

// ============================================
// GLOBAL BILLING OBJECT
// ============================================

window.billing = {
    exportToExcel,
    exportToPDF,
    exportToPNG,
    printTable,
    paginate,
    changePage,
    sortData,
    paginationState,
    getConsumerDetails,
    showToast,
    updateSummaryCards,
    openPaymentModalForConsumer,
    STATUSES,
    TARGET_TYPES,
    SOURCE_TYPES,
    ADJUSTMENT_DIRECTIONS
};

window.showToast = showToast;

window.billingData = {
    accountTypes,
    billAdjustmentTypes,
    chargeItems,
    waterBillHistory,
    billAdjustments,
    payments,
    paymentAllocations,
    consumers,
    recentActivities,
    billingHistory,
    monthlyTrend
};

console.log('Billing module loaded successfully');
