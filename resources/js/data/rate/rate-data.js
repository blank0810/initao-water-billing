// Enhanced Rate Management System - Water Billing
(function(){
    // Account Types
    const accountTypes = [
        { id: 1, code: 'RES-STD', name: 'Standard Residential', category: 'Residential' },
        { id: 2, code: 'RES-LOW', name: 'Low-Income Residential', category: 'Residential' },
        { id: 3, code: 'RES-SEN', name: 'Senior Citizen', category: 'Residential' },
        { id: 4, code: 'COM-SM', name: 'Small Business', category: 'Commercial' },
        { id: 5, code: 'COM-LG', name: 'Large Commercial', category: 'Commercial' },
        { id: 6, code: 'IND-LT', name: 'Light Industrial', category: 'Industrial' },
        { id: 7, code: 'IND-HV', name: 'Heavy Industrial', category: 'Industrial' },
        { id: 8, code: 'GOV', name: 'Government', category: 'Government' }
    ];

    // Tiered Rate Structures
    const rateTiers = [
        { id: 1, accountTypeId: 1, tier: 1, minCubic: 0, maxCubic: 10, ratePerCubic: 0.01500, description: 'Lifeline Rate' },
        { id: 2, accountTypeId: 1, tier: 2, minCubic: 11, maxCubic: 20, ratePerCubic: 0.02500, description: 'Normal Use' },
        { id: 3, accountTypeId: 1, tier: 3, minCubic: 21, maxCubic: 30, ratePerCubic: 0.03500, description: 'Moderate Use' },
        { id: 4, accountTypeId: 1, tier: 4, minCubic: 31, maxCubic: 999, ratePerCubic: 0.05000, description: 'High Use' },
        { id: 5, accountTypeId: 2, tier: 1, minCubic: 0, maxCubic: 10, ratePerCubic: 0.01000, description: 'Subsidized' },
        { id: 6, accountTypeId: 2, tier: 2, minCubic: 11, maxCubic: 999, ratePerCubic: 0.02000, description: 'Standard' },
        { id: 7, accountTypeId: 4, tier: 1, minCubic: 0, maxCubic: 999, ratePerCubic: 0.04500, description: 'Commercial Flat' },
        { id: 8, accountTypeId: 6, tier: 1, minCubic: 0, maxCubic: 999, ratePerCubic: 0.06000, description: 'Industrial Flat' }
    ];

    // Fixed Charges
    const fixedCharges = [
        { id: 1, name: 'Basic Service Fee', amount: 50.00, frequency: 'monthly', applicableTo: 'all' },
        { id: 2, name: 'Meter Maintenance', amount: 25.00, frequency: 'monthly', applicableTo: 'all' },
        { id: 3, name: 'Environmental Fee', amount: 10.00, frequency: 'monthly', applicableTo: 'all' },
        { id: 4, name: 'System Development Fee', amount: 15.00, frequency: 'monthly', applicableTo: 'commercial,industrial' }
    ];

    // Adjustments
    const adjustmentTypes = [
        { id: 1, name: 'Late Payment Penalty', rate: 0.02, type: 'penalty', direction: '+' },
        { id: 2, name: 'Senior Citizen Discount', rate: 0.05, type: 'discount', direction: '-' },
        { id: 3, name: 'PWD Discount', rate: 0.05, type: 'discount', direction: '-' },
        { id: 4, name: 'Early Payment Discount', rate: 0.03, type: 'discount', direction: '-' },
        { id: 5, name: 'Reconnection Fee', amount: 200.00, type: 'fee', direction: '+' }
    ];

    // Enhanced Consumer Data
    const consumers = [
        { 
            id: 'C-1001', name: 'Gelogo, Norben', address: 'Brgy. San Roque, Main St', 
            meterNo: 'M-1001', accountTypeId: 3, billingPeriod: '2025-01', 
            amountDue: 523.45, status: 'Active', notes: 'Senior citizen discount applied',
            consumption: 25, previousReading: 1000, currentReading: 1025
        },
        { 
            id: 'C-1002', name: 'Sayson, Sarah', address: 'Brgy. Poblacion, Oak Ave', 
            meterNo: 'M-1002', accountTypeId: 1, billingPeriod: '2025-01', 
            amountDue: 398.70, status: 'Active', notes: '',
            consumption: 18, previousReading: 2500, currentReading: 2518
        },
        { 
            id: 'C-1003', name: 'Apora, Jose', address: 'Brgy. Riverside, Pine Rd', 
            meterNo: 'M-1003', accountTypeId: 2, billingPeriod: '2025-01', 
            amountDue: 245.20, status: 'Active', notes: 'Low-income subsidy',
            consumption: 12, previousReading: 800, currentReading: 812
        },
        { 
            id: 'C-1004', name: 'Cruz, Maria', address: 'Brgy. Centro, Market St', 
            meterNo: 'M-1004', accountTypeId: 4, billingPeriod: '2025-01', 
            amountDue: 1250.00, status: 'Active', notes: 'Small business - Sari-sari store',
            consumption: 45, previousReading: 3200, currentReading: 3245
        },
        { 
            id: 'C-1005', name: 'Santos Manufacturing', address: 'Industrial Zone A', 
            meterNo: 'M-1005', accountTypeId: 6, billingPeriod: '2025-01', 
            amountDue: 5680.00, status: 'Active', notes: 'Light industrial facility',
            consumption: 150, previousReading: 15000, currentReading: 15150
        },
        { 
            id: 'C-1006', name: 'Reyes, Pedro', address: 'Brgy. Maharlika, Sunset Blvd', 
            meterNo: 'M-1006', accountTypeId: 1, billingPeriod: '2025-01', 
            amountDue: 856.30, status: 'Overdue', notes: 'Late payment penalty applied',
            consumption: 35, previousReading: 4500, currentReading: 4535
        }
    ];

    // Generate rate history for each consumer
    consumers.forEach((c, ci) => {
        c.rateHistory = [];
        for(let m = 6; m >= 1; m--) {
            const month = `2024-${String(13-m).padStart(2,'0')}`;
            const consumption = Math.round(10 + Math.random() * 40);
            const accountType = accountTypes.find(at => at.id === c.accountTypeId);
            const tiers = rateTiers.filter(rt => rt.accountTypeId === c.accountTypeId);
            
            let rateCharge = 0;
            let remaining = consumption;
            let tierBreakdown = [];
            
            tiers.forEach(tier => {
                if(remaining > 0) {
                    const tierConsumption = Math.min(remaining, tier.maxCubic - tier.minCubic + 1);
                    const tierCharge = tierConsumption * tier.ratePerCubic;
                    rateCharge += tierCharge;
                    tierBreakdown.push({ tier: tier.tier, consumption: tierConsumption, rate: tier.ratePerCubic, charge: tierCharge });
                    remaining -= tierConsumption;
                }
            });
            
            const fixedTotal = fixedCharges.reduce((sum, fc) => sum + fc.amount, 0);
            const penalty = Math.random() > 0.7 ? +(Math.random() * 50).toFixed(2) : 0;
            const discount = c.accountTypeId === 3 ? +(rateCharge * 0.05).toFixed(2) : 0;
            const vat = +((rateCharge + fixedTotal) * 0.12).toFixed(2);
            const totalAmount = +(rateCharge + fixedTotal + vat + penalty - discount).toFixed(2);
            
            c.rateHistory.push({
                month, consumption, rateCharge: +rateCharge.toFixed(2), 
                fixedCharges: +fixedTotal.toFixed(2), penalty, discount, vat, 
                totalAmount, accountType: accountType.name, tierBreakdown
            });
        }
    });

    // State
    let filteredConsumers = [...consumers];
    let currentPage = 1;
    const rowsPerPage = 10;
    let selectedAccountType = 'all';

    // DOM refs
    let mainTbody, searchEl, paginationInfo, tableSection, detailsSection;

    function resolveDOM() {
        mainTbody = document.getElementById('consumerMainTable');
        searchEl = document.getElementById('searchInput');
        paginationInfo = document.getElementById('paginationInfo');
        tableSection = document.getElementById('tableSection');
        detailsSection = document.getElementById('rateDetailsSection');
    }

    function computeMainAggregates() {
        const totalConsumers = filteredConsumers.length;
        const totalConsumption = filteredConsumers.reduce((sum, c) => sum + (c.consumption || 0), 0);
        const totalRateCharge = filteredConsumers.reduce((sum, c) => sum + c.amountDue, 0);
        const totalPenalty = filteredConsumers.reduce((sum, c) => {
            const latestHistory = c.rateHistory[c.rateHistory.length - 1];
            return sum + (latestHistory?.penalty || 0);
        }, 0);
        return { totalConsumers, totalConsumption, totalRateCharge, totalPenalty };
    }

    function populateMainCards() {
        const cards = computeMainAggregates();
        const elTotalConsumers = document.getElementById('cardTotalConsumers');
        const elTotalConsumption = document.getElementById('cardTotalConsumption');
        const elTotalRateCharge = document.getElementById('cardTotalRateCharge');
        const elTotalPenaltyMain = document.getElementById('cardTotalPenaltyMain');
        if(elTotalConsumers) elTotalConsumers.textContent = cards.totalConsumers;
        if(elTotalConsumption) elTotalConsumption.textContent = cards.totalConsumption + ' m³';
        if(elTotalRateCharge) elTotalRateCharge.textContent = '₱ ' + cards.totalRateCharge.toFixed(2);
        if(elTotalPenaltyMain) elTotalPenaltyMain.textContent = '₱ ' + cards.totalPenalty.toFixed(2);
    }

    function getAccountTypeName(accountTypeId) {
        const at = accountTypes.find(a => a.id === accountTypeId);
        return at ? at.name : '-';
    }

    function renderConsumerMainTable() {
        if(!mainTbody) return;
        mainTbody.innerHTML = '';

        const start = (currentPage - 1) * rowsPerPage;
        const pageItems = filteredConsumers.slice(start, start + rowsPerPage);

        if(pageItems.length === 0) {
            mainTbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No consumers found</td></tr>';
        } else {
            pageItems.forEach(c => {
                const statusColor = c.status === 'Active' ? 'text-green-600' : 'text-red-600';
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${c.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 font-mono">${c.id}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 font-mono">${c.meterNo}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${c.billingPeriod}</td>
                    <td class="px-6 py-4 text-sm font-semibold ${statusColor}">₱ ${c.amountDue.toFixed(2)}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${getAccountTypeName(c.accountTypeId)}</td>
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

    function filterByAccountType(typeId) {
        selectedAccountType = typeId;
        if(typeId === 'all') {
            filteredConsumers = [...consumers];
        } else {
            filteredConsumers = consumers.filter(c => c.accountTypeId === parseInt(typeId));
        }
        currentPage = 1;
        populateMainCards();
        renderConsumerMainTable();
    }

    function selectConsumer(consumerId) {
        resolveDOM();
        const consumer = consumers.find(c => c.id === consumerId);
        if(!consumer) return;

        const rateWrapper = document.getElementById('rateSummaryWrapper');
        const searchFilter = document.getElementById('searchFilterSection');
        if(rateWrapper) rateWrapper.classList.add('hidden');
        if(searchFilter) searchFilter.classList.add('hidden');
        if(tableSection) tableSection.classList.add('hidden');
        if(detailsSection) detailsSection.classList.remove('hidden');

        // Populate consumer profile
        document.getElementById('consumer_name').textContent = consumer.name;
        document.getElementById('consumer_id').textContent = consumer.id;
        document.getElementById('consumer_address').textContent = consumer.address;
        document.getElementById('consumer_meter_no').textContent = consumer.meterNo;
        document.getElementById('consumer_billing_period').textContent = consumer.billingPeriod;
        document.getElementById('consumer_amount_due').textContent = '₱ ' + consumer.amountDue.toFixed(2);
        document.getElementById('consumer_account_type').textContent = getAccountTypeName(consumer.accountTypeId);
        document.getElementById('consumer_status').textContent = consumer.status;
        document.getElementById('consumer_consumption').textContent = consumer.consumption + ' m³';
        document.getElementById('consumer_prev_reading').textContent = consumer.previousReading;
        document.getElementById('consumer_curr_reading').textContent = consumer.currentReading;

        // Compute aggregates
        const latestHistory = consumer.rateHistory[consumer.rateHistory.length - 1];
        const totalPenalty = consumer.rateHistory.reduce((s, x) => s + (x.penalty || 0), 0);
        const totalRateCharge = consumer.rateHistory.reduce((s, x) => s + (x.rateCharge || 0), 0);
        const totalConsumption = consumer.rateHistory.reduce((s, x) => s + (x.consumption || 0), 0);
        const totalDiscount = consumer.rateHistory.reduce((s, x) => s + (x.discount || 0), 0);
        const totalVAT = consumer.rateHistory.reduce((s, x) => s + (x.vat || 0), 0);
        const totalFixed = consumer.rateHistory.reduce((s, x) => s + (x.fixedCharges || 0), 0);

        document.getElementById('cardTotalPenalty').textContent = '₱ ' + totalPenalty.toFixed(2);
        document.getElementById('cardRateCharge').textContent = '₱ ' + totalRateCharge.toFixed(2);
        document.getElementById('cardConsumption').textContent = totalConsumption + ' m³';
        document.getElementById('cardDiscount').textContent = '₱ ' + totalDiscount.toFixed(2);
        document.getElementById('cardVAT').textContent = '₱ ' + totalVAT.toFixed(2);
        document.getElementById('cardFixedCharges').textContent = '₱ ' + totalFixed.toFixed(2);

        renderConsumerRateTable(consumer.rateHistory);
        renderRateChartLine(consumer.rateHistory);
        renderTierBreakdown(latestHistory?.tierBreakdown || []);
    }

    function renderConsumerRateTable(rows) {
        const t = document.getElementById('consumerRateTable');
        if(!t) return;
        t.innerHTML = '';
        if(!rows || rows.length === 0) {
            t.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No history found</td></tr>';
            return;
        }
        rows.forEach(r => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${r.month}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${r.consumption} m³</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">₱ ${r.rateCharge.toFixed(2)}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">₱ ${r.fixedCharges.toFixed(2)}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">₱ ${r.vat.toFixed(2)}</td>
                <td class="px-4 py-3 text-sm text-red-600">${r.penalty > 0 ? '₱ ' + r.penalty.toFixed(2) : '-'}</td>
                <td class="px-4 py-3 text-sm text-green-600">${r.discount > 0 ? '₱ ' + r.discount.toFixed(2) : '-'}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">₱ ${r.totalAmount.toFixed(2)}</td>
            `;
            t.appendChild(tr);
        });
    }

    function renderTierBreakdown(tierBreakdown) {
        const container = document.getElementById('tierBreakdownTable');
        if(!container) return;
        container.innerHTML = '';
        if(!tierBreakdown || tierBreakdown.length === 0) {
            container.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No tier data</td></tr>';
            return;
        }
        tierBreakdown.forEach(tb => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Tier ${tb.tier}</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${tb.consumption} m³</td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">₱ ${tb.rate.toFixed(5)}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">₱ ${tb.charge.toFixed(2)}</td>
            `;
            container.appendChild(tr);
        });
    }

    function renderRateChartLine(rows) {
        const container = document.getElementById('rateChart');
        if(!container) return;
        container.innerHTML = '';
        if(!rows || rows.length === 0) {
            container.innerHTML = '<div class="text-sm text-gray-500">No chart data</div>';
            return;
        }

        const series = {
            'Total Amount': { key: 'totalAmount', color: '#4f46e5', values: [] },
            'Rate Charge': { key: 'rateCharge', color: '#10b981', values: [] },
            'Consumption': { key: 'consumption', color: '#06b6d4', values: [] }
        };

        rows.forEach(r => {
            Object.values(series).forEach(s => s.values.push(Number(r[s.key] || 0)));
        });

        const width = container.clientWidth || 800;
        const height = 220;
        const padding = 40;
        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
        svg.classList.add('w-full');

        const allValues = [].concat(...Object.values(series).map(s => s.values));
        const maxVal = Math.max(...allValues, 1);
        const pointsCount = rows.length;
        const xStep = (width - padding * 2) / Math.max(1, pointsCount - 1);

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

        // Series
        Object.keys(series).forEach(label => {
            const s = series[label];
            const points = s.values.map((v, i) => {
                const x = padding + i * xStep;
                const y = (height - padding) - ((v / maxVal) * (height - padding * 2));
                return `${x},${y}`;
            }).join(' ');

            const poly = document.createElementNS(svgNS, 'polyline');
            poly.setAttribute('points', points);
            poly.setAttribute('fill', 'none');
            poly.setAttribute('stroke', s.color);
            poly.setAttribute('stroke-width', '2');
            svg.appendChild(poly);

            s.values.forEach((v, i) => {
                const x = padding + i * xStep;
                const y = (height - padding) - ((v / maxVal) * (height - padding * 2));
                const circle = document.createElementNS(svgNS, 'circle');
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
                circle.setAttribute('r', '3');
                circle.setAttribute('fill', s.color);
                svg.appendChild(circle);
            });
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
                (c.meterNo || '').toLowerCase().includes(q) ||
                getAccountTypeName(c.accountTypeId).toLowerCase().includes(q)
            ));
            if(selectedAccountType !== 'all') {
                filteredConsumers = filteredConsumers.filter(c => c.accountTypeId === parseInt(selectedAccountType));
            }
            currentPage = 1;
            populateMainCards();
            renderConsumerMainTable();
        });
    }

    function init() {
        resolveDOM();
        populateMainCards();
        setupSearch();
        renderConsumerMainTable();
        populateAccountTypeFilter();
    }

    function populateAccountTypeFilter() {
        const dropdown = document.getElementById('accountTypeFilterDropdown');
        if(!dropdown) return;
        
        dropdown.innerHTML = '<option value="all">All Account Types</option>';
        
        const categories = [...new Set(accountTypes.map(at => at.category))];
        categories.forEach(cat => {
            const optgroup = document.createElement('optgroup');
            optgroup.label = cat;
            const types = accountTypes.filter(at => at.category === cat);
            types.forEach(at => {
                const opt = document.createElement('option');
                opt.value = at.id;
                opt.textContent = at.name;
                optgroup.appendChild(opt);
            });
            dropdown.appendChild(optgroup);
        });
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
    window.filterByAccountType = filterByAccountType;
    window._rateModule = { accountTypes, rateTiers, fixedCharges, adjustmentTypes, consumers };
})();

console.log('Enhanced Rate Management System loaded');
