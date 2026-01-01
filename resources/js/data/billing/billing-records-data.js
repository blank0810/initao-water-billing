// Billing Records Data - Historical Bills per Period
// This represents the official billing history for audit reference

const BILLING_RECORDS_DATA = [
    {
        bill_no: 'BILL-2024-001',
        customer: `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-300">JD</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">ACC-2024-001</div>
                </div>
            </div>
        `,
        period: 'January 2024',
        consumption: '25',
        amount: `<span class="font-semibold text-gray-900 dark:text-white">₱739.20</span>`,
        status: `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            <i class="fas fa-check-circle mr-1"></i>Posted
        </span>`,
        actions: `
            <div class="flex items-center gap-2">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="View Bill">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-green-600 hover:text-green-900 dark:text-green-400 p-2 rounded" title="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
        `
    },
    {
        bill_no: 'BILL-2024-002',
        customer: `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-300">MS</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">Maria Santos</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">ACC-2024-002</div>
                </div>
            </div>
        `,
        period: 'January 2024',
        consumption: '18',
        amount: `<span class="font-semibold text-gray-900 dark:text-white">₱535.80</span>`,
        status: `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            <i class="fas fa-check-circle mr-1"></i>Posted
        </span>`,
        actions: `
            <div class="flex items-center gap-2">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="View Bill">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-green-600 hover:text-green-900 dark:text-green-400 p-2 rounded" title="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
        `
    },
    {
        bill_no: 'BILL-2024-003',
        customer: `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <span class="text-sm font-semibold text-green-600 dark:text-green-300">PG</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">Pedro Garcia</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">ACC-2024-003</div>
                </div>
            </div>
        `,
        period: 'February 2024',
        consumption: '32',
        amount: `<span class="font-semibold text-gray-900 dark:text-white">₱965.60</span>`,
        status: `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
            <i class="fas fa-bolt mr-1"></i>Generated
        </span>`,
        actions: `
            <div class="flex items-center gap-2">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="View Bill">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-purple-600 hover:text-purple-900 dark:text-purple-400 p-2 rounded" title="Post to Ledger">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        `
    },
    {
        bill_no: 'BILL-2024-004',
        customer: `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                    <span class="text-sm font-semibold text-orange-600 dark:text-orange-300">AR</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">Ana Reyes</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">ACC-2024-004</div>
                </div>
            </div>
        `,
        period: 'February 2024',
        consumption: '15',
        amount: `<span class="font-semibold text-gray-900 dark:text-white">₱468.00</span>`,
        status: `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
            <i class="fas fa-bolt mr-1"></i>Generated
        </span>`,
        actions: `
            <div class="flex items-center gap-2">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="View Bill">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-purple-600 hover:text-purple-900 dark:text-purple-400 p-2 rounded" title="Post to Ledger">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        `
    },
    {
        bill_no: 'BILL-2024-005',
        customer: `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <span class="text-sm font-semibold text-red-600 dark:text-red-300">LC</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">Luis Cruz</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">ACC-2024-005</div>
                </div>
            </div>
        `,
        period: 'January 2024',
        consumption: '42',
        amount: `<span class="font-semibold text-gray-900 dark:text-white">₱1,285.40</span>`,
        status: `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            <i class="fas fa-check-circle mr-1"></i>Posted
        </span>`,
        actions: `
            <div class="flex items-center gap-2">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="View Bill">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-green-600 hover:text-green-900 dark:text-green-400 p-2 rounded" title="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
        `
    }
];

// Load data into table instance
setTimeout(() => {
    if (window.tableInstances && window.tableInstances['billingRecordsTable']) {
        window.tableInstances['billingRecordsTable'].data = BILLING_RECORDS_DATA;
    }
}, 300);

export default BILLING_RECORDS_DATA;
