// Billing Consumer Data - Updated for Phase 1
const consumerBillingData = [
    { 
        id: 1, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">CUST-001</div>', 
        account_no: 'ACC-2024-001', 
        meter_reading: '125 m³',
        consumption: '25',
        bill_amount: '<span class="font-semibold text-gray-900 dark:text-white">₱739.20</span>', 
        billing_period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">Paid</span>', 
        actions: '<a href="/billing/show/B-2024-001" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 2, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">CUST-002</div>', 
        account_no: 'ACC-2024-002', 
        meter_reading: '108 m³',
        consumption: '18',
        bill_amount: '<span class="font-semibold text-gray-900 dark:text-white">₱535.80</span>', 
        billing_period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">Paid</span>', 
        actions: '<a href="/billing/show/B-2024-002" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 3, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Garcia</div><div class="text-xs text-gray-500">CUST-003</div>', 
        account_no: 'ACC-2024-003', 
        meter_reading: '142 m³',
        consumption: '32',
        bill_amount: '<span class="font-semibold text-gray-900 dark:text-white">₱965.60</span>', 
        billing_period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">Overdue</span>', 
        actions: '<a href="/billing/show/B-2024-003" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 4, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Rosa Mendoza</div><div class="text-xs text-gray-500">CUST-004</div>', 
        account_no: 'ACC-2024-004', 
        meter_reading: '95 m³',
        consumption: '15',
        bill_amount: '<span class="font-semibold text-gray-900 dark:text-white">₱445.50</span>', 
        billing_period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">Pending</span>', 
        actions: '<a href="/billing/show/B-2024-004" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 5, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Carlos Lopez</div><div class="text-xs text-gray-500">CUST-005</div>', 
        account_no: 'ACC-2024-005', 
        meter_reading: '178 m³',
        consumption: '48',
        bill_amount: '<span class="font-semibold text-gray-900 dark:text-white">₱1,285.40</span>', 
        billing_period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">Paid</span>', 
        actions: '<a href="/billing/show/B-2024-005" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
];

// Bill Details for Show Page
const billDetailsData = {
    id: 'B-2024-001',
    customer_name: 'Juan Dela Cruz',
    customer_id: 'CUST-001',
    account_no: 'ACC-2024-001',
    billing_period: 'January 2024',
    meter_reading_start: '100 m³',
    meter_reading_end: '125 m³',
    consumption: '25 m³',
    rate_applied: 'Residential Standard (RT-2024-001)',
    rate_period: 'BP-2024-01',
    calculation: {
        tier1: { range: '0-10 m³', rate: '₱10.00', amount: '₱100.00' },
        tier2: { range: '11-20 m³', rate: '₱12.00', amount: '₱60.00' },
        tier3: { range: '21-25 m³', rate: '₱15.00', amount: '₱75.00' },
    },
    subtotal: '₱235.00',
    service_charge: '₱50.00',
    environmental_fee: '₱35.00',
    tax: '₱23.52',
    total_amount: '₱343.52',
    due_date: 'February 8, 2024',
    status: 'Paid',
    payment_date: 'January 15, 2024',
    payment_method: 'Over the Counter',
    reference_no: 'PAY-2024-001'
};

// Generate consumer billing table data
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.consumerBillingTable;
        if (tableInstance) {
            tableInstance.data = consumerBillingData;
        }
    }, 500);
});
