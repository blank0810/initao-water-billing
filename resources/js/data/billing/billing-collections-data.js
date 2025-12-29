// Collections Data
const collectionsData = [
    { id: 1, receipt_no: 'RCP-2024-001', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>', payment_date: '2024-01-15', method: '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Cash</span>', amount: '<span class="font-semibold text-green-600">₱2,450.00</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-print mr-1"></i>Print</button>' },
    { id: 2, receipt_no: 'RCP-2024-002', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Ana Lopez</div><div class="text-xs text-gray-500">ACC-2024-004</div>', payment_date: '2024-01-14', method: '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">GCash</span>', amount: '<span class="font-semibold text-green-600">₱1,800.00</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-print mr-1"></i>Print</button>' },
];

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.collectionsTable;
        if (tableInstance) {
            tableInstance.data = collectionsData;
        }
    }, 500);
});
