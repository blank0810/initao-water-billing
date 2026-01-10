// Meter Reading Data - Phase 1 Extended
const meterReadingData = [
    {
        id: 'MTR-2024-001',
        meter_id: 'MTR-2024-001',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>',
        meter_serial: '<span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">M-2024-00001</span>',
        previous_reading: '<span class="font-semibold">2,750 m³</span>',
        current_reading: '<span class="font-semibold text-blue-600 dark:text-blue-400">2,775 m³</span>',
        consumption: '<span class="font-bold text-lg text-green-600 dark:text-green-400">25 m³</span>',
        reading_date: '2024-01-30',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Verified</span>',
        actions: '<a href="/meter/show/MTR-2024-001" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'MTR-2024-002',
        meter_id: 'MTR-2024-002',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">ACC-2024-002</div>',
        meter_serial: '<span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">M-2024-00002</span>',
        previous_reading: '<span class="font-semibold">1,520 m³</span>',
        current_reading: '<span class="font-semibold text-blue-600 dark:text-blue-400">1,542 m³</span>',
        consumption: '<span class="font-bold text-lg text-green-600 dark:text-green-400">22 m³</span>',
        reading_date: '2024-01-30',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Verified</span>',
        actions: '<a href="/meter/show/MTR-2024-002" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'MTR-2024-003',
        meter_id: 'MTR-2024-003',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Angel Construction Inc.</div><div class="text-xs text-gray-500">ACC-2024-003</div>',
        meter_serial: '<span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">M-2024-00003</span>',
        previous_reading: '<span class="font-semibold">5,100 m³</span>',
        current_reading: '<span class="font-semibold text-blue-600 dark:text-blue-400">5,185 m³</span>',
        consumption: '<span class="font-bold text-lg text-orange-600 dark:text-orange-400">85 m³</span>',
        reading_date: '2024-01-30',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Verified</span>',
        actions: '<a href="/meter/show/MTR-2024-003" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'MTR-2024-004',
        meter_id: 'MTR-2024-004',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Reyes</div><div class="text-xs text-gray-500">ACC-2024-004</div>',
        meter_serial: '<span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">M-2024-00004</span>',
        previous_reading: '<span class="font-semibold">3,250 m³</span>',
        current_reading: '<span class="font-semibold text-blue-600 dark:text-blue-400">3,318 m³</span>',
        consumption: '<span class="font-bold text-lg text-green-600 dark:text-green-400">68 m³</span>',
        reading_date: '2024-01-30',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
        actions: '<a href="/meter/show/MTR-2024-004" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'MTR-2024-005',
        meter_id: 'MTR-2024-005',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">City Hospital</div><div class="text-xs text-gray-500">ACC-2024-005</div>',
        meter_serial: '<span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">M-2024-00005</span>',
        previous_reading: '<span class="font-semibold">8,900 m³</span>',
        current_reading: '<span class="font-semibold text-blue-600 dark:text-blue-400">9,150 m³</span>',
        consumption: '<span class="font-bold text-lg text-red-600 dark:text-red-400">250 m³</span>',
        reading_date: '2024-01-30',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Verified</span>',
        actions: '<a href="/meter/show/MTR-2024-005" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
];

// Meter Reading Details (Show Page)
const meterReadingDetailsData = {
    id: 'MTR-2024-001',
    meter_id: 'MTR-2024-001',
    meter_info: {
        serial_number: 'M-2024-00001',
        meter_type: 'Digital Multi-Jet',
        installation_date: '2020-06-15',
        last_calibration: '2023-12-01',
        location: 'Front Gate / Property Entrance'
    },
    consumer: {
        id: 1,
        name: 'Juan Dela Cruz',
        account_no: 'ACC-2024-001',
        category: 'Residential Standard',
        address: '123 Main Street, Barangay San Jose, City'
    },
    current_reading: {
        reading_date: '2024-01-30',
        previous_reading: '2,750 m³',
        current_reading: '2,775 m³',
        consumption: '25 m³',
        reading_status: 'Verified',
        verifier_name: 'Mr. Reginald Santos',
        reading_method: 'Manual On-Site Reading'
    },
    reading_history: [
        { period: 'January 2024', reading: '2,775 m³', consumption: '25 m³', date: '2024-01-30', status: 'Verified' },
        { period: 'December 2023', reading: '2,750 m³', consumption: '28 m³', date: '2023-12-29', status: 'Verified' },
        { period: 'November 2023', reading: '2,722 m³', consumption: '22 m³', date: '2023-11-30', status: 'Verified' },
        { period: 'October 2023', reading: '2,700 m³', consumption: '30 m³', date: '2023-10-31', status: 'Verified' },
    ]
};

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Meter reading data loaded');
});
