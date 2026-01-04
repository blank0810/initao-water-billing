// Bill Generation Data
const billGenerationData = [
    { id: 1, bill_no: 'BILL-2024-001', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>', period: 'Jan 2024', consumption: '25 m³', amount: '<span class="font-semibold text-gray-900 dark:text-white">₱2,450.00</span>', status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Generated</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-eye mr-1"></i>View</button>' },
    { id: 2, bill_no: 'BILL-2024-002', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">ACC-2024-002</div>', period: 'Jan 2024', consumption: '32 m³', amount: '<span class="font-semibold text-gray-900 dark:text-white">₱3,200.00</span>', status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-eye mr-1"></i>View</button>' },
];

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.billGenerationTable;
        if (tableInstance) {
            tableInstance.data = billGenerationData;
        }
    }, 500);
});
