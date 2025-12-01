<!-- Search and Filters -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <x-ui.search-bar id="readingsSearch" placeholder="Search by meter, consumer, or reader..." />
        </div>
        <div class="sm:w-48">
            <select id="meterFilter" onchange="filterReadings()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Meters</option>
            </select>
        </div>
    </div>
</div>

<div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter Serial</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reading</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumption</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reader</th>
            </tr>
        </thead>
        <tbody id="readingsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
        </tbody>
    </table>
</div>

<div class="flex justify-between items-center mt-4 flex-wrap gap-4">
    <div id="readingsPaginationInfo" class="text-sm text-gray-700 dark:text-gray-300">Page 1 of 1</div>
    <div class="flex items-center gap-2">
        <button onclick="prevReadingsPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"><i class="fas fa-chevron-left mr-1"></i>Previous</button>
        <button onclick="nextReadingsPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Next<i class="fas fa-chevron-right ml-1"></i></button>
    </div>
</div>

<script>
const readingsData = [
    // Meter 5003 readings
    { reading_id: 2001, meter_serial: 'MTR-DEF-11223', consumer: 'Gelogo, Norben', reading_date: '2024-02-01', reading_value: 12.500, prev_reading: 0, is_estimated: 0, reader: 'Alex Reader' },
    { reading_id: 2002, meter_serial: 'MTR-DEF-11223', consumer: 'Gelogo, Norben', reading_date: '2024-03-01', reading_value: 25.300, prev_reading: 12.500, is_estimated: 0, reader: 'Alex Reader' },
    { reading_id: 2003, meter_serial: 'MTR-DEF-11223', consumer: 'Gelogo, Norben', reading_date: '2024-04-01', reading_value: 38.750, prev_reading: 25.300, is_estimated: 0, reader: 'Maria Santos' },
    { reading_id: 2004, meter_serial: 'MTR-DEF-11223', consumer: 'Gelogo, Norben', reading_date: '2024-05-01', reading_value: 52.100, prev_reading: 38.750, is_estimated: 0, reader: 'Alex Reader' },
    // Meter 5004 readings
    { reading_id: 2005, meter_serial: 'MTR-GHI-44556', consumer: 'Sayson, Sarah', reading_date: '2024-02-01', reading_value: 8.750, prev_reading: 0, is_estimated: 0, reader: 'Maria Santos' },
    { reading_id: 2006, meter_serial: 'MTR-GHI-44556', consumer: 'Sayson, Sarah', reading_date: '2024-03-01', reading_value: 18.200, prev_reading: 8.750, is_estimated: 0, reader: 'Maria Santos' },
    { reading_id: 2007, meter_serial: 'MTR-GHI-44556', consumer: 'Sayson, Sarah', reading_date: '2024-04-01', reading_value: 27.900, prev_reading: 18.200, is_estimated: 1, reader: 'John Cruz' },
    { reading_id: 2008, meter_serial: 'MTR-GHI-44556', consumer: 'Sayson, Sarah', reading_date: '2024-05-01', reading_value: 37.450, prev_reading: 27.900, is_estimated: 0, reader: 'Maria Santos' },
    // Meter 5005 readings (Faulty)
    { reading_id: 2009, meter_serial: 'MTR-JKL-77889', consumer: 'Apora, Jose', reading_date: '2024-02-01', reading_value: 15.200, prev_reading: 0, is_estimated: 0, reader: 'Alex Reader' },
    { reading_id: 2010, meter_serial: 'MTR-JKL-77889', consumer: 'Apora, Jose', reading_date: '2024-03-01', reading_value: 30.100, prev_reading: 15.200, is_estimated: 0, reader: 'John Cruz' },
    { reading_id: 2011, meter_serial: 'MTR-JKL-77889', consumer: 'Apora, Jose', reading_date: '2024-04-01', reading_value: 45.600, prev_reading: 30.100, is_estimated: 1, reader: 'Alex Reader' },
    { reading_id: 2012, meter_serial: 'MTR-JKL-77889', consumer: 'Apora, Jose', reading_date: '2024-05-01', reading_value: 60.800, prev_reading: 45.600, is_estimated: 1, reader: 'Maria Santos' },
    // Meter 5008 readings
    { reading_id: 2013, meter_serial: 'MTR-STU-22110', consumer: 'Ramos, Angela', reading_date: '2024-02-01', reading_value: 5.300, prev_reading: 0, is_estimated: 0, reader: 'John Cruz' },
    { reading_id: 2014, meter_serial: 'MTR-STU-22110', consumer: 'Ramos, Angela', reading_date: '2024-03-01', reading_value: 11.100, prev_reading: 5.300, is_estimated: 0, reader: 'John Cruz' },
    { reading_id: 2015, meter_serial: 'MTR-STU-22110', consumer: 'Ramos, Angela', reading_date: '2024-04-01', reading_value: 16.750, prev_reading: 11.100, is_estimated: 0, reader: 'Alex Reader' },
    { reading_id: 2016, meter_serial: 'MTR-STU-22110', consumer: 'Ramos, Angela', reading_date: '2024-05-01', reading_value: 22.900, prev_reading: 16.750, is_estimated: 0, reader: 'John Cruz' }
];

let filteredReadings = [...readingsData];

function renderReadings() {
    const tbody = document.getElementById('readingsTable');
    const sorted = filteredReadings.sort((a, b) => new Date(b.reading_date) - new Date(a.reading_date));
    
    tbody.innerHTML = sorted.map(item => {
        const consumption = (item.reading_value - item.prev_reading).toFixed(2);
        const typeClass = item.is_estimated ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200';
        const typeLabel = item.is_estimated ? 'Estimated' : 'Actual';
        
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${new Date(item.reading_date).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">${item.meter_serial}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${item.consumer}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">${item.reading_value.toFixed(2)} m³</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${consumption} m³</td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">${typeLabel}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${item.reader}</td>
            </tr>
        `;
    }).join('');
    
    populateMeterFilter();
}

function populateMeterFilter() {
    const select = document.getElementById('meterFilter');
    const uniqueMeters = [...new Set(readingsData.map(r => r.meter_serial))];
    
    select.innerHTML = '<option value="">All Meters</option>' + 
        uniqueMeters.map(m => `<option value="${m}">${m}</option>`).join('');
}

function filterReadings() {
    const filter = document.getElementById('meterFilter').value;
    filteredReadings = filter ? readingsData.filter(r => r.meter_serial === filter) : [...readingsData];
    renderReadings();
}

let readingsCurrentPage = 1;
const readingsPerPage = 10;

function prevReadingsPage() {
    if (readingsCurrentPage > 1) {
        readingsCurrentPage--;
        renderReadings();
    }
}

function nextReadingsPage() {
    readingsCurrentPage++;
    renderReadings();
}

document.addEventListener('DOMContentLoaded', function() {
    renderReadings();
});

window.filterReadings = filterReadings;
window.prevReadingsPage = prevReadingsPage;
window.nextReadingsPage = nextReadingsPage;
</script>
