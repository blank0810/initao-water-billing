// Enhanced Meter Management System
(function(){
    // Meter Status Types
    const meterStatuses = [
        { id: 1, code: 'ACTIVE', name: 'Active', color: 'green', icon: 'fa-check-circle' },
        { id: 2, code: 'MAINTENANCE', name: 'Maintenance', color: 'yellow', icon: 'fa-wrench' },
        { id: 3, code: 'FAULTY', name: 'Faulty', color: 'red', icon: 'fa-exclamation-triangle' },
        { id: 4, code: 'INACTIVE', name: 'Inactive', color: 'gray', icon: 'fa-times-circle' }
    ];

    // Meter Brands
    const meterBrands = [
        { id: 1, name: 'AquaMeter', model: 'AM-2000' },
        { id: 2, name: 'FlowTech', model: 'FT-Pro' },
        { id: 3, name: 'WaterPro', model: 'WP-500' },
        { id: 4, name: 'HydroSense', model: 'HS-X1' }
    ];

    // Meter Readers
    const meterReaders = [
        { id: 1, name: 'Alex Reader', zone: 'Zone A', active: true },
        { id: 2, name: 'Maria Santos', zone: 'Zone B', active: true },
        { id: 3, name: 'John Cruz', zone: 'Zone C', active: true },
        { id: 4, name: 'Ana Lopez', zone: 'Zone D', active: true }
    ];

    // Consumer Meters with complete data
    const consumers = [
        {
            id: 'C-1001', name: 'Gelogo, Norben', address: 'Brgy. San Roque, Main St',
            meterNo: 'MTR-001', serialNo: 'SN-2024-001', brandId: 1, statusId: 1,
            installDate: '2023-06-15', lastMaintenance: '2024-01-10',
            currentReading: 1250.50, previousReading: 1220.30,
            zone: 'Zone A', accountType: 'Residential'
        },
        {
            id: 'C-1002', name: 'Sayson, Sarah', address: 'Brgy. Poblacion, Oak Ave',
            meterNo: 'MTR-002', serialNo: 'SN-2024-002', brandId: 2, statusId: 1,
            installDate: '2023-07-20', lastMaintenance: '2024-01-15',
            currentReading: 980.75, previousReading: 960.50,
            zone: 'Zone B', accountType: 'Residential'
        },
        {
            id: 'C-1003', name: 'Apora, Jose', address: 'Brgy. Riverside, Pine Rd',
            meterNo: 'MTR-003', serialNo: 'SN-2024-003', brandId: 3, statusId: 2,
            installDate: '2023-05-10', lastMaintenance: '2024-01-20',
            currentReading: 1420.00, previousReading: 1390.25,
            zone: 'Zone C', accountType: 'Residential'
        },
        {
            id: 'C-1004', name: 'Cruz, Maria', address: 'Brgy. Centro, Market St',
            meterNo: 'MTR-004', serialNo: 'SN-2024-004', brandId: 1, statusId: 1,
            installDate: '2023-08-05', lastMaintenance: '2024-01-12',
            currentReading: 2150.25, previousReading: 2100.00,
            zone: 'Zone A', accountType: 'Commercial'
        },
        {
            id: 'C-1005', name: 'Santos Manufacturing', address: 'Industrial Zone A',
            meterNo: 'MTR-005', serialNo: 'SN-2024-005', brandId: 4, statusId: 1,
            installDate: '2023-04-01', lastMaintenance: '2024-01-05',
            currentReading: 5680.50, previousReading: 5500.00,
            zone: 'Zone D', accountType: 'Industrial'
        },
        {
            id: 'C-1006', name: 'Reyes, Pedro', address: 'Brgy. Maharlika, Sunset Blvd',
            meterNo: 'MTR-006', serialNo: 'SN-2024-006', brandId: 2, statusId: 3,
            installDate: '2023-09-15', lastMaintenance: '2023-12-20',
            currentReading: 856.30, previousReading: 820.00,
            zone: 'Zone B', accountType: 'Residential'
        }
    ];

    // Generate reading history for each consumer
    consumers.forEach((c, idx) => {
        c.readingHistory = [];
        for(let m = 6; m >= 1; m--) {
            const month = `2024-${String(7-m).padStart(2,'0')}-01`;
            const reading = c.previousReading + ((c.currentReading - c.previousReading) / 6) * (7-m);
            const consumption = m === 6 ? 0 : reading - c.readingHistory[c.readingHistory.length-1].reading;
            const readerId = (idx % meterReaders.length) + 1;
            const reader = meterReaders.find(r => r.id === readerId);
            
            c.readingHistory.push({
                date: month,
                reading: +reading.toFixed(2),
                consumption: +consumption.toFixed(2),
                readerId: readerId,
                readerName: reader.name,
                isEstimated: Math.random() > 0.9
            });
        }
    });

    // State
    let filteredConsumers = [...consumers];
    let currentPage = 1;
    const rowsPerPage = 10;
    let selectedStatus = 'all';
    let selectedZone = 'all';

    // DOM refs
    let mainTbody, searchEl, paginationInfo, tableSection, detailsSection;

    function resolveDOM() {
        mainTbody = document.getElementById('meterTable');
        searchEl = document.getElementById('searchInput');
        paginationInfo = document.getElementById('paginationInfo');
        tableSection = document.getElementById('tableSection');
        detailsSection = document.getElementById('meterDetailsSection');
    }

    function computeMainAggregates() {
        const totalMeters = filteredConsumers.length;
        const activeMeters = filteredConsumers.filter(c => c.statusId === 1).length;
        const maintenanceMeters = filteredConsumers.filter(c => c.statusId === 2).length;
        const faultyMeters = filteredConsumers.filter(c => c.statusId === 3).length;
        const totalConsumption = filteredConsumers.reduce((sum, c) => sum + (c.currentReading - c.previousReading), 0);
        const avgConsumption = totalMeters > 0 ? totalConsumption / totalMeters : 0;
        
        return { totalMeters, activeMeters, maintenanceMeters, faultyMeters, totalConsumption, avgConsumption };
    }

    function populateMainCards() {
        const cards = computeMainAggregates();
        document.getElementById('cardTotalMeters').textContent = cards.totalMeters;
        document.getElementById('cardActiveMeters').textContent = cards.activeMeters;
        document.getElementById('cardMaintenanceMeters').textContent = cards.maintenanceMeters;
        document.getElementById('cardFaultyMeters').textContent = cards.faultyMeters;
        document.getElementById('cardTotalConsumption').textContent = cards.totalConsumption.toFixed(2) + ' m³';
        document.getElementById('cardAvgConsumption').textContent = cards.avgConsumption.toFixed(2) + ' m³';
    }

    function getStatusInfo(statusId) {
        return meterStatuses.find(s => s.id === statusId) || meterStatuses[0];
    }

    function getBrandInfo(brandId) {
        return meterBrands.find(b => b.id === brandId) || meterBrands[0];
    }

    function renderConsumerMainTable() {
        if(!mainTbody) return;
        mainTbody.innerHTML = '';

        const start = (currentPage - 1) * rowsPerPage;
        const pageItems = filteredConsumers.slice(start, start + rowsPerPage);

        if(pageItems.length === 0) {
            mainTbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No meters found</td></tr>';
        } else {
            pageItems.forEach(c => {
                const status = getStatusInfo(c.statusId);
                const brand = getBrandInfo(c.brandId);
                const consumption = c.currentReading - c.previousReading;
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">${c.meterNo}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${c.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${c.address}</td>
                    <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded bg-${status.color}-100 text-${status.color}-800 dark:bg-${status.color}-900 dark:text-${status.color}-200"><i class="fas ${status.icon} mr-1"></i>${status.name}</span></td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${c.currentReading.toFixed(2)} m³</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${consumption.toFixed(2)} m³</td>
                    <td class="px-6 py-4 text-sm"><button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs" onclick="selectConsumer('${c.id}')">View Details</button></td>
                `;
                mainTbody.appendChild(tr);
            });
        }

        const totalPages = Math.max(1, Math.ceil(filteredConsumers.length / rowsPerPage));
        if(paginationInfo) paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    }

    function prevConsumerPage() { if(currentPage > 1) { currentPage--; renderConsumerMainTable(); } }
    function nextConsumerPage() { if(currentPage * rowsPerPage < filteredConsumers.length) { currentPage++; renderConsumerMainTable(); } }

    function filterByStatus(statusId) {
        selectedStatus = statusId;
        applyFilters();
    }

    function filterByZone(zone) {
        selectedZone = zone;
        applyFilters();
    }

    function applyFilters() {
        filteredConsumers = [...consumers];
        
        if(selectedStatus !== 'all') {
            filteredConsumers = filteredConsumers.filter(c => c.statusId === parseInt(selectedStatus));
        }
        
        if(selectedZone !== 'all') {
            filteredConsumers = filteredConsumers.filter(c => c.zone === selectedZone);
        }
        
        currentPage = 1;
        populateMainCards();
        renderConsumerMainTable();
    }

    function selectConsumer(consumerId) {
        resolveDOM();
        const consumer = consumers.find(c => c.id === consumerId);
        if(!consumer) return;

        const wrapper = document.getElementById('meterSummaryWrapper');
        const searchFilter = document.getElementById('searchFilterSection');
        if(wrapper) wrapper.classList.add('hidden');
        if(searchFilter) searchFilter.classList.add('hidden');
        if(tableSection) tableSection.classList.add('hidden');
        if(detailsSection) detailsSection.classList.remove('hidden');

        const status = getStatusInfo(consumer.statusId);
        const brand = getBrandInfo(consumer.brandId);
        const consumption = consumer.currentReading - consumer.previousReading;

        // Populate profile
        document.getElementById('meter_consumer_name').textContent = consumer.name;
        document.getElementById('meter_consumer_id').textContent = consumer.id;
        document.getElementById('meter_consumer_address').textContent = consumer.address;
        document.getElementById('meter_no').textContent = consumer.meterNo;
        document.getElementById('meter_serial').textContent = consumer.serialNo;
        document.getElementById('meter_brand').textContent = brand.name + ' ' + brand.model;
        document.getElementById('meter_status').textContent = status.name;
        document.getElementById('meter_install_date').textContent = consumer.installDate;
        document.getElementById('meter_zone').textContent = consumer.zone;
        document.getElementById('meter_account_type').textContent = consumer.accountType;

        // Populate readings
        document.getElementById('meter_current_reading').textContent = consumer.currentReading.toFixed(2) + ' m³';
        document.getElementById('meter_previous_reading').textContent = consumer.previousReading.toFixed(2) + ' m³';
        document.getElementById('meter_consumption').textContent = consumption.toFixed(2) + ' m³';
        document.getElementById('meter_last_maintenance').textContent = consumer.lastMaintenance;

        // Populate reading history
        renderReadingHistory(consumer.readingHistory);
        renderConsumptionChart(consumer.readingHistory);
    }

    function renderReadingHistory(history) {
        const t = document.getElementById('readingHistoryTable');
        if(!t) return;
        t.innerHTML = '';
        
        if(!history || history.length === 0) {
            t.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No reading history</td></tr>';
            return;
        }
        
        history.forEach(h => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${h.date}</td>
                <td class="px-4 py-3 text-sm font-semibold text-blue-600">${h.reading.toFixed(2)} m³</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${h.consumption.toFixed(2)} m³</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${h.readerName}</td>
                <td class="px-4 py-3 text-sm">${h.isEstimated ? '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Estimated</span>' : '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Actual</span>'}</td>
            `;
            t.appendChild(tr);
        });
    }

    function renderConsumptionChart(history) {
        const container = document.getElementById('consumptionChart');
        if(!container) return;
        container.innerHTML = '';
        
        if(!history || history.length === 0) {
            container.innerHTML = '<div class="text-sm text-gray-500">No chart data</div>';
            return;
        }

        const width = container.clientWidth || 800;
        const height = 220;
        const padding = 40;
        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);

        const consumptions = history.map(h => h.consumption);
        const maxVal = Math.max(...consumptions, 1);
        const xStep = (width - padding * 2) / Math.max(1, history.length - 1);

        // Axes
        const axisY = document.createElementNS(svgNS, 'line');
        axisY.setAttribute('x1', padding);
        axisY.setAttribute('y1', padding);
        axisY.setAttribute('x2', padding);
        axisY.setAttribute('y2', height - padding);
        axisY.setAttribute('stroke', '#cbd5e1');
        svg.appendChild(axisY);

        const axisX = document.createElementNS(svgNS, 'line');
        axisX.setAttribute('x1', padding);
        axisX.setAttribute('y1', height - padding);
        axisX.setAttribute('x2', width - padding);
        axisX.setAttribute('y2', height - padding);
        axisX.setAttribute('stroke', '#cbd5e1');
        svg.appendChild(axisX);

        // Bars
        consumptions.forEach((v, i) => {
            const x = padding + i * xStep - 10;
            const barHeight = (v / maxVal) * (height - padding * 2);
            const y = (height - padding) - barHeight;
            
            const rect = document.createElementNS(svgNS, 'rect');
            rect.setAttribute('x', x);
            rect.setAttribute('y', y);
            rect.setAttribute('width', '20');
            rect.setAttribute('height', barHeight);
            rect.setAttribute('fill', '#3b82f6');
            svg.appendChild(rect);
        });

        container.appendChild(svg);
    }

    function setupSearch() {
        if(!searchEl) return;
        searchEl.addEventListener('input', e => {
            const q = (e.target.value || '').toLowerCase().trim();
            filteredConsumers = consumers.filter(c => (
                c.name.toLowerCase().includes(q) || 
                c.id.toLowerCase().includes(q) || 
                c.meterNo.toLowerCase().includes(q) ||
                c.address.toLowerCase().includes(q)
            ));
            
            if(selectedStatus !== 'all') {
                filteredConsumers = filteredConsumers.filter(c => c.statusId === parseInt(selectedStatus));
            }
            if(selectedZone !== 'all') {
                filteredConsumers = filteredConsumers.filter(c => c.zone === selectedZone);
            }
            
            currentPage = 1;
            populateMainCards();
            renderConsumerMainTable();
        });
    }

    function populateFilters() {
        const statusDropdown = document.getElementById('statusFilterDropdown');
        if(statusDropdown) {
            statusDropdown.innerHTML = '<option value="all">All Status</option>';
            meterStatuses.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                statusDropdown.appendChild(opt);
            });
        }

        const zoneDropdown = document.getElementById('zoneFilterDropdown');
        if(zoneDropdown) {
            const zones = [...new Set(consumers.map(c => c.zone))];
            zoneDropdown.innerHTML = '<option value="all">All Zones</option>';
            zones.forEach(z => {
                const opt = document.createElement('option');
                opt.value = z;
                opt.textContent = z;
                zoneDropdown.appendChild(opt);
            });
        }
    }

    function init() {
        resolveDOM();
        populateMainCards();
        setupSearch();
        renderConsumerMainTable();
        populateFilters();
    }

    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose
    window.renderConsumerMainTable = renderConsumerMainTable;
    window.prevConsumerPage = prevConsumerPage;
    window.nextConsumerPage = nextConsumerPage;
    window.selectConsumer = selectConsumer;
    window.filterByStatus = filterByStatus;
    window.filterByZone = filterByZone;
    window.showMeterTable = function() {
        if(detailsSection) detailsSection.classList.add('hidden');
        if(tableSection) tableSection.classList.remove('hidden');
        const wrapper = document.getElementById('meterSummaryWrapper');
        const searchFilter = document.getElementById('searchFilterSection');
        if(wrapper) wrapper.classList.remove('hidden');
        if(searchFilter) searchFilter.classList.remove('hidden');
    };
    window._meterModule = { meterStatuses, meterBrands, meterReaders, consumers };
})();

console.log('Enhanced Meter Management System loaded');
