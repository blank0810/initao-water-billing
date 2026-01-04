// Adjustments Data
const adjustmentsData = [
    { id: 1, adjustment_no: 'ADJ-2024-001', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Garcia</div><div class="text-xs text-gray-500">ACC-2024-003</div>', type: '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Credit</span>', amount: '<span class="font-semibold text-green-600">-₱500.00</span>', reason: 'Meter reading error', status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-eye mr-1"></i>View</button>' },
    { id: 2, adjustment_no: 'ADJ-2024-002', consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Ana Lopez</div><div class="text-xs text-gray-500">ACC-2024-004</div>', type: '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Debit</span>', amount: '<span class="font-semibold text-red-600">+₱300.00</span>', reason: 'Late payment penalty', status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition"><i class="fas fa-eye mr-1"></i>View</button>' },
];

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.adjustmentsTable;
        if (tableInstance) {
            tableInstance.data = adjustmentsData;
        }
    }, 500);
});
