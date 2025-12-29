// Consumer Billing Data
const consumerBillingData = [
    { 
        id: 1, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">CUST-001</div>', 
        account_no: 'ACC-2024-001', 
        reading: '125 m³',
        consumption: '25',
        amount: '<span class="font-semibold text-gray-900 dark:text-white">₱739.20</span>', 
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">Active</span>', 
        actions: '<a href="/billing/customer/1" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 2, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">CUST-002</div>', 
        account_no: 'ACC-2024-002', 
        reading: '108 m³',
        consumption: '18',
        amount: '<span class="font-semibold text-gray-900 dark:text-white">₱535.80</span>', 
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">Active</span>', 
        actions: '<a href="/billing/customer/2" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
    { 
        id: 3, 
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Garcia</div><div class="text-xs text-gray-500">CUST-003</div>', 
        account_no: 'ACC-2024-003', 
        reading: '142 m³',
        consumption: '32',
        amount: '<span class="font-semibold text-gray-900 dark:text-white">₱965.60</span>', 
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">Overdue</span>', 
        actions: '<a href="/billing/customer/3" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>' 
    },
];

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.consumerBillingTable;
        if (tableInstance) {
            tableInstance.data = consumerBillingData;
        }
    }, 500);
});
